<?php

namespace Proto\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

class FeeEmailForBoard extends Mailable
{
    use Queueable, SerializesModels;

    public $charged_fees;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($charged_fees)
    {
        $this->charged_fees = $charged_fees;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this
            ->to('payments@proto.utwente.nl', 'S.A. Proto Payments Update')
            ->subject('Membership Fee Cron Update for ' . date('d-m-Y') . '. (' . $this->charged_fees->count . ' transactions)')
            ->view('emails.fee');
    }
}
