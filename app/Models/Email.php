<?php

namespace App\Models;

use App\Enums\EmailDestination;
use App\Enums\MembershipTypeEnum;
use Carbon;
use Eloquent;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Support\Collection as SupportCollection;
use Illuminate\Support\Facades\DB;

/**
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
    use HasFactory;

    protected $table = 'emails';

    protected $guarded = ['id'];

    protected function casts(): array
    {
        return [
            'ready' => 'boolean',
            'sent' => 'boolean',
            'destination' => EmailDestination::class,
        ];
    }

    public function lists(): BelongsToMany
    {
        return $this->belongsToMany(EmailList::class, 'emails_lists', 'email_id', 'list_id');
    }

    public function events(): BelongsToMany
    {
        return $this->belongsToMany(Event::class, 'emails_events', 'email_id', 'event_id');
    }

    public function specificUsers(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'emails_users', 'email_id', 'user_id');
    }

    public function attachments(): BelongsToMany
    {
        return $this->belongsToMany(StorageEntry::class, 'emails_files', 'email_id', 'file_id');
    }

    /** @return SupportCollection|User[] */
    public function recipients(): array|SupportCollection
    {
        return match ($this->destination) {
            EmailDestination::ALL_USERS => User::query()->orderBy('name')->get(),
            EmailDestination::ALL_MEMBERS => User::query()->whereHas('member', fn ($q) => $q->whereNot('membership_type', MembershipTypeEnum::PENDING))->orderBy('name')->get(),
            EmailDestination::PENDING_MEMBERS => User::query()->whereHas('member', fn ($q) => $q->where('membership_type', MembershipTypeEnum::PENDING))->orderBy('name')->get(),
            EmailDestination::ACTIVE_MEMBERS => User::query()->whereHas('committees')->orderBy('name')->get(),
            EmailDestination::EMAIL_LISTS => User::query()->whereHas('lists', fn ($q) => $q->whereIn('users_mailinglists.list_id', $this->lists->pluck('id')->toArray()))->orderBy('name')->get(),
            EmailDestination::EVENT => User::query()->whereIn('id', $this->events->map(fn ($event) => $event->allUsers()->pluck('id'))->flatten()->toArray())->orderBy('name')->get(),
            EmailDestination::EVENT_WITH_BACKUP => User::query()->whereIn('id', array_merge($this->events->map(fn ($event) => $event->allUsers()->pluck('id'))->flatten()->toArray(), $this->events->map(fn ($event) => $event->backupUsers()->pluck('id'))))->orderBy('name')->get(),
            EmailDestination::NO_DESTINATION => collect(),
            EmailDestination::SPECIFIC_USERS => $this->specificUsers()->get(),
        };
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

    public function getConcatLists(): string
    {
        return match ($this->destination) {
            EmailDestination::EVENT, EmailDestination::EVENT_WITH_BACKUP => implode(', ', $this->events->pluck('title')->toArray()),
            EmailDestination::EMAIL_LISTS => implode(', ', $this->lists->pluck('name')->toArray()),
            EmailDestination::SPECIFIC_USERS => implode(', ', $this->specificUsers()->pluck('name')->toArray()),
            default => '',
        };
    }

    public function getListUnsubscribeFooter(int $user_id): string
    {
        foreach ($this->lists as $list) {
            $footer[] = sprintf('%s (<a href="%s" style="color: #00aac0;">unsubscribe</a>)', $list->name, route('unsubscribefromlist', ['hash' => EmailList::generateUnsubscribeHash($user_id, $list->id)]));
        }

        return implode(', ', $footer);
    }
}
