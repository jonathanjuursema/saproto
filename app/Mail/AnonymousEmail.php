<?php

namespace App\Mail;

use App\Models\Committee;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class AnonymousEmail extends Mailable
{
    use Queueable;
    use SerializesModels;

    /**
     * @var Committee
     */
    public $committee;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct(Committee $committee, public $message_content, public $hash)
    {
        $this->committee = $committee;
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
