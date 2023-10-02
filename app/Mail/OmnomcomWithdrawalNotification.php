<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use App\Models\User;
use App\Models\Withdrawal;

class OmnomcomWithdrawalNotification extends Mailable
{
    use Queueable;
    use SerializesModels;

    public $user;

    public $withdrawal;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct(User $user, Withdrawal $withdrawal)
    {
        $this->user = $user;
        $this->withdrawal = $withdrawal;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this
            ->from('treasurer@'.config('proto.emaildomain'), config('proto.treasurer'))
            ->subject('S.A. Proto Withdrawal Announcement for '.date('d-m-Y', strtotime($this->withdrawal->date)))
            ->view('emails.omnomcom.withdrawalnotification');
    }
}
