<?php

namespace App\Models;

use Auth;
use Carbon;
use DB;
use Eloquent;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * Committee Model.
 *
 * @property int $id
 * @property string $name
 * @property string $slug
 * @property string $description
 * @property int $public
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property int|null $photo_id
 * @property Photo|null $photo
 * @property int $allow_anonymous_email
 * @property int $is_society
 * @property int $is_active
 * @property-read string $email_address
 * @property-read StorageEntry|null $image
 * @property-read Collection|HelperReminder[] $helperReminderSubscriptions
 * @property-read Collection|Event[] $organizedEvents
 * @property-read Collection|User[] $users
 *
 * @method static Builder|Committee whereAllowAnonymousEmail($value)
 * @method static Builder|Committee whereCreatedAt($value)
 * @method static Builder|Committee whereDescription($value)
 * @method static Builder|Committee whereId($value)
 * @method static Builder|Committee whereImageId($value)
 * @method static Builder|Committee whereIsSociety($value)
 * @method static Builder|Committee whereName($value)
 * @method static Builder|Committee wherePublic($value)
 * @method static Builder|Committee whereSlug($value)
 * @method static Builder|Committee whereUpdatedAt($value)
 * @method static Builder|Committee newModelQuery()
 * @method static Builder|Committee newQuery()
 * @method static Builder|Committee query()
 *
 * @mixin Eloquent
 */
class Committee extends Model
{
    protected $table = 'committees';

    protected $guarded = ['id'];

    protected $hidden = ['photo_id'];

    /** @return string */
    public function getPublicId()
    {
        return $this->slug;
    }

    /** @return Committee */
    public static function fromPublicId($public_id)
    {
        return self::where('slug', $public_id)->firstOrFail();
    }

    /** @return BelongsToMany */
    public function users()
    {
        return $this->belongsToMany('App\Models\User', 'committees_users')
            ->where(function ($query) {
                $query
                    ->whereNull('committees_users.deleted_at')
                    ->orWhere('committees_users.deleted_at', '>', Carbon::now());
            })
            ->where('committees_users.created_at', '<', Carbon::now())
            ->withPivot(['id', 'role', 'edition', 'created_at', 'deleted_at'])
            ->withTimestamps()
            ->orderBy('pivot_created_at', 'desc');
    }

    /** @return BelongsTo|Photo */
    public function photo()
    {
        return $this->belongsTo('App\Models\Photo', 'photo_id');
    }

    /** @return HasMany */
    public function organizedEvents()
    {
        return $this->hasMany('App\Models\Event', 'committee_id');
    }

    /** @return HasMany */
    public function helperReminderSubscriptions()
    {
        return $this->hasMany('App\Models\HelperReminder');
    }

    /** @return string */
    public function getEmailAddressAttribute()
    {
        return $this->slug.'@'.config('proto.emaildomain');
    }

    /** @return User[] */
    public function HelperReminderSubscribers()
    {
        $users = [];
        $subscriptions = $this->helperReminderSubscriptions()->get();
        foreach ($subscriptions as $subscription) {
            $users[] = $subscription->user;
        }

        return $users;
    }

    /**
     * @return bool Whether the user wants to receive helper reminders.
     */
    public function wantsToReceiveHelperReminder(User $user): bool
    {
        if (! $this->isMember($user)) {
            HelperReminder::where('user_id', $user->id)->where('committee_id', $this->id)->delete();
        }

        return $this->helperReminderSubscriptions()->where('user_id', $user->id)->count() > 0;
    }

    /** @return Collection|Event[] */
    public function pastEvents()
    {
        $events = $this->organizedEvents()->where('end', '<', time())->orderBy('start', 'desc');

        if (Auth::user()?->can('board')) {
            return $events->get();
        } else {
            return $events->where('secret', '=', 0)->get();
        }
    }

    /** @return Collection|Event[] */
    public function upcomingEvents()
    {
        $events = $this->organizedEvents()->where('end', '>', time());

        if (Auth::user()?->can('board')) {
            return $events->get();
        } else {
            return $events->where('secret', '=', 0)->get();
        }
    }

    /**
     * @param  bool  $includeSecret
     * @return Event[]
     */
    public function helpedEvents($includeSecret = false)
    {
        /** @var Activity[] $activities */
        $activities = $this->belongsToMany('App\Models\Activity', 'committees_activities')->orderBy('created_at', 'desc')->get();

        $events = [];
        foreach ($activities as $activity) {
            $event = $activity->event;
            if ($event?->isPublished() || (! $event->secret || $includeSecret)) {
                $events[] = $event;
            }
        }

        return $events;
    }

    /** @return Event[] */
    public function pastHelpedEvents()
    {
        /** @var Activity[] $activities */
        $activities = $this->belongsToMany('App\Models\Activity', 'committees_activities')->orderBy('created_at', 'desc')->get();

        $events = [];
        foreach ($activities as $activity) {
            $event = $activity->event;
            if ($event && ! $event->secret && $event->end < time()) {
                $events[] = $event;
            }
        }

        return $events;
    }

    /** @return array<string, array<string, array<int, CommitteeMembership>>> */
    public function allMembers()
    {
        $members = ['editions' => [], 'members' => ['current' => [], 'past' => [], 'future' => []]];
        $memberships = CommitteeMembership::withTrashed()->where('committee_id', $this->id)
            ->orderBy(DB::raw('deleted_at IS NULL'), 'desc')
            ->orderBy('created_at', 'desc')
            ->orderBy('deleted_at', 'desc')
            ->get();

        foreach ($memberships as $membership) {
            if ($membership->edition) {
                $members['editions'][$membership->edition][] = $membership;
            } else {
                if (
                    strtotime($membership->created_at) < date('U') &&
                    (! $membership->deleted_at || strtotime($membership->deleted_at) > date('U'))
                ) {
                    $members['members']['current'][] = $membership;
                } elseif (strtotime($membership->created_at) > date('U')) {
                    $members['members']['future'][] = $membership;
                } else {
                    $members['members']['past'][] = $membership;
                }
            }
        }

        return $members;
    }

    /**
     * @param  User  $user
     * @return bool Whether the use is a member of the committee.
     */
    public function isMember($user)
    {
        return $user->isInCommittee($this);
    }
}
