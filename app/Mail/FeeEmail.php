<?php

namespace App\Mail;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Config;

class FeeEmail extends Mailable
{
    use Queueable;
    use SerializesModels;

    /**
     * @param  User  $user
     * @param  string  $fee
     * @param  float  $fee_amount
     * @param  null|string  $remitted_reason
     */
    public function __construct(public $user, public $fee, public $fee_amount, public $remitted_reason) {}

    /** @return FeeEmail */
    public function build()
    {
        return $this
            ->from('treasurer@proto.utwente.nl', Config::string('proto.treasurer'))
            ->subject('Information on your membership fee for S.A. Proto')
            ->view('emails.fee_for_users');
    }
}
