<?php

namespace App\Console\Commands;

use App\Repositories\HoldRepository;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class CleanupExpiredHolds extends Command
{
  
    protected $signature = 'holds:cleanup-expired';

   
    protected $description = 'Delete expired holds and release their reserved stock';

    protected HoldRepository $holdRepository;

    public function __construct(HoldRepository $holdRepository)
    {
        parent::__construct();
        $this->holdRepository = $holdRepository;
    }

   
    public function handle(): int
    {
        $this->info('Starting cleanup of expired holds...');

        $deletedCount = $this->holdRepository->deleteExpiredHolds();

        if ($deletedCount > 0) {
            $message = "Cleaned up {$deletedCount} expired hold(s)";
            $this->info($message);
            Log::info($message);
        } else {
            $this->info('No expired holds found');
        }

        return Command::SUCCESS;
    }
}
