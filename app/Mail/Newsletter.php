<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use App\Models\User;

class Newsletter extends Mailable
{
    use Queueable;
    use SerializesModels;

    public $user;

    public $list;

    public $text;

    /**
     * @param  string  $list
     * @param  string  $text
     */
    public function __construct(User $user, $list, $text)
    {
        $this->user = $user;
        $this->list = $list;
        $this->text = $text;
    }

    /** @return Newsletter */
    public function build()
    {
        return $this
            ->from('internal@'.config('proto.emaildomain'), config('proto.internal'))
            ->subject('S.A. Proto Weekly Newsletter (Week '.date('W').')')
            ->view('emails.newsletter');
    }
}
