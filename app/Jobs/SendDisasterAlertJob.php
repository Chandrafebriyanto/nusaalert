<?php

namespace App\Jobs;

use App\Models\Alert;
use App\Models\Bencana;
use App\Models\User;
use App\Notifications\DisasterAlertNotification;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class SendDisasterAlertJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries = 3;
    public int $backoff = 30;

    public function __construct(
        public Alert $alert
    ) {}

    public function handle(): void
    {
        $alert = $this->alert->load(['bencana', 'lokasi', 'user']);

        if (!$alert->user || !$alert->bencana) {
            Log::warning("SendDisasterAlertJob: Alert #{$alert->id} missing user or bencana relation.");
            return;
        }

        try {
            // Send via Laravel Notification (database + mail channels)
            $alert->user->notify(new DisasterAlertNotification($alert));
            Log::info("Notification sent for Alert #{$alert->id} to user {$alert->user->email}");
        } catch (\Exception $e) {
            Log::error("Failed to send notification for Alert #{$alert->id}: " . $e->getMessage());
            throw $e; // Let queue retry
        }
    }
}
