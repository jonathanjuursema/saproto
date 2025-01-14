<?php

namespace App\Models;

use Carbon;
use Eloquent;
use Hashids;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Query\Builder as QueryBuilder;
use Illuminate\Support\Collection as SupportCollection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Config;
use Override;

/**
 * Event Model.
 *
 * @property int $id
 * @property string $title
 * @property string $description
 * @property int $start
 * @property int $end
 * @property int $publication
 * @property int|null $image_id
 * @property int|null $committee_id
 * @property int|null $category_id
 * @property string|null $summary
 * @property string $location
 * @property bool $is_featured
 * @property bool $is_external
 * @property bool $involves_food
 * @property bool $secret
 * @property bool $force_calendar_sync
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property Carbon|null $deleted_at
 * @property int $unique_users_count
 * @property-read object $formatted_date
 * @property-read bool $is_future
 * @property-read Activity|null $activity
 * @property-read StorageEntry|null $image
 * @property-read Committee|null $committee
 * @property-read EventCategory|null $category
 * @property-read Collection|PhotoAlbum[] $albums
 * @property-read Collection|Ticket[] $tickets
 * @property-read Collection|Video[] $videos
 *
 * @method static bool|null forceDelete()
 * @method static QueryBuilder|Event onlyTrashed()
 * @method static QueryBuilder|Event withTrashed()
 * @method static QueryBuilder|Event withoutTrashed()
 * @method static bool|null restore()
 * @method static Builder|Event whereCommitteeId($value)
 * @method static Builder|Event whereCategoryId($value)
 * @method static Builder|Event whereCreatedAt($value)
 * @method static Builder|Event whereDeletedAt($value)
 * @method static Builder|Event whereDescription($value)
 * @method static Builder|Event whereEnd($value)
 * @method static Builder|Event whereForceCalendarSync($value)
 * @method static Builder|Event whereId($value)
 * @method static Builder|Event whereImageId($value)
 * @method static Builder|Event whereInvolvesFood($value)
 * @method static Builder|Event whereIsEducational($value)
 * @method static Builder|Event whereIsExternal($value)
 * @method static Builder|Event whereIsFeatured($value)
 * @method static Builder|Event whereLocation($value)
 * @method static Builder|Event whereSecret($value)
 * @method static Builder|Event whereStart($value)
 * @method static Builder|Event whereSummary($value)
 * @method static Builder|Event whereTitle($value)
 * @method static Builder|Event whereUpdatedAt($value)
 * @method static Builder|Event newModelQuery()
 * @method static Builder|Event newQuery()
 * @method static Builder|Event query()
 *
 * @mixin Eloquent
 */
class Event extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $table = 'events';

    protected $guarded = ['id'];

    protected $hidden = ['created_at', 'updated_at', 'secret', 'image_id', 'deleted_at', 'update_sequence'];

    protected $with = ['category', 'activity'];

    protected $appends = ['is_future', 'formatted_date'];

    #[Override]
    protected function casts(): array
    {
        return [
            'deleted_at' => 'datetime',
        ];
    }

    public function getPublicId(): string
    {
        return Hashids::connection('event')->encode($this->id);
    }

    public static function fromPublicId(string $public_id): Event
    {
        return self::query()->findOrFail(self::getIdFromPublicId($public_id));
    }

    public static function getIdFromPublicId($public_id)
    {
        $id = Hashids::connection('event')->decode($public_id);

        return count($id) > 0 ? $id[0] : 0;
    }

    public function committee(): BelongsTo
    {
        return $this->belongsTo(Committee::class);
    }

    public function mayViewEvent(?User $user): bool
    {
        // board may always view events
        if ($user?->can('board')) {
            return true;
        }

        // only show secret events if the user is participating, helping or organising
        if ($this->secret && ($user instanceof User && $this->activity && ($this->activity->isParticipating($user) || $this->activity->isHelping($user) || $this->activity->isOrganising($user)))) {
            return true;
        }

        // show non-secret events only when published
        return ! $this->secret && (! $this->publication || $this->isPublished());
    }

    public static function getEventBlockQuery(): Builder
    {
        return Event::query()
            ->orderBy('start')
            ->with('image')
            ->with('activity', static function ($e) {
                $e->withExists(['backupUsers as user_has_backup_participation' => static function ($q) {
                    $q->where('user_id', Auth::id());
                }, 'helpingParticipations as user_has_helper_participation' => static function ($q) {
                    $q->where('user_id', Auth::id());
                }, 'participation as user_has_participation' => static function ($q) {
                    $q->where('user_id', Auth::id())
                        ->whereNull('committees_activities_id');
                },
                ])->withCount([
                    'users',
                ]);
            })->withExists(['tickets as user_has_tickets' => static function ($q) {
                $q->whereHas('purchases', static function ($q) {
                    $q->where('user_id', Auth::id());
                });
            }]);
    }

    public function isPublished(): bool
    {
        return $this->publication < Carbon::now()->timestamp;
    }

    public function image(): BelongsTo
    {
        return $this->belongsTo(StorageEntry::class);
    }

    public function activity(): HasOne
    {
        return $this->hasOne(Activity::class);
    }

    public function videos(): HasMany
    {
        return $this->hasMany(Video::class);
    }

    public function albums(): HasMany
    {
        return $this->hasMany(PhotoAlbum::class, 'event_id');
    }

    public function tickets(): HasMany
    {
        return $this->hasMany(Ticket::class, 'event_id');
    }

    public function dinnerforms(): HasMany
    {
        return $this->hasMany(Dinnerform::class, 'event_id');
    }

    public function category(): BelongsTo
    {
        return $this->BelongsTo(EventCategory::class);
    }

    /**
     * @return bool Whether the user is organising the activity.
     */
    public function isOrganising(User $user): bool
    {
        return $this->committee && $user->isInCommittee($this->committee);
    }

    /** @return Collection|TicketPurchase[] */
    public function getTicketPurchasesFor(User $user): Collection|array
    {
        return TicketPurchase::query()
            ->where('user_id', $user->id)
            ->whereHas('ticket', function ($q) {
                $q->where('event_id', $this->id);
            })
            ->get();
    }

    public function current(): bool
    {
        return $this->start < date('U') && $this->end > date('U');
    }

    public function over(): bool
    {
        return $this->end < date('U');
    }

    /**
     * @param  string  $long_format  Format when timespan is larger than 24 hours.
     * @param  string  $short_format  Format when timespan is smaller than 24 hours.
     * @param  string  $combiner  Character to separate start and end time.
     * @return string Timespan text in given format
     */
    public function generateTimespanText(string $long_format, string $short_format, string $combiner): string
    {
        return date($long_format, $this->start).' '.$combiner.' '.(
            (($this->end - $this->start) < 3600 * 24)
                ?
                date($short_format, $this->end)
                :
                date($long_format, $this->end)
        );
    }

    /**
     * @return bool Whether the user is an admin of the event.
     */
    public function isEventAdmin(User $user): bool
    {
        if ($user->can('board')) {
            return true;
        }

        if ($this->committee?->isMember($user)) {
            return true;
        }

        return $this->isEventEro($user);
    }

    /**
     * @return bool Whether the user is an ERO at the event
     */
    public function isEventEro(User $user): bool
    {
        if ($user->can('board')) {
            return true;
        }

        if (date('U') > $this->end) {
            return false;
        }

        if (! $this->activity) {
            return false;
        }

        $eroHelping = HelpingCommittee::query()
            ->where('activity_id', $this->activity->id)
            ->where('committee_id', Config::integer('proto.committee.ero'))->first();
        if ($eroHelping) {
            return ActivityParticipation::query()
                ->where('activity_id', $this->activity->id)
                ->where('committees_activities_id', $eroHelping->id)
                ->where('user_id', $user->id)->count() > 0;
        }

        return false;
    }

    /**
     * @return bool Whether the user has bought a ticket for the event.
     */
    public function hasBoughtTickets(User $user): bool
    {
        return $this->getTicketPurchasesFor($user)->count() > 0;
    }

    public function allUsers(): SupportCollection
    {
        $users = collect();
        foreach ($this->tickets as $ticket) {
            $users = $users->merge($ticket->getUsers());
        }

        if ($this->activity) {
            $users = $users->merge($this->activity->allUsers->sort(static function ($a, $b): int {
                return (int) isset($a->pivot->committees_activities_id);
                // prefer helper participation registration
            })->unique());
        }

        return $users->sort(static fn ($a, $b): int => strcmp($a->name, $b->name));
    }

    // recounts the unique users on an event to make the fetching of the event_block way faster
    public function updateUniqueUsersCount(): void
    {
        $allUserIds = collect();
        foreach ($this->tickets as $ticket) {
            if ($ticket->show_participants) {
                $allUserIds = $allUserIds->merge($ticket->getUsers()->pluck('id'));
            }
        }

        if ($this->activity) {
            $allUserIds = $allUserIds->merge($this->activity->users->pluck('id'));
        }

        $this->unique_users_count = $allUserIds->unique()->count();
        $this->save();
    }

    /** @return string[] */
    public function getAllEmails(): array
    {
        return $this->allUsers()->pluck('email')->toArray();
    }

    public function shouldShowDietInfo(): bool
    {
        return $this->involves_food && $this->end > strtotime('-1 week');
    }

    public function getIsFutureAttribute(): bool
    {
        return date('U') < $this->start;
    }

    public function getFormattedDateAttribute(): object
    {
        return (object) [
            'simple' => date('M d, Y', $this->start),
            'year' => date('Y', $this->start),
            'month' => date('M Y', $this->start),
            'time' => date('H:i', $this->start),
        ];
    }

    #[Override]
    protected static function boot(): void
    {
        parent::boot();

        self::updating(static function ($event) {
            $event->update_sequence++;
        });
    }
}
