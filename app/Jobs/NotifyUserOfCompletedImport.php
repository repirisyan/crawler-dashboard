<?php

namespace App\Jobs;

use App\Models\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;

class NotifyUserOfCompletedImport implements ShouldQueue
{
    use Queueable;

    public $user_id;

    public $message;

    /**
     * Create a new job instance.
     */
    public function __construct($user_id, $message)
    {
        $this->user_id = $user_id;
        $this->message = $message;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        Notification::create([
            'user_id' => $this->user_id,
            'message' => $this->message,
            'category' => 'success',
        ]);
    }
}
