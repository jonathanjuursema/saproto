<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class DailyHelperMail extends Mailable
{
    use Queueable;
    use SerializesModels;

    public $user;

    public $events;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($user, $events)
    {
        $this->user = $user;
        $this->events = $events;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this
            ->from('board@proto.utwente.nl', 'S.A. Proto')
            ->subject(count($this->events).' '.(count($this->events) === 1 ? 'activity needs' : 'activities need').' your help!')
            ->view('emails.dailyhelpermail');
    }
}
