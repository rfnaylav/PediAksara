<?php

namespace App\Http\Controllers;

use App\Models\Book;
use App\Models\Fine;
use App\Models\Loan;
use App\Models\LoanItem;
use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class LoanController extends Controller
{
    public function index(): View
    {
        return view('loans.index', [
            'loans' => Loan::query()
                ->with(['user', 'librarian', 'items.book', 'fine'])
                ->latest('loan_date')
                ->paginate(15),
            'title' => 'Transaksi peminjaman',
            'memberView' => false,
        ]);
    }

    public function memberIndex(Request $request): View
    {
        return view('loans.index', [
            'loans' => Loan::query()
                ->where('user_id', $request->user()->id)
                ->with(['user', 'items.book', 'fine'])
                ->latest('loan_date')
                ->paginate(15),
            'title' => 'Riwayat peminjaman saya',
            'memberView' => true,
        ]);
    }

    public function create(): View
    {
        return view('loans.create', [
            'members' => User::query()->where('role', 'anggota')->orderBy('name')->get(),
            'books' => Book::query()->where('stock', '>', 0)->orderBy('title')->get(),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'user_id' => ['required', 'integer', 'exists:users,id'],
            'book_ids' => ['required', 'array', 'min:1', 'max:'.Loan::MAX_ACTIVE_BOOKS],
            'book_ids.*' => ['required', 'integer', 'distinct', 'exists:books,id'],
        ]);

        $loan = DB::transaction(function () use ($validated): Loan {
            $member = User::query()
                ->where('role', 'anggota')
                ->lockForUpdate()
                ->findOrFail($validated['user_id']);

            if ($member->loans()->whereHas('fine', fn (Builder $query) => $query->where('status', Fine::STATUS_UNPAID))->exists()) {
                throw ValidationException::withMessages([
                    'user_id' => 'Anggota memiliki denda yang belum lunas.',
                ]);
            }

            $activeCount = LoanItem::query()
                ->whereNull('returned_at')
                ->whereHas('loan', fn (Builder $query) => $query->where('user_id', $member->id))
                ->count();
            if ($activeCount + count($validated['book_ids']) > Loan::MAX_ACTIVE_BOOKS) {
                throw ValidationException::withMessages([
                    'book_ids' => 'Anggota hanya boleh memiliki maksimal '.Loan::MAX_ACTIVE_BOOKS.' buku aktif.',
                ]);
            }

            $books = Book::query()
                ->whereIn('id', $validated['book_ids'])
                ->orderBy('id')
                ->lockForUpdate()
                ->get();
            if ($books->count() !== count($validated['book_ids']) || $books->contains(fn (Book $book) => $book->stock < 1)) {
                throw ValidationException::withMessages([
                    'book_ids' => 'Salah satu buku sudah tidak tersedia.',
                ]);
            }

            $loan = Loan::create([
                'user_id' => $member->id,
                'librarian_id' => Auth::id(),
                'loan_date' => today(),
                'due_date' => today()->addDays(Loan::LOAN_DAYS),
                'status' => Loan::STATUS_ACTIVE,
            ]);

            foreach ($books as $book) {
                $loan->items()->create(['book_id' => $book->id]);
                $book->decrement('stock');
            }

            return $loan;
        });

        return redirect()->route('loans.show', $loan)->with('status', 'Peminjaman berhasil dicatat.');
    }

    public function show(Loan $loan): View
    {
        $loan->load(['user', 'librarian', 'items.book', 'fine']);

        return view('loans.show', ['loan' => $loan]);
    }

    public function memberShow(Request $request, Loan $loan): View
    {
        abort_unless($loan->user_id === $request->user()->id, 404);

        return $this->show($loan);
    }

    public function extend(Loan $loan): RedirectResponse
    {
        DB::transaction(function () use ($loan): void {
            $lockedLoan = Loan::query()->lockForUpdate()->findOrFail($loan->id);
            $lockedLoan->extend();
        });

        return back()->with('status', 'Jatuh tempo berhasil diperpanjang 7 hari.');
    }

    public function returnItem(Loan $loan, LoanItem $item): RedirectResponse
    {
        DB::transaction(function () use ($loan, $item): void {
            $lockedLoan = Loan::query()->lockForUpdate()->findOrFail($loan->id);
            $lockedItem = $lockedLoan->items()->lockForUpdate()->findOrFail($item->id);

            if ($lockedItem->returned_at !== null) {
                throw ValidationException::withMessages([
                    'item' => 'Buku ini sudah dikembalikan.',
                ]);
            }

            $book = Book::query()->lockForUpdate()->findOrFail($lockedItem->book_id);
            $lockedItem->update(['returned_at' => today()]);
            $book->increment('stock');

            $lockedLoan->refresh();
            $lockedLoan->syncFine();
            $lockedLoan->updateStatus();
        });

        return back()->with('status', 'Pengembalian berhasil dicatat.');
    }

    public function overdue(Request $request): View
    {
        $query = Loan::query()
            ->where('status', Loan::STATUS_ACTIVE)
            ->whereDate('due_date', '<', today())
            ->with(['user', 'items.book', 'fine']);

        if ($request->user()->hasRole('anggota')) {
            $query->where('user_id', $request->user()->id);
        }

        return view('loans.overdue', [
            'loans' => $query->orderBy('due_date')->paginate(15),
        ]);
    }
}
