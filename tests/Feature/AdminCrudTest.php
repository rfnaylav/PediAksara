<?php

namespace Tests\Feature;

use App\Models\Author;
use App\Models\Book;
use App\Models\Category;
use App\Models\Publisher;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Http\UploadedFile;
use Tests\TestCase;

class AdminCrudTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_create_catalog_records_and_staff_accounts(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $this->actingAs($admin);

        $this->post(route('categories.store'), ['name' => 'Sains'])->assertRedirect(route('categories.index'));
        $this->post(route('authors.store'), ['name' => 'Penulis A'])->assertRedirect(route('authors.index'));
        $this->post(route('publishers.store'), ['name' => 'Penerbit A'])->assertRedirect(route('publishers.index'));

        $category = Category::query()->where('name', 'Sains')->firstOrFail();
        $author = Author::query()->where('name', 'Penulis A')->firstOrFail();
        $publisher = Publisher::query()->where('name', 'Penerbit A')->firstOrFail();

        $this->post(route('books.store'), [
            'category_id' => $category->id,
            'author_id' => $author->id,
            'publisher_id' => $publisher->id,
            'title' => 'Belajar Sains',
            'isbn' => '9780000000001',
            'year' => 2022,
            'stock' => 4,
        ])->assertRedirect(route('books.index'));
        $this->assertDatabaseHas('books', ['title' => 'Belajar Sains', 'stock' => 4]);

        $book = Book::query()->where('isbn', '9780000000001')->firstOrFail();
        $this->put(route('books.update', $book), [
            'category_id' => $category->id,
            'author_id' => $author->id,
            'publisher_id' => $publisher->id,
            'title' => 'Belajar Sains Edisi Baru',
            'isbn' => $book->isbn,
            'year' => 2023,
            'stock' => 0,
        ])->assertRedirect(route('books.index'));
        $this->assertDatabaseHas('books', ['id' => $book->id, 'title' => 'Belajar Sains Edisi Baru', 'stock' => 0]);

        $this->post(route('users.store'), [
            'name' => 'Pustakawan Baru',
            'email' => 'pustakawan@example.test',
            'role' => 'pustakawan',
            'password' => 'StrongPassword123!',
            'password_confirmation' => 'StrongPassword123!',
        ])->assertRedirect(route('users.index'));
        $this->assertDatabaseHas('users', [
            'email' => 'pustakawan@example.test',
            'role' => 'pustakawan',
            'member_code' => null,
        ]);
    }

    public function test_admin_forms_validate_unique_isbn_and_catalog_is_searchable(): void
    {
        $this->actingAs(User::factory()->create(['role' => 'admin']));
        $category = Category::create(['name' => 'Sejarah']);
        $author = Author::create(['name' => 'Penulis B']);
        $publisher = Publisher::create(['name' => 'Penerbit B']);
        $attributes = [
            'category_id' => $category->id,
            'author_id' => $author->id,
            'publisher_id' => $publisher->id,
            'title' => 'Sejarah Nusantara',
            'isbn' => '9780000000002',
            'year' => 2020,
            'stock' => 1,
        ];
        $book = Book::create($attributes);
        Book::create(array_merge($attributes, [
            'title' => 'Koleksi Habis',
            'isbn' => '9780000000003',
            'stock' => 0,
        ]));

        $this->post(route('books.store'), $attributes)->assertSessionHasErrors('isbn');
        $this->get(route('catalog.index', ['q' => 'Nusantara']))->assertOk()->assertSee('Sejarah Nusantara');
        $this->get(route('catalog.index', ['q' => '9780000000002']))->assertOk()->assertSee('Sejarah Nusantara');
        $this->get(route('catalog.index', [
            'category_id' => $category->id,
            'author_id' => $author->id,
            'available' => 1,
        ]))->assertOk()->assertSee('Sejarah Nusantara')->assertDontSee('Koleksi Habis');
        $this->get(route('catalog.show', $book))
            ->assertOk()
            ->assertSee('9780000000002')
            ->assertSee('Penulis B')
            ->assertSee('Penerbit B');
        $this->get(route('catalog.index', ['q' => 'tidak-ada']))
            ->assertOk()
            ->assertSee('Tidak ada buku yang cocok');
    }

    public function test_librarian_cannot_manage_catalog(): void
    {
        $this->actingAs(User::factory()->create(['role' => 'pustakawan']))
            ->get(route('books.index'))
            ->assertForbidden();
    }

    public function test_user_can_change_password_after_entering_the_current_password(): void
    {
        $user = User::factory()->create([
            'role' => 'admin',
            'password' => 'CurrentPassword123!',
        ]);
        $this->actingAs($user);

        $this->put(route('account.password.update'), [
            'current_password' => 'CurrentPassword123!',
            'password' => 'NewPassword123!',
            'password_confirmation' => 'NewPassword123!',
        ])->assertRedirect();

        $this->assertTrue(Hash::check('NewPassword123!', $user->fresh()->password));

        $this->put(route('account.password.update'), [
            'current_password' => 'wrong-password',
            'password' => 'AnotherPassword123!',
            'password_confirmation' => 'AnotherPassword123!',
        ])->assertSessionHasErrors('current_password');
    }

    public function test_admin_can_upload_and_replace_a_book_cover_used_by_the_catalog(): void
    {
        Storage::fake('public');
        $this->actingAs(User::factory()->create(['role' => 'admin']));
        $category = Category::create(['name' => 'Sastra']);
        $author = Author::create(['name' => 'Penulis Sampul']);
        $publisher = Publisher::create(['name' => 'Penerbit Sampul']);
        $bookData = [
            'category_id' => $category->id,
            'author_id' => $author->id,
            'publisher_id' => $publisher->id,
            'title' => 'Buku Bersampul',
            'isbn' => '9780000000004',
            'year' => 2020,
            'stock' => 2,
        ];

        $this->post(route('books.store'), $bookData + [
            'cover' => UploadedFile::fake()->createWithContent('not-an-image.txt', 'not an image'),
        ])->assertSessionHasErrors('cover');
        $this->assertDatabaseMissing('books', ['isbn' => $bookData['isbn']]);

        $this->post(route('books.store'), $bookData + [
            'cover' => $this->fakeCoverImage('cover.png'),
        ])->assertRedirect(route('books.index'));

        $book = Book::query()->where('isbn', $bookData['isbn'])->firstOrFail();
        $firstCover = $book->cover_image;
        Storage::disk('public')->assertExists($firstCover);
        $this->get(route('catalog.index'))->assertSee(asset('storage/'.$firstCover), false);
        $this->get(route('catalog.show', $book))->assertSee(asset('storage/'.$firstCover), false);

        $this->put(route('books.update', $book), $bookData + [
            'cover' => $this->fakeCoverImage('replacement.png'),
        ])->assertRedirect(route('books.index'));

        $secondCover = $book->fresh()->cover_image;
        $this->assertNotSame($firstCover, $secondCover);
        Storage::disk('public')->assertMissing($firstCover);
        Storage::disk('public')->assertExists($secondCover);

        $this->delete(route('books.destroy', $book->fresh()))->assertRedirect(route('books.index'));
        $this->assertDatabaseMissing('books', ['id' => $book->id]);
        Storage::disk('public')->assertMissing($secondCover);
    }

    private function fakeCoverImage(string $filename): UploadedFile
    {
        $png = base64_decode('iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAQAAAC1HAwCAAAAC0lEQVR42mP8/x8AAwMCAO+jf5kAAAAASUVORK5CYII=');

        return UploadedFile::fake()->createWithContent($filename, $png);
    }
}
