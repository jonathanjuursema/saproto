<?php

namespace App\Models;

use App\Enums\EmailDestination;
use Carbon;
use DB;
use Eloquent;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Support\Collection as SupportCollection;

/**
 *
 * @property int $id
 * @property string $description
 * @property string $subject
 * @property string $sender_name
 * @property string $sender_address
 * @property string $body
 * @property int|null $sent_to
 * @property EmailDestination $destination
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
 * /
 * Email Model.
 */
class Email extends Model
{
    protected $table = 'emails';

    protected $guarded = ['id'];

    protected $casts = [
        'ready' => 'boolean',
        'sent' => 'boolean',
        'destination' => EmailDestination::class,
    ];


    /** @return BelongsToMany */
    public function lists(): BelongsToMany
    {
        return $this->belongsToMany(EmailList::class, 'emails_lists', 'email_id', 'list_id');
    }

    /** @return BelongsToMany */
    public function events(): BelongsToMany
    {
        return $this->belongsToMany(Event::class, 'emails_events', 'email_id', 'event_id');
    }

    /** @return BelongsToMany */
    public function attachments(): BelongsToMany
    {
        return $this->belongsToMany(StorageEntry::class, 'emails_files', 'email_id', 'file_id');
    }

    /**
     * @return string
     *
     */
    public function destinationForBody(): string
    {
        return match ($this->destination) {
            EmailDestination::ALL_USERS => 'users',
            EmailDestination::ALL_MEMBERS => 'members',
            EmailDestination::PENDING_MEMBERS => 'pending',
            EmailDestination::ACTIVE_MEMBERS => 'active members',
            EmailDestination::EMAIL_LIST => 'list',
            EmailDestination::EVENT => 'event',
            EmailDestination::EVENT_WITH_BACKUP => 'event with backup',
            EmailDestination::NO_DESTINATION => 'no destination',
        };
    }

    /** @return SupportCollection|User[] */
    public function recipients(): array|SupportCollection
    {
        return match ($this->destination) {
            EmailDestination::ALL_USERS => User::orderBy('name')->get(),
            EmailDestination::ALL_MEMBERS => User::whereHas('member', fn($q) => $q->where('is_pending', false))->orderBy('name')->get(),
            EmailDestination::PENDING_MEMBERS => User::whereHas('member', fn($q) => $q->where('is_pending', true))->orderBy('name')->get(),
            EmailDestination::ACTIVE_MEMBERS => User::whereHas('committees')->orderBy('name')->get(),
            EmailDestination::EMAIL_LIST => User::whereHas('lists', fn($q) => $q->whereIn('users_mailinglists.list_id', $this->lists->pluck('id')->toArray()))->orderBy('name')->get(),
            EmailDestination::EVENT => User::whereIn('id', $this->events->map(fn($event) => $event->allUsers()->pluck('id'))->flatten()->toArray())->orderBy('name', 'asc')->get(),
            EmailDestination::EVENT_WITH_BACKUP => User::whereIn('id', $this->events->map(fn($event) => $event->allUsers()->pluck('id'))->flatten()->toArray())->orderBy('name', 'asc')->get(),
            EmailDestination::NO_DESTINATION => collect([]),
        };
    }

    /**
     * @param EmailList $list
     * @return bool
     */
    public function hasRecipientList(EmailList $list): bool
    {
        return DB::table('emails_lists')->where('email_id', $this->id)->where('list_id', $list->id)->count() > 0;
    }

    /**
     * @param User $user
     * @return string Email body with variables parsed.
     */
    public function parseBodyFor(User $user): string
    {
        $variable_from = ['$calling_name', '$name'];
        $variable_to = [$user->calling_name, $user->name];

        return str_replace($variable_from, $variable_to, $this->body);
    }

    /** @return string */
    public function getEventName(): string
    {
        return match ($this->destination) {
            EmailDestination::EVENT, EmailDestination::EVENT_WITH_BACKUP => implode(', ', $this->events->pluck('title')->toArray()),
            default => '',
        };
    }

    /** @return string */
    public function getListName(): string
    {
        return match ($this->destination) {
            EmailDestination::EMAIL_LIST => implode(', ', $this->lists->pluck('name')->toArray()),
            default => '',
        };
    }

    /** @return string */
    public static function getListUnsubscribeFooter(int $user_id, int $email_id): string
    {
        $footer = [];
        $lists = self::whereId($email_id)->firstOrFail()->lists;
        foreach ($lists as $list) {
            $footer[] = sprintf('%s (<a href="%s" style="color: #00aac0;">unsubscribe</a>)', $list->name, route('unsubscribefromlist', ['hash' => EmailList::generateUnsubscribeHash($user_id, $list->id)]));
        }

        return implode(', ', $footer);
    }
}
