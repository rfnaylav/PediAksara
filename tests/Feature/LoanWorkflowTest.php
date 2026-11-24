<?php

namespace Tests\Feature;

use App\Models\Author;
use App\Models\Book;
use App\Models\Category;
use App\Models\Fine;
use App\Models\Loan;
use App\Models\Publisher;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class LoanWorkflowTest extends TestCase
{
    use RefreshDatabase;

    public function test_librarian_can_create_return_and_settle_a_loan_with_fine_and_stock_updates(): void
    {
        $librarian = User::factory()->create(['role' => 'pustakawan']);
        $member = User::factory()->create(['role' => 'anggota']);
        $books = collect([$this->createBook(), $this->createBook()]);
        $this->actingAs($librarian);

        $this->post(route('loans.store'), [
            'user_id' => $member->id,
            'book_ids' => $books->pluck('id')->all(),
        ])->assertRedirect();

        $loan = Loan::query()->with('items')->firstOrFail();
        $this->assertSame(now()->addDays(Loan::LOAN_DAYS)->toDateString(), $loan->due_date->toDateString());
        foreach ($books as $book) {
            $this->assertDatabaseHas('books', ['id' => $book->id, 'stock' => 1]);
        }

        $this->travelTo(now()->startOfDay()->addDays(Loan::LOAN_DAYS + 3));
        $firstItem = $loan->items->first();
        $this->post(route('loans.items.return', [$loan, $firstItem]))->assertRedirect();
        $this->assertDatabaseHas('fines', ['loan_id' => $loan->id, 'days_late' => 6, 'amount' => 6000, 'status' => Fine::STATUS_UNPAID]);
        $this->post(route('fines.pay', $loan->fine))->assertSessionHasErrors('fine');

        $this->post(route('loans.items.return', [$loan, $loan->items->last()]))->assertRedirect();
        $this->assertDatabaseHas('loans', ['id' => $loan->id, 'status' => Loan::STATUS_COMPLETE]);
        $this->assertDatabaseHas('fines', ['loan_id' => $loan->id, 'days_late' => 6, 'amount' => 6000]);
        $this->post(route('fines.pay', $loan->fresh()->fine))->assertRedirect();
        $this->assertDatabaseHas('fines', ['loan_id' => $loan->id, 'status' => Fine::STATUS_PAID]);
        foreach ($books as $book) {
            $this->assertDatabaseHas('books', ['id' => $book->id, 'stock' => 2]);
        }
    }

    public function test_member_with_unpaid_fine_cannot_borrow_and_member_cannot_open_another_members_loan(): void
    {
        $librarian = User::factory()->create(['role' => 'pustakawan']);
        $member = User::factory()->create(['role' => 'anggota']);
        $otherMember = User::factory()->create(['role' => 'anggota']);
        $book = $this->createBook();
        $loan = Loan::create([
            'user_id' => $otherMember->id,
            'librarian_id' => $librarian->id,
            'loan_date' => today(),
            'due_date' => today()->addDays(Loan::LOAN_DAYS),
            'status' => Loan::STATUS_ACTIVE,
        ]);
        $loan->items()->create(['book_id' => $book->id]);
        Fine::create([
            'loan_id' => $loan->id,
            'days_late' => 1,
            'amount' => Loan::FINE_PER_DAY,
            'status' => Fine::STATUS_UNPAID,
        ]);

        $this->actingAs($librarian)->post(route('loans.store'), [
            'user_id' => $otherMember->id,
            'book_ids' => [$this->createBook()->id],
        ])->assertSessionHasErrors('user_id');

        $this->actingAs($member)->get(route('member.loans.show', $loan))->assertNotFound();
    }

    public function test_member_can_register_but_cannot_access_administrator_routes(): void
    {
        $this->post(route('register'), [
            'name' => 'Anggota Baru',
            'email' => 'anggota@example.test',
            'password' => 'StrongPassword123!',
            'password_confirmation' => 'StrongPassword123!',
        ])->assertRedirect(route('catalog.index'));

        $this->assertDatabaseHas('users', [
            'email' => 'anggota@example.test',
            'role' => 'anggota',
        ]);
        $this->assertNotNull(User::query()->where('email', 'anggota@example.test')->value('member_code'));

        $this->get(route('books.index'))->assertForbidden();
    }

    public function test_loan_extension_is_limited_and_overdue_fine_is_synced_by_command(): void
    {
        $librarian = User::factory()->create(['role' => 'pustakawan']);
        $member = User::factory()->create(['role' => 'anggota']);
        $book = $this->createBook();
        $loan = Loan::create([
            'user_id' => $member->id,
            'librarian_id' => $librarian->id,
            'loan_date' => today(),
            'due_date' => today()->addDays(Loan::LOAN_DAYS),
            'status' => Loan::STATUS_ACTIVE,
        ]);
        $loan->items()->create(['book_id' => $book->id]);
        $this->actingAs($librarian);

        $this->post(route('loans.extend', $loan))->assertRedirect();
        $this->post(route('loans.extend', $loan))->assertRedirect();
        $this->post(route('loans.extend', $loan))->assertSessionHasErrors('loan');
        $this->assertSame(Loan::MAX_EXTENSIONS, $loan->fresh()->extend_count);

        $this->travelTo($loan->fresh()->due_date->copy()->addDay());
        $this->artisan('loans:sync-fines')->assertSuccessful();
        $this->assertDatabaseHas('fines', [
            'loan_id' => $loan->id,
            'days_late' => 1,
            'amount' => Loan::FINE_PER_DAY,
        ]);
    }

    public function test_fines_cover_one_day_and_more_than_seven_days_late(): void
    {
        $member = User::factory()->create(['role' => 'anggota']);
        $librarian = User::factory()->create(['role' => 'pustakawan']);

        foreach ([1, 8] as $daysLate) {
            $loan = Loan::create([
                'user_id' => $member->id,
                'librarian_id' => $librarian->id,
                'loan_date' => today()->subDays($daysLate + Loan::LOAN_DAYS),
                'due_date' => today()->subDays($daysLate),
                'status' => Loan::STATUS_ACTIVE,
            ]);
            $loan->items()->create(['book_id' => $this->createBook()->id]);
            $loan->syncFine();

            $this->assertDatabaseHas('fines', [
                'loan_id' => $loan->id,
                'days_late' => $daysLate,
                'amount' => $daysLate * Loan::FINE_PER_DAY,
            ]);
        }
    }

    private function createBook(): Book
    {
        $category = Category::query()->firstOrCreate(['name' => 'Umum']);
        $author = Author::query()->firstOrCreate(['name' => 'Penulis Uji']);
        $publisher = Publisher::query()->firstOrCreate(['name' => 'Penerbit Uji']);

        return Book::create([
            'category_id' => $category->id,
            'author_id' => $author->id,
            'publisher_id' => $publisher->id,
            'title' => 'Buku Uji '.fake()->unique()->numerify('###'),
            'stock' => 2,
        ]);
    }
}
