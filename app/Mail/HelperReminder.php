<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use App\Models\HelpingCommittee;

class HelperReminder extends Mailable
{
    use Queueable;
    use SerializesModels;

    public $committee;

    public $event;

    public $help;

    public $helping_count;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct(HelpingCommittee $help)
    {
        $this->help = $help;
        $this->committee = $help->committee;
        $this->event = $help->activity->event;
        $this->helping_count = $help->helperCount();
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this
            ->to($this->committee->helperReminderSubscribers())
            ->from('webmaster@proto.utwente.nl', 'S.A. Proto')
            ->subject(sprintf('Activity %s is in three days, but doesn\'t have enough helpers for the %s.', $this->event->title, $this->committee->name))
            ->view('emails.helperreminder');
    }
}
