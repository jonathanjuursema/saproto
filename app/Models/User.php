<?php

namespace App\Models;

use Carbon;
use DateTime;
use Eloquent;
use Exception;
use Hash;
use Illuminate\Auth\Passwords\CanResetPassword;
use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;
use Illuminate\Contracts\Auth\CanResetPassword as CanResetPasswordContract;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Query\Builder as QueryBuilder;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Laravel\Passport\Client;
use Laravel\Passport\HasApiTokens;
use Solitweb\DirectAdmin\DirectAdmin;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Traits\HasRoles;

/**
 * User Model.
 *
 * @property int $id
 * @property string $name
 * @property string $calling_name
 * @property string $email
 * @property string|null $password
 * @property string|null $remember_token
 * @property int|null $image_id
 * @property string|null $birthdate
 * @property string|null $phone
 * @property string|null $diet
 * @property string|null $website
 * @property string $theme
 * @property bool $phone_visible
 * @property bool $address_visible
 * @property bool $receive_sms
 * @property bool $keep_protube_history
 * @property bool $show_birthday
 * @property bool $show_achievements
 * @property bool $profile_in_almanac
 * @property bool $show_omnomcom_total
 * @property bool $show_omnomcom_calories
 * @property bool $keep_omnomcom_history
 * @property bool $disable_omnomcom
 * @property bool $did_study_create
 * @property bool $did_study_itech
 * @property bool $signed_nda
 * @property bool $pref_calendar_relevant_only
 * @property float|null $pref_calendar_alarm
 * @property string|null $utwente_username
 * @property string|null $edu_username
 * @property string|null $utwente_department
 * @property string|null $tfa_totp_key
 * @property string|null $personal_key
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property Carbon|null $deleted_at
 * @property-read string|null $proto_email
 * @property-read bool $completed_profile
 * @property-read bool $is_member
 * @property-read bool $is_protube_admin
 * @property-read bool $photo_preview
 * @property-read bool $signed_membership_form
 * @property-read string|null $welcome_message
 * @property-read StorageEntry|null $photo
 * @property-read Address|null $address
 * @property-read Bank|null $bank
 * @property-read Member|null $member
 * @property-read Collection|Achievement[] $achievements
 * @property-read Collection|Client[] $clients
 * @property-read Collection|EmailList[] $lists
 * @property-read Collection|MollieTransaction[] $mollieTransactions
 * @property-read Collection|OrderLine[] $orderlines
 * @property-read Collection|Ticket[] $tickets
 * @property-read Collection|PlayedVideo[] $playedVideos
 * @property-read Collection|Feedback[] $feedback
 * @property-read Collection|RfidCard[] $rfid
 * @property-read Collection|Tempadmin[] $tempadmin
 * @property-read Collection|Token[] $tokens
 * @property-read Collection|Committee[] $committees
 * @property-read Collection|Role[] $roles
 * @property-read Collection|Permission[] $permissions
 * @property-read Collection|Committee[] $societies
 *
 * @method static bool|null forceDelete()
 * @method static QueryBuilder|User onlyTrashed()
 * @method static QueryBuilder|User withTrashed()
 * @method static QueryBuilder|User withoutTrashed()
 * @method static Builder|User role($roles, $guard = null)
 * @method static Builder|User whereAddressVisible($value)
 * @method static Builder|User whereBirthdate($value)
 * @method static Builder|User whereCallingName($value)
 * @method static Builder|User whereCreatedAt($value)
 * @method static Builder|User whereDeletedAt($value)
 * @method static Builder|User whereDidStudyCreate($value)
 * @method static Builder|User whereDidStudyItech($value)
 * @method static Builder|User whereDiet($value)
 * @method static Builder|User whereDisableOmnomcom($value)
 * @method static Builder|User whereEduUsername($value)
 * @method static Builder|User whereEmail($value)
 * @method static Builder|User whereId($value)
 * @method static Builder|User whereImageId($value)
 * @method static Builder|User whereKeepOmnomcomHistory($value)
 * @method static Builder|User whereKeepProtubeHistory($value)
 * @method static Builder|User whereName($value)
 * @method static Builder|User wherePassword($value)
 * @method static Builder|User wherePersonalKey($value)
 * @method static Builder|User wherePhone($value)
 * @method static Builder|User wherePhoneVisible($value)
 * @method static Builder|User wherePrefCalendarAlarm($value)
 * @method static Builder|User wherePrefCalendarRelevantOnly($value)
 * @method static Builder|User whereProfileInAlmanac($value)
 * @method static Builder|User whereReceiveSms($value)
 * @method static Builder|User whereRememberToken($value)
 * @method static Builder|User whereShowAchievements($value)
 * @method static Builder|User whereShowBirthday($value)
 * @method static Builder|User whereShowOmnomcomCalories($value)
 * @method static Builder|User whereShowOmnomcomTotal($value)
 * @method static Builder|User whereSignedNda($value)
 * @method static Builder|User whereTfaTotpKey($value)
 * @method static Builder|User whereTheme($value)
 * @method static Builder|User whereUpdatedAt($value)
 * @method static Builder|User whereUtwenteDepartment($value)
 * @method static Builder|User whereUtwenteUsername($value)
 * @method static Builder|User whereWebsite($value)
 * @method static Builder|User newModelQuery()
 * @method static Builder|User newQuery()
 * @method static Builder|User permission($permissions)
 * @method static Builder|User query()
 *
 * @mixin Eloquent
 */
class User extends Authenticatable implements AuthenticatableContract, CanResetPasswordContract
{
    use CanResetPassword;
    use HasApiTokens;
    use HasFactory;
    use HasRoles;
    use SoftDeletes;

    protected $table = 'users';

    protected $guarded = ['password', 'remember_token'];

    protected $with = ['member'];

    protected $appends = ['is_member', 'photo_preview', 'welcome_message', 'is_protube_admin'];

    protected $hidden = ['password', 'remember_token', 'personal_key', 'deleted_at', 'created_at', 'image_id', 'tfa_totp_key', 'updated_at', 'diet'];

    protected $casts = [
        'deleted_at' => 'datetime',
    ];

    /** @return string|null */
    public function getPublicId(): ?string
    {
        return $this->is_member ? $this->member->proto_username : null;
    }

    /**
     * @param string $public_id
     * @return User|null
     */
    public static function fromPublicId(string $public_id): User|null
    {
        return User::whereHas('member', function ($query) use ($public_id) {
            $query->where('proto_username', $public_id);
        })->first();
    }

    /**
     * **IMPORTANT!** IF YOU ADD ANY RELATION TO A USER IN ANOTHER MODEL, DON'T FORGET TO UPDATE THIS METHOD.
     *
     * @return bool whether the user is stale (not in use, can really be deleted safely).
     */
    public function isStale(): bool
    {
        return !(
            $this->password ||
            $this->edu_username ||
            strtotime($this->created_at) > strtotime('-1 hour') ||
            Member::withTrashed()->where('user_id', $this->id)->first() ||
            Bank::where('user_id', $this->id)->first() ||
            Address::where('user_id', $this->id)->first() ||
            OrderLine::where('user_id', $this->id)->count() > 0 ||
            CommitteeMembership::withTrashed()->where('user_id', $this->id)->count() > 0 ||
            Feedback::where('user_id', $this->id)->count() > 0 ||
            EmailListSubscription::where('user_id', $this->id)->count() > 0 ||
            RfidCard::where('user_id', $this->id)->count() > 0 ||
            PlayedVideo::where('user_id', $this->id)->count() > 0 ||
            AchievementOwnership::where('user_id', $this->id)->count() > 0
        );
    }

    /** @return BelongsTo */
    public function photo(): BelongsTo
    {
        return $this->belongsTo(StorageEntry::class, 'image_id');
    }

    /** @return BelongsToMany */
    public function groups(): BelongsToMany
    {
        return $this->belongsToMany(Committee::class, 'committees_users')
            ->where(function ($query) {
                $query->whereNull('committees_users.deleted_at')
                    ->orWhere('committees_users.deleted_at', '>', Carbon::now());
            })
            ->where('committees_users.created_at', '<', Carbon::now())
            ->withPivot(['id', 'role', 'edition', 'created_at', 'deleted_at'])
            ->withTimestamps()
            ->orderBy('pivot_created_at', 'desc');
    }

    /** @return BelongsToMany */
    public function lists(): BelongsToMany
    {
        return $this->belongsToMany(EmailList::class, 'users_mailinglists', 'user_id', 'list_id');
    }

    /** @return BelongsToMany */
    public function achievements(): BelongsToMany
    {
        return $this->belongsToMany(Achievement::class, 'achievements_users')->withPivot(['id', 'description'])->withTimestamps()->orderBy('pivot_created_at', 'desc');
    }

    public function tickets(): BelongsToMany
    {
        return $this->belongsToMany(Ticket::class, 'ticket_purchases')->withPivot('id', 'created_at')->withTimestamps();
    }

    /** @return BelongsToMany */
    public function committees(): BelongsToMany
    {
        return $this->groups()->where('is_society', false);
    }

    /** @return BelongsToMany */
    public function societies(): BelongsToMany
    {
        return $this->groups()->where('is_society', true);
    }

    /** @return HasOne */
    public function bank(): HasOne
    {
        return $this->hasOne(Bank::class);
    }

    /** @return HasOne */
    public function address(): HasOne
    {
        return $this->hasOne(Address::class);
    }

    /** @return HasOne */
    public function member(): HasOne
    {
        return $this->hasOne(Member::class);
    }

    public function welcomeMessage(): HasOne
    {
        return $this->hasOne(WelcomeMessage::class);
    }

    /** @return HasMany */
    public function orderlines(): HasMany
    {
        return $this->hasMany(OrderLine::class);
    }

    /** @return HasMany */
    public function tempadmin(): HasMany
    {
        return $this->hasMany(Tempadmin::class);
    }

    /** @return HasMany */
    public function feedback(): HasMany
    {
        return $this->hasMany(Feedback::class);
    }

    /** @return HasMany */
    public function rfid(): HasMany
    {
        return $this->hasMany(RfidCard::class);
    }

    /** @return HasMany */
    public function tokens(): HasMany
    {
        return $this->hasMany(Token::class);
    }

    /** @return HasMany */
    public function playedVideos(): HasMany
    {
        return $this->hasMany(PlayedVideo::class);
    }

    /** @return HasMany */
    public function mollieTransactions(): HasMany
    {
        return $this->hasMany(MollieTransaction::class);
    }

    /**
     * Use this method instead of $user->photo->generate to bypass the "no profile" problem.
     *
     * @param int $w
     * @param int $h
     * @return string Path to a resized version of someone's profile picture.
     */
    public function generatePhotoPath(int $w = 100, int $h = 100): string
    {
        if ($this->photo) {
            return $this->photo->generateImagePath($w, $h);
        }

        return asset('images/default-avatars/other.png');
    }

    /**
     * @param string $password
     *
     * @throws Exception
     */
    public function setPassword(string $password): void
    {
        // Update Laravel Password
        $this->password = Hash::make($password);
        $this->save();

        // Update DirectAdmin Password
        if ($this->is_member) {
            $da = new DirectAdmin();
            $da->connect(getenv('DA_HOSTNAME'), getenv('DA_PORT'));
            $da->set_login(getenv('DA_USERNAME'), getenv('DA_PASSWORD'));
            $da->set_method('POST');
            $da->query('/CMD_API_POP', [
                'action' => 'modify',
                'domain' => env('DA_DOMAIN'),
                'user' => $this->member->proto_username,
                'newuser' => $this->member->proto_username,
                'passwd' => $password,
                'passwd2' => $password,
                'quota' => 0, // Unlimited
                'limit' => 0, // Unlimited
            ]);
        }

        // Remove breach notification flag
        HashMapItem::where('key', 'pwned-pass')->where('subkey', $this->id)->delete();
    }

    /** @return bool */
    public function hasUnpaidOrderlines(): bool
    {
        foreach ($this->orderlines as $orderline) {
            if (!$orderline->isPayed()) {
                return true;
            }
            if ($orderline->orderline && $orderline->withdrawal->id !== 1 && !$orderline->withdrawal->closed) {
                return true;
            }
        }

        return false;
    }

    /**
     * Returns whether the user is currently tempadmin.
     */
    public function isTempadmin(): bool
    {
        foreach ($this->tempadmin as $tempadmin) {
            if (Carbon::now()->between(Carbon::parse($tempadmin->start_at), Carbon::parse($tempadmin->end_at))) {
                return true;
            }
        }
        return false;
    }

    /**
     * Returns whether the user is tempadmin between now and the end of the day.
     */
    public function isTempadminLaterToday(): bool
    {
        $now = Carbon::now();
        foreach ($this->tempadmin as $tempadmin) {
            // Skip all 'past' tempadmin entries
            if (Carbon::parse($tempadmin->end_at)->isBefore($now)) {
                continue;
            }

            if ($now->between(Carbon::parse($tempadmin->start_at)->startOfDay(), Carbon::parse($tempadmin->end_at)->endOfDay())) {
                return true;
            }
        }

        return false;
    }

    /**
     * @return int
     *
     * @throws Exception
     */
    public function age(): int
    {
        return Carbon::instance(new DateTime($this->birthdate))->age;
    }

    /**
     * @param Committee $committee
     * @return bool
     */
    public function isInCommittee(Committee $committee): bool
    {
        return $this->committees()->where('committees.id', $committee->id)->exists();
    }

    /**
     * @param string $slug
     * @return bool
     */
    public function isInCommitteeBySlug(string $slug): bool
    {
        $committee = Committee::where('slug', $slug)->first();

        return $committee && $this->isInCommittee($committee);
    }

    /** @return bool */
    public function isActiveMember(): bool
    {
        return $this->committees()->exists();
    }

    /**
     * @param int $limit
     * @return Withdrawal[]
     */
    public function withdrawals(int $limit = 0): array
    {
        $withdrawals = [];
        foreach (Withdrawal::orderBy('date', 'desc')->get() as $withdrawal) {
            if ($withdrawal->orderlinesForUser($this)->count() > 0) {
                $withdrawals[] = $withdrawal;
                if ($limit > 0 && count($withdrawals) > $limit) {
                    break;
                }
            }
        }

        return $withdrawals;
    }

    /** @return string|null */
    public function websiteUrl(): ?string
    {
        if (preg_match("/(?:http|https):\/\/.*/i", $this->website) === 1) {
            return $this->website;
        }

        return 'https://' . $this->website;
    }

    /** @return string|null */
    public function websiteDisplay(): ?string
    {
        if (preg_match("/(?:http|https):\/\/(.*)/i", $this->website, $matches) === 1) {
            return $matches[1];
        }

        return $this->website;
    }

    /** @return bool */
    public function hasDiet(): bool
    {
        return strlen(str_replace(["\r", "\n", ' '], '', $this->diet)) > 0;
    }

    /**
     * @return string|null
     */
    public function getProtoEmailAttribute(): ?string
    {
        return $this->is_member && $this->groups()->exists() ? $this->member->proto_username . '@' . config('proto.emaildomain') : null;
    }

    /** @return string */
    public function getDisplayEmail(): string
    {
        return $this->proto_email ?? $this->email;
    }

    /**
     * This method returns a guess of the system for whether this user is a first year student.
     * Note that this is a _GUESS_. There is no way for us to know sure without manually setting a flag on each user.
     *
     * @return bool Whether the system thinks the user is a first year.
     */
    public function isFirstYear(): bool
    {
        return $this->is_member
            && Carbon::createFromTimestamp($this->member->created_at)->age < 1
            && $this->did_study_create;
    }

    /** @return bool */
    public function hasTFAEnabled(): bool
    {
        return $this->tfa_totp_key !== null;
    }

    /** @return void */
    public function generateNewPersonalKey(): void
    {
        $this->personal_key = str_random(64);
        $this->save();
    }

    /**
     * @return string|null
     */
    public function getPersonalKey(): ?string
    {
        if ($this->personal_key == null) {
            $this->generateNewPersonalKey();
        }

        return $this->personal_key;
    }

    /** @return Token */
    public function generateNewToken(): Token
    {
        $token = new Token();
        $token->generate($this);

        return $token;
    }

    /** @return Token */
    public function getToken(): Token
    {
        if (count($this->tokens) > 0) {
            $token = $this->tokens->last();
        } else {
            $token = $this->generateNewToken();
        }
        $token->touch();

        return $token;
    }

    /** Removes user's birthdate and phone number. */
    public function clearMemberProfile(): void
    {
        $this->birthdate = null;
        $this->phone = null;
        $this->save();
    }

    /** @return array<string, Collection<Member>> */
    public function getMemberships(): array
    {
        $memberships['pending'] = Member::withTrashed()->where('user_id', '=', $this->id)->where('deleted_at', '=', null)->where('is_pending', '=', true)->get();
        $memberships['previous'] = Member::withTrashed()->where('user_id', '=', $this->id)->where('deleted_at', '!=', null)->get();

        return $memberships;
    }

    /** @return float|null */
    public function getCalendarAlarm(): ?float
    {
        return $this->pref_calendar_alarm;
    }

    /** @param float|null $hours */
    public function setCalendarAlarm($hours): void
    {
        $hours = floatval($hours);
        $this->pref_calendar_alarm = ($hours > 0 ? $hours : null);
        $this->save();
    }

    /** @return bool */
    public function getCalendarRelevantSetting(): bool
    {
        return $this->pref_calendar_relevant_only;
    }

    /** @return void */
    public function toggleCalendarRelevantSetting(): void
    {
        $this->pref_calendar_relevant_only = !$this->pref_calendar_relevant_only;
        $this->save();
    }

    /** @return string */
    public function getIcalUrl(): string
    {
        return route('ical::calendar', ['personal_key' => $this->getPersonalKey()]);
    }

    /** @return bool */
    public function getCompletedProfileAttribute(): bool
    {
        return $this->birthdate !== null && $this->phone !== null;
    }

    /** @return bool Whether user has a current membership that is not pending. */
    public function getIsMemberAttribute(): bool
    {
        return $this->member && !$this->member->is_pending;
    }

    /** @return bool */
    public function getSignedMembershipFormAttribute(): bool
    {
        return $this->member?->membershipForm !== null;
    }

    /** @return bool */
    public function getIsProtubeAdminAttribute(): bool
    {
        return $this->can('protube') || $this->isTempadmin();
    }

    /** @return string */
    public function getPhotoPreviewAttribute(): string
    {
        return $this->generatePhotoPath();
    }

    public function getWelcomeMessageAttribute(): ?string
    {
        return $this->welcomeMessage()->get()?->message;
    }
}
