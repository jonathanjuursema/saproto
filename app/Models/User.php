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
 * @property-read bool $completed_profile
 * @property-read bool $is_member
 * @property-read bool $is_protube_admin
 * @property-read bool $photo_preview
 * @property-read bool $signed_membership_form
 * @property-read string|null $welcome_message
 * @property-read Photo|null $photo
 * @property-read Address|null $address
 * @property-read Bank|null $bank
 * @property-read Member|null $member
 * @property-read HelperReminder|null $helperReminderSubscriptions
 * @property-read Collection|Achievement[] $achievements
 * @property-read Collection|Client[] $clients
 * @property-read Collection|EmailList[] $lists
 * @property-read Collection|MollieTransaction[] $mollieTransactions
 * @property-read Collection|OrderLine[] $orderlines
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

    protected $appends = ['is_member', 'photo_preview', 'welcome_message', 'is_protube_admin', 'is_active_member', 'bank'];

    protected $hidden = ['password', 'remember_token', 'personal_key', 'deleted_at', 'created_at', 'image_id', 'tfa_totp_key', 'updated_at', 'diet'];


    protected $casts = [
        'deleted_at' => 'datetime',
        'show_birthday' => 'boolean',
        'phone_visible' => 'boolean',
        'receive_sms' => 'boolean',
        'show_omnomcom_total' => 'boolean',
        'show_omnomcom_calories' => 'boolean',
        'disable_omnomcom' => 'boolean',
        'keep_omnomcom_history' => 'boolean',
        'show_achievements' => 'boolean',
        'profile_in_almanac' => 'boolean',
        'is_member' => 'boolean',
    ];

    /** @return string|null */
    public function getPublicId()
    {
        return $this->is_member ? $this->member->proto_username : null;
    }

    /**
     * @param string $public_id
     * @return mixed|User|null
     */
    public static function fromPublicId($public_id)
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
    public function isStale()
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
    public function photo()
    {
        return $this->belongsTo('App\Models\Photo', 'photo_id');
    }

    public function getPhotoPreviewAttribute()
    {
        return $this->generateSmallPhotoPath();
    }

    /** @return BelongsTo */
    public function helperReminderSubscriptions()
    {
        return $this->belongsTo('App\Models\HelperReminder');
    }

    /** @return BelongsToMany */
    private function getGroups()
    {
        return $this->belongsToMany('App\Models\Committee', 'committees_users')
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
    public function lists()
    {
        return $this->belongsToMany('App\Models\EmailList', 'users_mailinglists', 'user_id', 'list_id');
    }

    /** @return BelongsToMany */
    public function achievements()
    {
        return $this->belongsToMany('App\Models\Achievement', 'achievements_users')->withPivot(['id', 'description'])->withTimestamps()->orderBy('pivot_created_at', 'desc');
    }

    /** @return BelongsToMany */
    public function committees()
    {
        return $this->getGroups()->where('is_society', false);
    }

    /** @return BelongsToMany */
    public function societies()
    {
        return $this->getGroups()->where('is_society', true);
    }

    /** @return HasOne */
    public function member(): HasOne
    {
        return $this->hasOne('App\Models\Member');
    }

    /** @return HasOne */
    public function bank(): HasOne
    {
        return $this->hasOne('App\Models\Bank');
    }

    /** @return Bank | null */
    public function getBankAttribute(): Bank|null
    {
        return $this->bank()->first();
    }

    /** @return HasOne */
    public function address()
    {
        return $this->hasOne('App\Models\Address');
    }

    /** @return HasMany */
    public function orderlines()
    {
        return $this->hasMany('App\Models\OrderLine');
    }

    /** @return HasMany */
    public function tempadmin()
    {
        return $this->hasMany('App\Models\Tempadmin');
    }

    /** @return HasMany */
    public function feedback()
    {
        return $this->hasMany('App\Models\Feedback');
    }

    /** @return HasMany */
    public function rfid()
    {
        return $this->hasMany('App\Models\RfidCard');
    }

    /** @return HasMany */
    public function tokens()
    {
        return $this->hasMany('App\Models\Token');
    }

    /** @return HasMany */
    public function playedVideos()
    {
        return $this->hasMany('App\Models\PlayedVideo');
    }

    /** @return HasMany */
    public function mollieTransactions()
    {
        return $this->hasMany('App\Models\MollieTransaction');
    }

    /**
     * Use this method instead of $user->photo->generate to bypass the "no profile" problem.
     *
     * @return string Path to a resized version of someone's profile picture.
     */
    public function generateTinyPhotoPath()
    {
        if ($this->photo) {
            return $this->photo->getTinyUrl();
        } else {
            return asset('images/default-avatars/other.png');
        }
    }

    /**
     * Use this method instead of $user->photo->generate to bypass the "no profile" problem.
     *
     * @return string Path to a resized version of someone's profile picture.
     */
    public function generateSmallPhotoPath()
    {
        if ($this->photo) {
            return $this->photo->getSmallUrl();
        } else {
            return asset('images/default-avatars/other.png');
        }
    }

    /**
     * Use this method instead of $user->photo->generate to bypass the "no profile" problem.
     *
     * @return string Path to a resized version of someone's profile picture.
     */
    public function generateMediumPhotoPath()
    {
        if ($this->photo) {
            return $this->photo->getMediumUrl();
        } else {
            return asset('images/default-avatars/other.png');
        }
    }

    /**
     * Use this method instead of $user->photo->generate to bypass the "no profile" problem.
     *
     * @return string Path to a resized version of someone's profile picture.
     */
    public function generateLargePhotoPath()
    {
        if ($this->photo) {
            return $this->photo->getLargeUrl();
        } else {
            return asset('images/default-avatars/other.png');
        }
    }

    /**
     * @param string $password
     *
     * @throws Exception
     */
    public function setPassword($password)
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
    public function hasUnpaidOrderlines()
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

    /** @return bool */
    public function isTempadmin()
    {
        foreach ($this->tempadmin as $tempadmin) {
            if (Carbon::now()->between(Carbon::parse($tempadmin->start_at), Carbon::parse($tempadmin->end_at))) {
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
    public function age()
    {
        return Carbon::instance(new DateTime($this->birthdate))->age;
    }

    /**
     * @param Committee $committee
     * @return bool
     */
    public function isInCommittee($committee)
    {
        return in_array($this->id, $committee->users->pluck('id')->toArray());
    }

    /**
     * @param string $slug
     * @return bool
     */
    public function isInCommitteeBySlug($slug)
    {
        $committee = Committee::where('slug', $slug)->first();

        return $committee && $this->isInCommittee($committee);
    }

    /** @return bool */
    public function isActiveMember()
    {
        return count(
                CommitteeMembership::withTrashed()
                    ->where('user_id', $this->id)
                    ->where('created_at', '<', date('Y-m-d H:i:s'))
                    ->where(function ($q) {
                        $q->whereNull('deleted_at')
                            ->orWhere('deleted_at', '>', date('Y-m-d H:i:s'));
                    })
                    ->with('committee')
                    ->get()
                    ->where('committee.is_society', false)
            ) > 0;
    }

    /**
     * @return bool
     */
    public function getIsActiveMemberAttribute(): bool
    {
        return $this->isActiveMember();
    }

    /**
     * @param int $limit
     * @return Withdrawal[]
     */
    public function withdrawals($limit = 0)
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
    public function websiteUrl()
    {
        if (preg_match("/(?:http|https):\/\/.*/i", $this->website) === 1) {
            return $this->website;
        } else {
            return 'https://' . $this->website;
        }
    }

    /** @return string|null */
    public function websiteDisplay()
    {
        if (preg_match("/(?:http|https):\/\/(.*)/i", $this->website, $matches) === 1) {
            return $matches[1];
        } else {
            return $this->website;
        }
    }

    /** @return bool */
    public function hasDiet()
    {
        return strlen(str_replace(["\r", "\n", ' '], '', $this->diet)) > 0;
    }

    /** @return string */
    public function getDisplayEmail()
    {
        return ($this->is_member && $this->isActiveMember()) ? sprintf('%s@%s', $this->member->proto_username, config('proto.emaildomain')) : $this->email;
    }

    /**
     * This method returns a guess of the system for whether this user is a first year student.
     * Note that this is a _GUESS_. There is no way for us to know sure without manually setting a flag on each user.
     *
     * @return bool Whether the system thinks the user is a first year.
     */
    public function isFirstYear()
    {
        return $this->is_member
            && Carbon::createFromTimestamp($this->member->created_at)->age < 1
            && $this->did_study_create;
    }

    /** @return bool */
    public function hasTFAEnabled()
    {
        return $this->tfa_totp_key !== null;
    }

    /** @return void */
    public function generateNewPersonalKey()
    {
        $this->personal_key = str_random(64);
        $this->save();
    }

    /** @return string */
    public function getPersonalKey()
    {
        if ($this->personal_key == null) {
            $this->generateNewPersonalKey();
        }

        return $this->personal_key;
    }

    /** @return Token */
    public function generateNewToken()
    {
        $token = new Token();
        $token->generate($this);

        return $token;
    }

    /** @return Token */
    public function getToken()
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
    public function clearMemberProfile()
    {
        $this->birthdate = null;
        $this->phone = null;
        $this->save();
    }

    /** @return array<string, Collection<Member>> */
    public function getMemberships()
    {
        $baseQuery = Member::withTrashed()->where('user_id', '=', $this->id)->with('user');
        $memberships['pending'] = $baseQuery->where('deleted_at', '=', null)->where('is_pending', '=', true)->get();
        $memberships['previous'] = $baseQuery->where('deleted_at', '!=', null)->get();

        return $memberships;
    }

    /** @return float|null */
    public function getCalendarAlarm()
    {
        return $this->pref_calendar_alarm;
    }

    /** @param float|null $hours */
    public function setCalendarAlarm($hours)
    {
        $hours = floatval($hours);
        $this->pref_calendar_alarm = ($hours > 0 ? $hours : null);
        $this->save();
    }

    /** @return bool */
    public function getCalendarRelevantSetting()
    {
        return $this->pref_calendar_relevant_only;
    }

    /** @return void */
    public function toggleCalendarRelevantSetting()
    {
        $this->pref_calendar_relevant_only = !$this->pref_calendar_relevant_only;
        $this->save();
    }

    /** @return bool */
    public function getCompletedProfileAttribute()
    {
        return $this->birthdate !== null && $this->phone !== null;
    }

    /** @return bool Whether user has a current membership that is not pending. */
    public function getIsMemberAttribute(): bool
    {
        return $this->member->exists && !$this->member->is_pending;
    }

    /** @return bool */
    public function getSignedMembershipFormAttribute()
    {
        return $this->member?->membershipForm !== null;
    }

    /** @return bool */
    public function getIsProtubeAdminAttribute()
    {
        return $this->can('protube') || $this->isTempadmin();
    }

    /** @return string */
    public function getIcalUrl()
    {
        return route('ical::calendar', ['personal_key' => $this->getPersonalKey()]);
    }

    /** @return string|null */
    public function getWelcomeMessageAttribute()
    {
        $welcomeMessage = WelcomeMessage::where('user_id', $this->id)->first();
        if ($welcomeMessage) {
            return $welcomeMessage->message;
        } else {
            return null;
        }
    }
}
