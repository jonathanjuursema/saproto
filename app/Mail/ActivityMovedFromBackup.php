<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use App\Models\ActivityParticipation;

class ActivityMovedFromBackup extends Mailable
{
    use Queueable;
    use SerializesModels;

    public $calling_name;

    public $event_id;

    public $event_title;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct(ActivityParticipation $participation)
    {
        $this->calling_name = $participation->user->calling_name;
        $this->event_id = $participation->activity->event->getPublicId();
        $this->event_title = $participation->activity->event->title;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this
            ->from('board@'.config('proto.emaildomain'), 'S.A. Proto')
            ->bcc('board@'.config('proto.emaildomain'))
            ->subject('Moved from back-up list to participants for '.$this->event_title.'.')
            ->view('emails.takenfrombackup');
    }
}
