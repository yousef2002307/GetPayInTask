<?php

namespace App\Jobs;

use App\Repositories\HoldRepository;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Log;

class DeleteExpiredHoldJob implements ShouldQueue
{
    use Queueable;

    protected int $holdId;

    public function __construct(int $holdId)
    {
        $this->holdId = $holdId;
    }

    public function handle(HoldRepository $holdRepository): void
    {
        try {
            $holdRepository->deleteExpiredHolds($this->holdId);
        } catch (\Exception $e) {
            Log::error("Failed to delete expired hold {$this->holdId}: " . $e->getMessage());
        }
    }
}
