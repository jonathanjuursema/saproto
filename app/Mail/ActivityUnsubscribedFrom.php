<?php

namespace App\Mail;

use App\Models\ActivityParticipation;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class ActivityUnsubscribedFrom extends Mailable
{
    use Queueable;
    use SerializesModels;

    /**
     * @var array<'id'|'name'|'title', mixed>
     */
    public $activity;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct(ActivityParticipation $participation)
    {
        $this->activity = [
            'id' => $participation->activity->event->getPublicId(),
            'title' => $participation->activity->event->title,
            'name' => $participation->user->calling_name,
        ];
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
            ->subject('You have been signed out for '.$this->activity['title'].'.')
            ->view('emails.unsubscribeactivity');
    }
}
