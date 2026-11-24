<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Validation\ValidationException;

class Loan extends Model
{
    public const LOAN_DAYS = 7;

    public const MAX_EXTENSIONS = 2;

    public const FINE_PER_DAY = 1000;

    public const MAX_ACTIVE_BOOKS = 3;

    public const STATUS_ACTIVE = 'dipinjam';

    public const STATUS_COMPLETE = 'selesai';

    protected $fillable = [
        'user_id',
        'librarian_id',
        'loan_date',
        'due_date',
        'extend_count',
        'status',
    ];

    protected function casts(): array
    {
        return [
            'loan_date' => 'date',
            'due_date' => 'date',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function librarian(): BelongsTo
    {
        return $this->belongsTo(User::class, 'librarian_id');
    }

    public function items(): HasMany
    {
        return $this->hasMany(LoanItem::class);
    }

    public function fine(): HasOne
    {
        return $this->hasOne(Fine::class);
    }

    public function extend(): void
    {
        if ($this->status !== self::STATUS_ACTIVE || $this->items()->whereNull('returned_at')->doesntExist()) {
            throw ValidationException::withMessages([
                'loan' => 'Peminjaman ini sudah selesai dan tidak dapat diperpanjang.',
            ]);
        }

        if ($this->extend_count >= self::MAX_EXTENSIONS) {
            throw ValidationException::withMessages([
                'loan' => 'Batas perpanjangan sudah tercapai.',
            ]);
        }

        if (today()->greaterThan($this->due_date)) {
            throw ValidationException::withMessages([
                'loan' => 'Peminjaman yang sudah terlambat tidak dapat diperpanjang.',
            ]);
        }

        $this->forceFill([
            'due_date' => Carbon::parse($this->due_date)->addDays(self::LOAN_DAYS),
            'extend_count' => $this->extend_count + 1,
        ])->save();
    }

    public function syncFine(): void
    {
        $daysLate = $this->items->sum(function (LoanItem $item): int {
            $date = $item->returned_at ?? today();
            $dueDate = Carbon::parse($this->due_date)->startOfDay();
            $referenceDate = Carbon::parse($date)->startOfDay();

            return $referenceDate->greaterThan($dueDate)
                ? (int) $dueDate->diffInDays($referenceDate)
                : 0;
        });

        if ($daysLate === 0) {
            return;
        }

        $fine = $this->fine()->firstOrNew();
        $fine->days_late = $daysLate;
        $fine->amount = $daysLate * self::FINE_PER_DAY;
        $fine->save();
    }

    public function updateStatus(): void
    {
        if ($this->items()->whereNull('returned_at')->doesntExist()) {
            $this->update(['status' => self::STATUS_COMPLETE]);
        }
    }
}
