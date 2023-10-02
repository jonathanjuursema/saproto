<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use App\Models\EmailList;
use App\Models\User;

class MembershipEnded extends Mailable
{
    use Queueable;
    use SerializesModels;

    public $user;

    public $lists;

    /** @return void */
    public function __construct(User $user)
    {
        $this->user = $user;
        $this->lists = $this->getSubscriptionList();
    }

    /** @return MembershipEnded */
    public function build()
    {
        return $this
            ->from('secretary@proto.utwente.nl', config('proto.secretary').' (Secretary)')
            ->subject('Termination of your membership of Study Association Proto')
            ->view('emails.membershipend');
    }

    /** @return string */
    public function getSubscriptionList()
    {
        $footer = [];
        $lists = $this->user->lists;
        foreach ($lists as $list) {
            $footer[] = sprintf('<li>%s (<a href="%s">Unsubscribe</a>)</li>', $list->name, route('unsubscribefromlist', ['hash' => EmailList::generateUnsubscribeHash($this->user->id, $list->id)]));
        }

        return implode('', $footer);
    }
}
