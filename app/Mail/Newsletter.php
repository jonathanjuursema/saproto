<?php

namespace App\Mail;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Config;

class Newsletter extends Mailable
{
    use Queueable;
    use SerializesModels;

    /**
     * @param  string  $list
     * @param  string  $text
     */
    public function __construct(public User $user, public $list, public $text, public $events, public $image_url)
    {
        //
    }

    /** @return Newsletter */
    public function build()
    {
        return $this
            ->from('internal@'.Config::string('proto.emaildomain'), Config::string('proto.internal'))
            ->subject('S.A. Proto Weekly Newsletter (Week '.date('W').')')
            ->view('emails.newsletter');
    }
}
