<?php

namespace App\Http\Controllers;

use App\Models\Fine;
use App\Models\Loan;
use App\Models\LoanItem;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ReportController extends Controller
{
    public function index(Request $request): View
    {
        $filters = $request->validate([
            'from' => ['nullable', 'date'],
            'to' => ['nullable', 'date', 'after_or_equal:from'],
        ]);

        $loans = Loan::query()
            ->with(['user', 'librarian', 'items.book', 'fine'])
            ->when($filters['from'] ?? null, fn ($query, $date) => $query->whereDate('loan_date', '>=', $date))
            ->when($filters['to'] ?? null, fn ($query, $date) => $query->whereDate('loan_date', '<=', $date))
            ->latest('loan_date');

        $fines = Fine::query()
            ->with('loan.user')
            ->when($filters['from'] ?? null, fn ($query, $date) => $query->whereDate('created_at', '>=', $date))
            ->when($filters['to'] ?? null, fn ($query, $date) => $query->whereDate('created_at', '<=', $date));

        $returns = LoanItem::query()
            ->whereNotNull('returned_at')
            ->with(['loan.user', 'book'])
            ->when($filters['from'] ?? null, fn ($query, $date) => $query->whereDate('returned_at', '>=', $date))
            ->when($filters['to'] ?? null, fn ($query, $date) => $query->whereDate('returned_at', '<=', $date))
            ->latest('returned_at');

        return view('reports.index', [
            'loans' => $loans->paginate(20)->withQueryString(),
            'returns' => $returns->paginate(20)->withQueryString(),
            'fineCount' => (clone $fines)->count(),
            'fineTotal' => (clone $fines)->sum('amount'),
            'filters' => $filters,
        ]);
    }
}
