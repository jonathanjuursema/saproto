<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use App\Models\User;

class PwnedPasswordNotification extends Mailable
{
    use Queueable;
    use SerializesModels;

    public $name;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct(User $user)
    {
        $this->name = $user->name;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this
            ->subject('IMPORTANT SECURITY NOTICE: The password you use with S.A. Proto was exposed in a data breach.')
            ->from('security@proto.utwente.nl', 'S.A. Proto Security Notification')
            ->view('emails.pwnedpassword');
    }
}
