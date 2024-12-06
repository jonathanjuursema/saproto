<?php

namespace App\Mail;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Config;

class VerifyPersonalDetails extends Mailable
{
    use Queueable;
    use SerializesModels;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct(public User $user) {}

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build(): static
    {
        return $this
            ->from('board@'.Config::string('proto.emaildomain'), 'Have You Tried Turning It Off And On Again committee')
            ->subject('Please make sure your personal details are still up to date.')
            ->view('emails.users.verifypersonaldetails');
    }
}
