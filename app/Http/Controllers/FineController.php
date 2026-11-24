<?php

namespace App\Http\Controllers;

use App\Models\Fine;
use App\Models\Loan;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class FineController extends Controller
{
    public function index(): View
    {
        return view('fines.index', [
            'fines' => Fine::query()
                ->with(['loan.user', 'loan.items.book'])
                ->latest()
                ->paginate(15),
        ]);
    }

    public function pay(Fine $fine): RedirectResponse
    {
        DB::transaction(function () use ($fine): void {
            $lockedFine = Fine::query()->lockForUpdate()->findOrFail($fine->id);
            $loan = Loan::query()->findOrFail($lockedFine->loan_id);

            if ($loan->status !== Loan::STATUS_COMPLETE) {
                throw ValidationException::withMessages([
                    'fine' => 'Denda baru dapat dibayar setelah semua buku dikembalikan.',
                ]);
            }

            $lockedFine->markAsPaid();
        });

        return back()->with('status', 'Pembayaran denda berhasil dicatat.');
    }
}
