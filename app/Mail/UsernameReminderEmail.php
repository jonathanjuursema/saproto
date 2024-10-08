<?php

namespace App\Mail;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class UsernameReminderEmail extends Mailable
{
    use Queueable;
    use SerializesModels;

    public string $name;

    public ?string $username;

    public bool $ismember;

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
    public function build(): static
    {
        return $this
            ->subject('Your username for S.A. Proto.')
            ->view('emails.username');
    }
}
