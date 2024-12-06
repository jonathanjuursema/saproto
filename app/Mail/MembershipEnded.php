<?php

namespace App\Mail;

use App\Models\EmailList;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Config;

class MembershipEnded extends Mailable
{
    use Queueable;
    use SerializesModels;

    /**
     * @var User
     */
    public $user;

    /**
     * @var string
     */
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
            ->from('secretary@proto.utwente.nl', Config::string('proto.secretary').' (Secretary)')
            ->subject('Termination of your membership of Study Association Proto')
            ->view('emails.membershipend');
    }

    public function getSubscriptionList(): string
    {
        $footer = [];
        $lists = $this->user->lists;
        foreach ($lists as $list) {
            $footer[] = sprintf('<li>%s (<a href="%s">Unsubscribe</a>)</li>', $list->name, route('unsubscribefromlist', ['hash' => EmailList::generateUnsubscribeHash($this->user->id, $list->id)]));
        }

        return implode('', $footer);
    }
}
