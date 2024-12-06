<?php

namespace App\Models;

use Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * Activity Model.
 *
 * @property int $id
 * @property int|null $event_id
 * @property float|null $price
 * @property float $no_show_fee
 * @property int $participants
 * @property int|null $attendees
 * @property int $registration_start
 * @property int $registration_end
 * @property int $deregistration_end
 * @property string|null $comment
 * @property string|null $redirect_url
 * @property bool $closed
 * @property bool $hide_participants
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property ActivityParticipation[] $participation
 * @property ActivityParticipation[] $helpingParticipations
 * @property-read Account|null $closedAccount
 * @property-read Event|null $event
 * @property-read Collection|User[] $allUsers
 * @property-read Collection|User[] $backupUsers
 * @property-read Collection|HelpingCommittee[] $helpingCommitteeInstances
 * @property-read Collection|Committee[] $helpingCommittees
 * @property-read Collection|User[] $presentUsers
 * @property-read Collection|User[] $users
 *
 * @method static Builder|Activity whereClosed($value)
 * @method static Builder|Activity whereClosedAccount($value)
 * @method static Builder|Activity whereComment($value)
 * @method static Builder|Activity whereCreatedAt($value)
 * @method static Builder|Activity whereDeregistrationEnd($value)
 * @method static Builder|Activity whereEventId($value)
 * @method static Builder|Activity whereId($value)
 * @method static Builder|Activity whereNoShowFee($value)
 * @method static Builder|Activity whereParticipants($value)
 * @method static Builder|Activity wherePrice($value)
 * @method static Builder|Activity whereRegistrationEnd($value)
 * @method static Builder|Activity whereRegistrationStart($value)
 * @method static Builder|Activity whereUpdatedAt($value)
 * @method static Builder|Activity whereHideParticipants($value)
 * @method static Builder|Activity newModelQuery()
 * @method static Builder|Activity newQuery()
 * @method static Builder|Activity query()
 */
class Activity extends Validatable
{
    use HasFactory;

    protected $table = 'activities';

    protected $guarded = ['id'];

    protected array $rules = [
        'registration_start' => 'required|integer',
        'registration_end' => 'required|integer',
        'deregistration_end' => 'required|integer',
        'participants' => 'integer',
        'price' => 'required|regex:/[0-9]+(\.[0-9]{0,2}){0,1}/',
    ];

    public function event(): BelongsTo
    {
        return $this->belongsTo(Event::class);
    }

    public function closedAccount(): BelongsTo
    {
        return $this->belongsTo(Account::class, 'closed_account');
    }

    public function users(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'activities_users')
            ->withPivot('id', 'committees_activities_id', 'is_present')
            ->whereNull('activities_users.deleted_at')
            ->whereNull('committees_activities_id')
            ->where('backup', false)
            ->withTimestamps();
    }

    public function presentUsers(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'activities_users')
            ->withPivot('id', 'committees_activities_id', 'is_present')
            ->whereNull('activities_users.deleted_at')
            ->whereNull('committees_activities_id')
            ->where('activities_users.is_present', true)
            ->where('backup', false)
            ->withTimestamps();
    }

    public function allUsers(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'activities_users')
            ->withPivot('id', 'committees_activities_id', 'is_present')
            ->whereNull('activities_users.deleted_at')
            ->where('backup', false)
            ->withTimestamps();
    }

    public function backupUsers(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'activities_users')
            ->whereNull('activities_users.deleted_at')
            ->whereNull('committees_activities_id')
            ->where('backup', true)
            ->withPivot('id')
            ->withTimestamps();
    }

    public function helpingCommittees(): BelongsToMany
    {
        return $this->belongsToMany(Committee::class, 'committees_activities')->withPivot(['amount', 'id'])->withTimestamps();
    }

    public function helpingCommitteeInstances(): HasMany
    {
        return $this->hasMany(HelpingCommittee::class, 'activity_id');
    }

    /**
     * @return \Illuminate\Support\Collection The ActivityParticipations for the helping users.
     */
    public function helpingUsers(int $help_id): \Illuminate\Support\Collection
    {
        return ActivityParticipation::query()->whereNull('activities_users.deleted_at')->where('committees_activities_id', $help_id)->get();
    }

    /**
     * @return ActivityParticipation|null The ActivityParticipation for the supplied user and committee in combination with this activity. Returns null if there is none.
     */
    public function getHelpingParticipation(Committee $committee, User $user): ?ActivityParticipation
    {
        $h = HelpingCommittee::query()->where('activity_id', $this->id)
            ->where('committee_id', $committee->id)
            ->first();

        if ($h === null) {
            return null;
        }

        return ActivityParticipation::query()->where('activity_id', $this->id)
            ->where('user_id', $user->id)
            ->where('committees_activities_id', $h->id)->first();
    }

    /**
     * @return ActivityParticipation|null Return the ActivityParticipation for the supplied user. Returns null if users doesn't participate.
     */
    public function getParticipation(User $user, ?HelpingCommittee $h = null): ?ActivityParticipation
    {
        if ($h == null) {
            return ActivityParticipation::query()->where('activity_id', $this->id)
                ->where('user_id', $user->id)
                ->whereNull('committees_activities_id')
                ->first();
        }

        return ActivityParticipation::query()->where('activity_id', $this->id)
            ->where('user_id', $user->id)
            ->where('committees_activities_id', $h->id)
            ->first();
    }

    public function participation(): HasMany
    {
        return $this->hasMany(ActivityParticipation::class, 'activity_id');
    }

    public function helpingParticipations(): HasMany
    {
        return $this->hasMany(ActivityParticipation::class, 'activity_id')->whereNotNull('committees_activities_id');
    }

    /**
     * @return bool Whether the user participates
     */
    public function isParticipating(User $user): bool
    {
        return $this->getParticipation($user) instanceof ActivityParticipation;
    }

    public function isOnBackupList(User $user): bool
    {
        return $this->backupUsers->where('id', $user->id)->first() !== null;
    }

    /**
     * @return bool Whether the user or committee is helping
     */
    public function isHelping(User $user, ?HelpingCommittee $h = null): bool
    {
        if ($h instanceof HelpingCommittee) {
            return $this->getParticipation($user, $h) instanceof ActivityParticipation;
        }

        return ActivityParticipation::query()->where('activity_id', $this->id)->where('user_id', $user->id)->whereNotNull('committees_activities_id')->count() > 0;
    }

    /**
     * @return bool Whether the user is organising
     */
    public function isOrganising(User $user): ?bool
    {
        return $this->event?->committee?->isMember($user);
    }

    /**
     * @return bool Whether the activity is full
     */
    public function isFull(): bool
    {
        return $this->participants != -1 && ($this->users_count ?? $this->users->count()) >= $this->participants;
    }

    /**
     * @return int The number of free spots
     */
    public function freeSpots(): int
    {
        if ($this->participants <= 0) {
            return -1;
        }

        return max(($this->participants - count($this->users)), 0);
    }

    /**
     * @return bool Whether people can still subscribed to the activity.
     */
    public function canSubscribe(): bool
    {
        if ($this->closed || $this->isFull() || $this->participants == 0) {
            return false;
        }

        return date('U') > $this->registration_start && date('U') < $this->registration_end;
    }

    /**
     * @return bool Whether people can still subscribe to the activity's backup list.
     */
    public function canSubscribeBackup(): bool
    {
        if ($this->canSubscribe()) {
            return true;
        }

        return ! ($this->closed || $this->participants == 0 || date('U') < $this->registration_start);
    }

    /**
     * @return bool Whether people can still unsubscribe from the activity.
     */
    public function canUnsubscribe(): bool
    {
        if ($this->closed) {
            return false;
        }

        return date('U') < $this->deregistration_end;
    }

    /**
     * @return bool Whether the activity has started yet.
     */
    public function hasStarted(): bool
    {
        return $this->event->start < date('U');
    }

    /**
     * @return bool Whether the activity has participants.
     */
    public function withParticipants(): bool
    {
        return $this->participants !== 0;
    }

    /**
     * @return int|null how many people actually showed up
     */
    public function getAttendees(): ?int
    {
        $present = $this->getPresent();

        return $present > 0 ? $present : $this->attendees;
    }

    public function getPresent(): int
    {
        return ActivityParticipation::query()->where('activity_id', $this->id)
            ->where('is_present', true)
            ->where('backup', false)
            ->whereNull('deleted_at')
            ->count();
    }
}
