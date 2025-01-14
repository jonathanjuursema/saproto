<?php

namespace App\Models;

use App\Enums\MembershipTypeEnum;
use Carbon;
use Eloquent;
use Exception;
use Illuminate\Auth\Passwords\CanResetPassword;
use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;
use Illuminate\Contracts\Auth\CanResetPassword as CanResetPasswordContract;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Query\Builder as QueryBuilder;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Laravel\Passport\Client;
use Laravel\Passport\HasApiTokens;
use Override;
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
 * @property string|null $discord_id
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

    public function getPublicId(): ?string
    {
        return $this->is_member ? $this->member->proto_username : null;
    }

    public static function fromPublicId(string $public_id): ?User
    {
        return User::query()->whereHas('member', static function ($query) use ($public_id) {
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
        return ! (
            $this->password ||
            $this->edu_username ||
            strtotime($this->created_at) > strtotime('-1 hour') ||
            Member::withTrashed()->where('user_id', $this->id)->first() ||
            Bank::query()->where('user_id', $this->id)->first() ||
            Address::query()->where('user_id', $this->id)->first() ||
            OrderLine::query()->where('user_id', $this->id)->count() > 0 ||
            CommitteeMembership::withTrashed()->where('user_id', $this->id)->count() > 0 ||
            Feedback::query()->where('user_id', $this->id)->count() > 0 ||
            EmailListSubscription::query()->where('user_id', $this->id)->count() > 0 ||
            RfidCard::query()->where('user_id', $this->id)->count() > 0 ||
            PlayedVideo::query()->where('user_id', $this->id)->count() > 0 ||
            AchievementOwnership::query()->where('user_id', $this->id)->count() > 0
        );
    }

    public function photo(): BelongsTo
    {
        return $this->belongsTo(StorageEntry::class, 'image_id');
    }

    public function groups(): BelongsToMany
    {
        return $this->belongsToMany(Committee::class, 'committees_users')
            ->where(static function ($query) {
                $query->whereNull('committees_users.deleted_at')
                    ->orWhere('committees_users.deleted_at', '>', Carbon::now());
            })
            ->where('committees_users.created_at', '<', Carbon::now())
            ->withPivot(['id', 'role', 'edition'])
            ->withTimestamps()
            ->orderByPivot('created_at', 'desc');
    }

    public function lists(): BelongsToMany
    {
        return $this->belongsToMany(EmailList::class, 'users_mailinglists', 'user_id', 'list_id');
    }

    public function achievements(): BelongsToMany
    {
        return $this->belongsToMany(Achievement::class, 'achievements_users')->withPivot(['id', 'description'])->withTimestamps()->orderByPivot('created_at', 'desc');
    }

    public function tickets(): BelongsToMany
    {
        return $this->belongsToMany(Ticket::class, 'ticket_purchases')->withPivot('id', 'created_at')->withTimestamps();
    }

    public function committees(): BelongsToMany
    {
        return $this->groups()->where('is_society', false);
    }

    public function societies(): BelongsToMany
    {
        return $this->groups()->where('is_society', true);
    }

    public function bank(): HasOne
    {
        return $this->hasOne(Bank::class);
    }

    public function address(): HasOne
    {
        return $this->hasOne(Address::class);
    }

    public function member(): HasOne
    {
        return $this->hasOne(Member::class);
    }

    public function welcomeMessage(): HasOne
    {
        return $this->hasOne(WelcomeMessage::class);
    }

    public function orderlines(): HasMany
    {
        return $this->hasMany(OrderLine::class);
    }

    public function tempadmin(): HasMany
    {
        return $this->hasMany(Tempadmin::class);
    }

    public function feedback(): HasMany
    {
        return $this->hasMany(Feedback::class);
    }

    public function rfid(): HasMany
    {
        return $this->hasMany(RfidCard::class);
    }

    public function tokens(): HasMany
    {
        return $this->hasMany(Token::class);
    }

    public function playedVideos(): HasMany
    {
        return $this->hasMany(PlayedVideo::class);
    }

    public function mollieTransactions(): HasMany
    {
        return $this->hasMany(MollieTransaction::class);
    }

    /**
     * Use this method instead of $user->photo->generate to bypass the "no profile" problem.
     *
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
     * @throws Exception
     */
    public function setPassword(string $password): void
    {
        // Update Laravel Password
        $this->password = Hash::make($password);
        $this->save();

        // Update DirectAdmin Password
        if ($this->is_member && ! App::environment('local')) {
            $da = new DirectAdmin;
            $da->connect(Config::string('directadmin.da-hostname'), Config::string('directadmin.da-port'));
            $da->set_login(Config::string('directadmin.da-username'), Config::string('directadmin.da-password'));
            $da->set_method('POST');
            $da->query('/CMD_API_POP', [
                'action' => 'modify',
                'domain' => Config::string('directadmin.da-domain'),
                'user' => $this->member->proto_username,
                'newuser' => $this->member->proto_username,
                'passwd' => $password,
                'passwd2' => $password,
                'quota' => 0, // Unlimited
                'limit' => 0, // Unlimited
            ]);
        }

        // Remove breach notification flag
        HashMapItem::query()->where('key', 'pwned-pass')->where('subkey', $this->id)->delete();
    }

    public function hasUnpaidOrderlines(): bool
    {
        return $this->orderlines()->unpayed()->exists();
    }

    /**
     * Returns whether the user is currently tempadmin.
     */
    public function isTempadmin(): bool
    {
        return $this->tempadmin()->where('start_at', '<', Carbon::now())->where('end_at', '>', Carbon::now())->exists();
    }

    /**
     * Returns whether the user is tempadmin between now and the end of the day.
     */
    public function isTempadminLaterToday(): bool
    {
        return $this->tempadmin()->where('start_at', '<', Carbon::now()->endOfDay())->where('end_at', '<', Carbon::now())->exists();
    }

    /**
     * @throws Exception
     */
    public function age(): int
    {
        return Carbon::parse($this->birthdate)->age;
    }

    public function isInCommittee(Committee $committee): bool
    {
        return $committee->users()->where('user_id', $this->id)->exists();
    }

    public function isInCommitteeBySlug(string $slug): bool
    {
        $committee = Committee::query()->where('slug', $slug)->first();

        return $committee && $this->isInCommittee($committee);
    }

    public function isActiveMember(): bool
    {
        return $this->committees()->exists();
    }

    public function withdrawals(): HasManyThrough
    {
        return $this->hasManyThrough(Withdrawal::class, OrderLine::class, 'user_id', 'id', 'id', 'payed_with_withdrawal')
            ->groupBy('withdrawals.id')
            ->orderBy('withdrawals.created_at', 'desc');
    }

    public function websiteUrl(): ?string
    {
        if (preg_match("/(?:http|https):\/\/.*/i", $this->website) === 1) {
            return $this->website;
        }

        return 'https://'.$this->website;
    }

    public function websiteDisplay(): ?string
    {
        if (preg_match("/(?:http|https):\/\/(.*)/i", $this->website, $matches) === 1) {
            return $matches[1];
        }

        return $this->website;
    }

    public function hasDiet(): bool
    {
        return strlen(str_replace(["\r", "\n", ' '], '', $this->diet)) > 0;
    }

    public function getProtoEmailAttribute(): ?string
    {
        return $this->is_member && $this->groups()->exists() ? $this->member->proto_username.'@'.config('proto.emaildomain') : null;
    }

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

    public function hasTFAEnabled(): bool
    {
        return $this->tfa_totp_key !== null;
    }

    public function generateNewPersonalKey(): void
    {
        $this->personal_key = Str::random(64);
        $this->save();
    }

    public function getPersonalKey(): ?string
    {
        if ($this->personal_key == null) {
            $this->generateNewPersonalKey();
        }

        return $this->personal_key;
    }

    public function generateNewToken(): Token
    {
        $token = new Token;
        $token->generate($this);

        return $token;
    }

    public function getToken(): Token
    {
        $token = $this->tokens->last() ?? $this->generateNewToken();
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
        $memberships['pending'] = Member::withTrashed()->where('user_id', '=', $this->id)->whereNull('deleted_at')->type(MembershipTypeEnum::PENDING)->get();
        $memberships['previous'] = Member::withTrashed()->where('user_id', '=', $this->id)->whereNotNull('deleted_at')->get();

        return $memberships;
    }

    public function getCalendarAlarm(): ?float
    {
        return $this->pref_calendar_alarm;
    }

    public function setCalendarAlarm(?float $hours): void
    {
        $hours = floatval($hours);
        $this->pref_calendar_alarm = ($hours > 0 ? $hours : null);
        $this->save();
    }

    public function getCalendarRelevantSetting(): bool
    {
        return $this->pref_calendar_relevant_only;
    }

    public function toggleCalendarRelevantSetting(): void
    {
        $this->pref_calendar_relevant_only = ! $this->pref_calendar_relevant_only;
        $this->save();
    }

    public function getCompletedProfileAttribute(): bool
    {
        return $this->birthdate !== null && $this->phone !== null;
    }

    /** @return bool Whether user has a current membership that is not pending. */
    public function getIsMemberAttribute(): bool
    {
        return $this->member && $this->member->membership_type !== MembershipTypeEnum::PENDING;
    }

    public function getSignedMembershipFormAttribute(): bool
    {
        return $this->member?->membershipForm !== null;
    }

    public function getIsProtubeAdminAttribute(): bool
    {
        if ($this->can('protube')) {
            return true;
        }

        return $this->isTempadmin();
    }

    public function getPhotoPreviewAttribute(): string
    {
        return $this->generatePhotoPath();
    }

    public function getIcalUrl(): string
    {
        return route('ical::calendar', ['personal_key' => $this->getPersonalKey()]);
    }

    public function getWelcomeMessageAttribute(): ?string
    {
        return WelcomeMessage::query()->where('user_id', $this->id)->first()?->message;
    }

    #[Override]
    protected function casts(): array
    {
        return [
            'deleted_at' => 'datetime',
        ];
    }
}
