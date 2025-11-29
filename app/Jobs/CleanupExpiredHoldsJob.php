<?php

namespace App\Jobs;

use App\Models\Hold;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Log;

class CleanupExpiredHoldsJob implements ShouldQueue
{
    use Queueable;

    public function __construct()
    {
        //
    }

    public function handle(): void
    {
        $count = 0;

        Hold::where('expires_at', '<=', Carbon::now())
            ->select('id')
            ->chunk(100, function ($holds) use (&$count) {
                foreach ($holds as $hold) {
                    DeleteExpiredHoldJob::dispatch($hold->id);
                    $count++;
                }
            });

        if ($count > 0) {
            Log::info("CleanupExpiredHoldsJob: Dispatched {$count} delete job(s) for expired holds");
        }
    }
}
