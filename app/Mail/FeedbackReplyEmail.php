<?php

namespace App\Mail;

use App\Models\Feedback;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Config;

class FeedbackReplyEmail extends Mailable
{
    use Queueable;
    use SerializesModels;

    /**
     * Create a new message instance.
     */
    public function __construct(public Feedback $feedback, public User $user, public string $reply, public bool $accepted) {}

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this
            ->from('board@'.Config::string('proto.emaildomain'), 'Board of S.A. Proto')
            ->subject('Answer on your '.$this->feedback->category->title)
            ->view('emails.feedbackreply');
    }
}
