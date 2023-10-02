<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use App\Models\User;

class UsernameReminderEmail extends Mailable
{
    use Queueable;
    use SerializesModels;

    public $name;

    public $username;

    public $ismember;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct(User $user)
    {
        $this->name = $user->calling_name;
        $this->ismember = $user->is_member;
        $this->username = $this->ismember ? $user->member->proto_username : null;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this
            ->subject('Your username for S.A. Proto.')
            ->view('emails.username');
    }
}
