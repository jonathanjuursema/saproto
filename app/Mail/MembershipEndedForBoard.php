<?php

namespace Proto\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class MembershipEndedForBoard extends Mailable
{
    use Queueable;
    use SerializesModels;

    public $deleted_memberships;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($deleted_memberships)
    {
        $this->deleted_memberships = $deleted_memberships;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this
            ->to('board@proto.utwente.nl', 'S.A. Proto Membership Terminations')
            ->subject('Member ship automatically ended for '.count($this->deleted_memberships).' members! '.date('d-m-Y'))
            ->view('emails.membershipsendedforboard');
    }
}
