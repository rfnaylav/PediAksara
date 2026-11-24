<?php

namespace App\Console\Commands;

use App\Models\Loan;
use Illuminate\Console\Command;

class SyncOverdueFines extends Command
{
    protected $signature = 'loans:sync-fines';

    protected $description = 'Create or update fines for overdue active loans';

    public function handle(): int
    {
        $count = 0;

        Loan::query()
            ->where('status', Loan::STATUS_ACTIVE)
            ->whereDate('due_date', '<', today())
            ->with(['items', 'fine'])
            ->chunkById(100, function ($loans) use (&$count): void {
                foreach ($loans as $loan) {
                    $loan->syncFine();
                    $count++;
                }
            });

        $this->info("$count overdue loan(s) synchronized.");

        return self::SUCCESS;
    }
}
