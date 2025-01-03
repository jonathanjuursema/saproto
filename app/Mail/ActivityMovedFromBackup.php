<?php

namespace App\Mail;

use App\Models\ActivityParticipation;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Config;

class ActivityMovedFromBackup extends Mailable
{
    use Queueable;
    use SerializesModels;

    public string $calling_name;

    public string $event_id;

    public string $event_title;

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
            ->from('board@'.Config::string('proto.emaildomain'), 'S.A. Proto')
            ->bcc('board@'.Config::string('proto.emaildomain'))
            ->subject('Moved from back-up list to participants for '.$this->event_title.'.')
            ->view('emails.takenfrombackup');
    }
}
