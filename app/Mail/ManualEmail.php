<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class ManualEmail extends Mailable
{
    use Queueable;
    use SerializesModels;

    public $sender_address;

    public $sender_name;

    public $subject;

    public $body;

    public $submitted_attachments;

    public $destination;

    public $user_id;

    public $events;

    public $email_id;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($sender_address, $sender_name, $subject, $body, $attachments, $destination, $user_id, $events, $email_id)
    {
        $this->sender_address = $sender_address;
        $this->sender_name = $sender_name;
        $this->subject = $subject;
        $this->body = $body;
        $this->submitted_attachments = $attachments;
        $this->destination = $destination;
        $this->user_id = $user_id;
        $this->events = $events;
        $this->email_id = $email_id;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        $mail = $this->view('emails.manualemail')
            ->from($this->sender_address, $this->sender_name)
            ->subject($this->subject);
        foreach ($this->submitted_attachments as $attachment) {
            $options = [
                'as' => $attachment->original_filename,
                'mime' => $attachment->mime,
            ];
            $mail->attach($attachment->generateLocalPath(), $options);
        }

        return $mail;
    }
}
