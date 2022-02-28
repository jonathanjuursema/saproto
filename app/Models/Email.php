<?php

namespace Proto\Models;

use Carbon;
use DB;
use Eloquent;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Support\Collection as SupportCollection;

/**
 * Email Model.
 *
 * @property int $id
 * @property string $description
 * @property string $subject
 * @property string $sender_name
 * @property string $sender_address
 * @property string $body
 * @property int $to_user
 * @property int $to_member
 * @property int $to_list
 * @property int $to_event
 * @property int $to_active
 * @property int $to_pending
 * @property int|null $sent_to
 * @property int $sent
 * @property int $ready
 * @property int $time
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read Collection|StorageEntry[] $attachments
 * @property-read Collection|Event[] $events
 * @property-read Collection|EmailList[] $lists
 * @method static Builder|Email whereBody($value)
 * @method static Builder|Email whereCreatedAt($value)
 * @method static Builder|Email whereDescription($value)
 * @method static Builder|Email whereId($value)
 * @method static Builder|Email whereReady($value)
 * @method static Builder|Email whereSenderAddress($value)
 * @method static Builder|Email whereSenderName($value)
 * @method static Builder|Email whereSent($value)
 * @method static Builder|Email whereSentTo($value)
 * @method static Builder|Email whereSubject($value)
 * @method static Builder|Email whereTime($value)
 * @method static Builder|Email whereToActive($value)
 * @method static Builder|Email whereToEvent($value)
 * @method static Builder|Email whereToList($value)
 * @method static Builder|Email whereToMember($value)
 * @method static Builder|Email whereToUser($value)
 * @method static Builder|Email whereUpdatedAt($value)
 * @mixin Eloquent
 */
class Email extends Model
{
    protected $table = 'emails';

    protected $guarded = ['id'];

    /** @return BelongsToMany|EmailList[] */
    public function lists()
    {
        return $this->belongsToMany('Proto\Models\EmailList', 'emails_lists', 'email_id', 'list_id');
    }

    /** @return BelongsToMany|Event[] */
    public function events()
    {
        return $this->belongsToMany('Proto\Models\Event', 'emails_events', 'email_id', 'event_id');
    }

    /** @return BelongsToMany|StorageEntry[] */
    public function attachments()
    {
        return $this->belongsToMany('Proto\Models\StorageEntry', 'emails_files', 'email_id', 'file_id');
    }

    /** @return string */
    public function destinationForBody()
    {
        if ($this->to_user) {
            return 'users';
        } elseif ($this->to_member) {
            return 'members';
        } elseif ($this->to_pending) {
            return 'pending';
        } elseif ($this->to_active) {
            return 'active members';
        } elseif ($this->to_list) {
            return 'list';
        } elseif ($this->to_event) {
            return 'event';
        }
    }

    /** @return SupportCollection|User[] */
    public function recipients()
    {
        if ($this->to_user) {
            return User::orderBy('name', 'asc')->get();
        } elseif ($this->to_member) {
            return User::has('member')->orderBy('name', 'asc')->get()->reject(function ($user, $index) {
                return $user->member->is_pending == true;
            });
        } elseif ($this->to_pending) {
            return User::has('member')->orderBy('name', 'asc')->get()->reject(function ($user) {
                return $user->member->is_pending == false;
            });
        } elseif ($this->to_active) {
            $user_ids = [];
            foreach (Committee::all() as $committee) {
                $user_ids = array_merge($user_ids, $committee->users->pluck('id')->toArray());
            }
            return User::whereIn('id', $user_ids)->orderBy('name', 'asc')->get();
        } elseif ($this->to_list) {
            $user_ids = [];
            foreach ($this->lists as $list) {
                $user_ids = array_merge($user_ids, $list->users->pluck('id')->toArray());
            }
            return User::whereIn('id', $user_ids)->orderBy('name', 'asc')->get();
        } elseif ($this->to_event != false) {
            $user_ids = [];
            foreach ($this->events as $event) {
                if ($event) {
                    $user_ids = array_merge($user_ids, $event->returnAllUsers()->pluck('id')->toArray());
                }
            }
            return User::whereIn('id', $user_ids)->orderBy('name', 'asc')->get();
        } else {
            return collect([]);
        }
    }

    /** @return bool */
    public function hasRecipientList(EmailList $list)
    {
        return DB::table('emails_lists')->where('email_id', $this->id)->where('list_id', $list->id)->count() > 0;
    }

    /**
     * @param User $user
     * @return string Email body with variables parsed.
     */
    public function parseBodyFor($user)
    {
        $variable_from = ['$calling_name', '$name'];
        $variable_to = [$user->calling_name, $user->name];
        return str_replace($variable_from, $variable_to, $this->body);
    }

    /** @return string */
    public function getEventName()
    {
        $events = [];
        if ($this->to_event == false) {
            return '';
        } else {
            foreach ($this->events as $event) {
                if ($event) {
                    $events[] = $event->title;
                } else {
                    $events[] = 'Unknown Event';
                }
            }
        }
        return implode(', ', $events);
    }

    /** @return string */
    public function getListName()
    {
        $lists = [];
        if ($this->to_list == false) {
            return '';
        } else {
            foreach ($this->lists as $list) {
                $lists[] = $list->name;
            }
        }
        return implode(', ', $lists);
    }

    /** @return string */
    public static function getListUnsubscribeFooter($user_id, $email_id)
    {
        $footer = [];
        $lists = self::whereId($email_id)->firstOrFail()->lists;
        foreach ($lists as $list) {
            $footer[] = sprintf('%s (<a href="%s" style="color: #00aac0;">unsubscribe</a>)', $list->name, route('unsubscribefromlist', ['hash' => EmailList::generateUnsubscribeHash($user_id, $list->id)]));
        }
        return implode(', ', $footer);
    }
}
