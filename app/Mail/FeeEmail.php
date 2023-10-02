<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use App\Models\User;

class FeeEmail extends Mailable
{
    use Queueable;
    use SerializesModels;

    public $user;

    public $fee;

    public $fee_amount;

    public $remitted_reason;

    /**
     * @param  User  $user
     * @param  string  $fee
     * @param  float  $fee_amount
     * @param  null|string  $remitted_reason
     */
    public function __construct($user, $fee, $fee_amount, $remitted_reason)
    {
        $this->user = $user;
        $this->fee = $fee;
        $this->fee_amount = $fee_amount;
        $this->remitted_reason = $remitted_reason;
    }

    /** @return FeeEmail */
    public function build()
    {
        return $this
            ->from('treasurer@proto.utwente.nl', config('proto.treasurer'))
            ->subject('Information on your membership fee for S.A. Proto')
            ->view('emails.fee_for_users');
    }
}
