<?php

namespace App\Models;

use App\Enums\MembershipTypeEnum;
use Carbon;
use Eloquent;
use Exception;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Support\Collection as SupportCollection;
use Illuminate\Support\Facades\DB;

/**
 * Email Model.
 *
 * @property int $id
 * @property string $description
 * @property string $subject
 * @property string $sender_name
 * @property string $sender_address
 * @property string $body
 * @property int|null $sent_to
 * @property bool $to_user
 * @property bool $to_member
 * @property bool $to_list
 * @property bool $to_event
 * @property bool $to_active
 * @property bool $to_pending
 * @property bool $to_backup
 * @property bool $ready
 * @property bool $sent
 * @property int $time
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read Collection|StorageEntry[] $attachments
 * @property-read Collection|Event[] $events
 * @property-read Collection|EmailList[] $lists
 *
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
 * @method static Builder|Email whereToPending($value)
 * @method static Builder|Email newModelQuery()
 * @method static Builder|Email newQuery()
 * @method static Builder|Email query()
 *
 * @mixin Eloquent
 */
class Email extends Model
{
    use HasFactory;

    protected $table = 'emails';

    protected $guarded = ['id'];

    public function lists(): BelongsToMany
    {
        return $this->belongsToMany(EmailList::class, 'emails_lists', 'email_id', 'list_id');
    }

    public function events(): BelongsToMany
    {
        return $this->belongsToMany(Event::class, 'emails_events', 'email_id', 'event_id');
    }

    public function attachments(): BelongsToMany
    {
        return $this->belongsToMany(StorageEntry::class, 'emails_files', 'email_id', 'file_id');
    }

    /**
     * @throws Exception
     */
    public function destinationForBody(): string
    {
        if ($this->to_user) {
            return 'users';
        }

        if ($this->to_member) {
            return 'members';
        }

        if ($this->to_pending) {
            return 'pending';
        }

        if ($this->to_active) {
            return 'active members';
        }

        if ($this->to_list) {
            return 'list';
        }

        if ($this->to_event) {
            if ($this->to_backup) {
                return 'event with backup';
            }

            return 'event';
        }

        throw new Exception('Email has no destination');
    }

    public function recipients(): SupportCollection
    {
        if ($this->to_user) {
            return User::query()->orderBy('name')->get();
        }

        if ($this->to_member) {
            return User::query()->whereHas('member', static function ($q) {
                $q->whereNot('membership_type', MembershipTypeEnum::PENDING);
            })->orderBy('name')->get();
        }

        if ($this->to_pending) {
            return User::query()->whereHas('member', static function ($q) {
                $q->type(MembershipTypeEnum::PENDING);
            })->orderBy('name')->get();
        }

        if ($this->to_active) {
            return User::query()->whereHas('committees')->orderBy('name')->get();
        }

        if ($this->to_list) {
            return User::query()->whereHas('lists', function ($q) {
                $q->whereIn('users_mailinglists.list_id', $this->lists->pluck('id')->toArray());
            })->orderBy('name')->get();
        }

        if ($this->to_event) {
            $user_ids = [];
            foreach ($this->events as $event) {
                if ($event != null) {
                    $user_ids = array_merge($user_ids, $event->allUsers()->pluck('id')->toArray());
                    if ($this->to_backup && $event->activity) {
                        $user_ids = array_merge($user_ids, $event->activity->backupUsers()->pluck('users.id')->toArray());
                    }
                }
            }

            return User::query()->whereIn('id', $user_ids)->orderBy('name')->get();
        }

        return collect();
    }

    public function hasRecipientList(EmailList $list): bool
    {
        return DB::table('emails_lists')->where('email_id', $this->id)->where('list_id', $list->id)->exists();
    }

    /**
     * @return string Email body with variables parsed.
     */
    public function parseBodyFor(User $user): string
    {
        $variable_from = ['$calling_name', '$name'];
        $variable_to = [$user->calling_name, $user->name];

        if ($this->to_member || $this->to_active || $this->to_pending) {
            $variable_from[] = '$username';
            $variable_to[] = $user->member->proto_username ?? '(no username found)';
        }

        return str_replace($variable_from, $variable_to, $this->body);
    }

    public function getEventName(): string
    {
        $events = [];
        if (! $this->to_event) {
            return '';
        }

        foreach ($this->events as $event) {
            $events[] = $event->title;
        }

        return implode(', ', $events);
    }

    public function getListName(): string
    {
        $lists = [];
        if (! $this->to_list) {
            return '';
        }

        foreach ($this->lists as $list) {
            $lists[] = $list->name;
        }

        return implode(', ', $lists);
    }

    public static function getListUnsubscribeFooter($user_id, $email_id): string
    {
        $footer = [];
        $lists = self::whereId($email_id)->firstOrFail()->lists;
        foreach ($lists as $list) {
            $footer[] = sprintf('%s (<a href="%s" style="color: #00aac0;">unsubscribe</a>)', $list->name, route('unsubscribefromlist', ['hash' => EmailList::generateUnsubscribeHash($user_id, $list->id)]));
        }

        return implode(', ', $footer);
    }
}
