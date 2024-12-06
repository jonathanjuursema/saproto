<?php

namespace App\Jobs;

use App\Mail\ManualEmail;
use App\Mail\NewManualEmail;
use App\Models\Email;
use App\Models\User;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Mail;

class SendManualEmail implements ShouldQueue
{
    use Queueable;

    /**
     * Create a new job instance.
     */
    public function __construct(public Email $email, public User $recipient)
    {
        //
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        Mail::to($recipient)
            ->queue((new ManualEmail(
                $email->sender_address . '@' . config('proto.emaildomain'),
                $email->sender_name,
                $email->subject,
                $email->parseBodyFor($recipient),
                $email->attachments,
                $email->destination->text(),
                $recipient->id,
                $email->events()->get(),
                $email->id
            )
            )->onQueue('medium'));
    }
}
