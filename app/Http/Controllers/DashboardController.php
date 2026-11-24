<?php

namespace App\Http\Controllers;

use App\Models\Book;
use App\Models\Fine;
use App\Models\Loan;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function __invoke(): View
    {
        return view('dashboard', [
            'bookCount' => Book::query()->count(),
            'availableCopies' => Book::query()->sum('stock'),
            'activeLoans' => Loan::query()->where('status', Loan::STATUS_ACTIVE)->count(),
            'overdueLoans' => Loan::query()->where('status', Loan::STATUS_ACTIVE)->whereDate('due_date', '<', today())->count(),
            'unpaidFines' => Fine::query()->where('status', Fine::STATUS_UNPAID)->sum('amount'),
            'recentLoans' => Loan::query()->with(['user', 'items.book'])->latest('loan_date')->limit(8)->get(),
        ]);
    }
}
