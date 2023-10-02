<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use App\Models\Committee;

class AnonymousEmail extends Mailable
{
    use Queueable;
    use SerializesModels;

    public $committee;

    public $message_content;

    public $hash;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct(Committee $committee, $message_content, $message_hash)
    {
        $this->committee = $committee;
        $this->message_content = $message_content;
        $this->hash = $message_hash;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this
            ->subject('Anonymous e-mail for the '.$this->committee->name.'.')
            ->view('emails.anonymous');
    }
}
