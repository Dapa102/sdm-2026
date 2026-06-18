<?php

namespace App\Console\Commands;

use App\Models\MeritTransaction;
use Illuminate\Console\Command;

class ExpireMeritPoints extends Command
{
    protected $signature = 'merit:expire';

    protected $description = 'Mark expired merit points as expired';

    public function handle(): int
    {
        $this->info('Starting merit point expiry check...');

        $expiredCount = MeritTransaction::where('is_expired', false)
            ->where('expiry_date', '<', now())
            ->whereNotNull('expiry_date')
            ->update(['is_expired' => true]);

        if ($expiredCount > 0) {
            $this->info("✓ Marked {$expiredCount} merit transactions as expired.");
        } else {
            $this->info('✓ No expired merit points found.');
        }

        return self::SUCCESS;
    }
}
