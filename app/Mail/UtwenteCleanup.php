<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Config;

class UtwenteCleanup extends Mailable
{
    use Queueable;
    use SerializesModels;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct(public $unlinked) {}

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this
            ->to('secretary@'.Config::string('proto.emaildomain'), 'S.A. Proto Secretary')
            ->cc('sysadmin@'.Config::string('proto.emaildomain'), 'S.A. Proto System Admins')
            ->subject('UTwente Account Clean-Up')
            ->view('emails.utwente_cleanup');
    }
}
