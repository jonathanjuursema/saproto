<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use App\Models\User;

class UserMailChange extends Mailable
{
    use Queueable;
    use SerializesModels;

    public $user;

    public $changer;

    public $email;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct(User $user, $changer, $email)
    {
        $this->user = $user;
        $this->changer = $changer;
        $this->email = $email;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this
            ->from('security@'.config('proto.emaildomain'), 'Have You Tried Turning It Off And On Again committee')
            ->subject('Your e-mail address for S.A. Proto has been changed.')
            ->view('emails.emailchange');
    }
}
