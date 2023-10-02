<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use App\Models\HelpingCommittee;
use App\Models\User;

class HelperMutation extends Mailable
{
    use Queueable;
    use SerializesModels;

    public $helper_name;

    public $committee;

    public $event;

    public $helping;

    public $help;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct(User $helper, HelpingCommittee $help, $helping)
    {
        $this->helper_name = $helper->name;
        $this->helping = $helping;
        $this->help = $help;
        $this->committee = $help->committee;
        $this->event = $help->activity->event;
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
            ->subject(sprintf('Helper for the %s %s for the activity %s.', $this->committee->name, $this->helping ? 'subscribed' : 'UNSUBSCRIBED', $this->event->title))
            ->view('emails.helpermutation');
    }
}
