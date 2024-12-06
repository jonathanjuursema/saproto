<?php

namespace App\Jobs;

use App\Mail\NewManualEmail;
use App\Models\Email;
use App\Models\User;
use Illuminate\Bus\Batchable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Queue\Middleware\SkipIfBatchCancelled;
use Illuminate\Support\Facades\Mail;

class SendManualEmail implements ShouldQueue
{
    use Batchable, Queueable;

    /**
     * Create a new job instance.
     */
    public function __construct(public Email $email, public User $recipient)
    {
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        Mail::to($this->recipient)->sendNow(new NewManualEmail($this->email, $this->recipient));
    }

    public function middleware(): array
    {
        return [new SkipIfBatchCancelled];
    }
}
