<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Fine extends Model
{
    public const STATUS_UNPAID = 'belum';

    public const STATUS_PAID = 'lunas';

    protected $fillable = [
        'loan_id',
        'days_late',
        'amount',
        'status',
        'paid_at',
    ];

    protected function casts(): array
    {
        return [
            'paid_at' => 'datetime',
        ];
    }

    public function loan(): BelongsTo
    {
        return $this->belongsTo(Loan::class);
    }

    public function markAsPaid(): void
    {
        if ($this->status === self::STATUS_PAID) {
            return;
        }

        $this->update([
            'status' => self::STATUS_PAID,
            'paid_at' => now(),
        ]);
    }
}
