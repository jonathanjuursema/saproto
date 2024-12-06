<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class ManualEmail extends Mailable
{
    use Queueable;
    use SerializesModels;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct(public $sender_address, public $sender_name, public $email_subject, public $body, public $submitted_attachments, public $destination, public $user_id, public $events, public $email_id) {}

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build(): static
    {
        $mail = $this->view('emails.manualemail')
            ->from($this->sender_address, $this->sender_name)
            ->subject($this->email_subject);
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
