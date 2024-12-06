<?php

namespace App\Jobs;

use App\Mail\NewManualEmail;
use App\Models\Email;
use App\Models\User;
use DateTime;
use Illuminate\Bus\Batchable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Queue\Middleware\RateLimited;
use Illuminate\Queue\Middleware\SkipIfBatchCancelled;
use Illuminate\Support\Facades\Mail;

class SendManualEmail implements ShouldQueue
{
    use Batchable, Queueable;

    /**
     * The number of times the job may be attempted.
     *
     * @var int
     */
    public int $tries = 20;

    /**
     * The maximum number of unhandled exceptions to allow before failing.
     *
     * @var int
     */
    public int $maxExceptions = 3;

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

    public function retryUntil(): DateTime
    {
        return now()->addMinutes(30);
    }

    public function middleware(): array
    {
        return [new SkipIfBatchCancelled, (new RateLimited('emails'))];
    }

    public function backoff(): array
    {
        return [30, 60, 60 * 5, 60 * 10, 60 * 30];
    }
}
