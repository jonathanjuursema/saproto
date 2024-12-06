<?php

namespace App\Models;

use Carbon;
use Eloquent;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cookie;
use Illuminate\Support\Facades\Route;

/**
 * Announcement Model.
 *
 * @property int $id
 * @property string $description
 * @property string $content
 * @property string $display_from
 * @property string $display_till
 * @property int $show_style
 * @property bool $show_guests
 * @property bool $show_users
 * @property bool $show_members
 * @property bool $show_only_homepage
 * @property bool $show_only_new
 * @property bool $show_only_firstyear
 * @property bool $show_only_active
 * @property bool $show_as_popup
 * @property bool $show_by_time
 * @property bool $is_dismissable
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read string $bootstrap_style
 * @property-read bool $is_visible
 * @property-read string $hash_map_id
 * @property-read string $modal_id
 *
 * @method static Builder|Announcement whereContent($value)
 * @method static Builder|Announcement whereCreatedAt($value)
 * @method static Builder|Announcement whereDescription($value)
 * @method static Builder|Announcement whereDisplayFrom($value)
 * @method static Builder|Announcement whereDisplayTill($value)
 * @method static Builder|Announcement whereId($value)
 * @method static Builder|Announcement whereIsDismissable($value)
 * @method static Builder|Announcement whereShowAsPopup($value)
 * @method static Builder|Announcement whereShowGuests($value)
 * @method static Builder|Announcement whereShowMembers($value)
 * @method static Builder|Announcement whereShowOnlyActive($value)
 * @method static Builder|Announcement whereShowOnlyFirstyear($value)
 * @method static Builder|Announcement whereShowOnlyHomepage($value)
 * @method static Builder|Announcement whereShowOnlyNew($value)
 * @method static Builder|Announcement whereShowStyle($value)
 * @method static Builder|Announcement whereShowUsers($value)
 * @method static Builder|Announcement whereUpdatedAt($value)
 * @method static Builder|Announcement newModelQuery()
 * @method static Builder|Announcement newQuery()
 * @method static Builder|Announcement query()
 *
 * @mixin Eloquent
 */
class Announcement extends Model
{
    protected $table = 'announcements';

    protected $guarded = ['id'];

    public function getBootstrapStyleAttribute(): string
    {
        $map = [
            'primary',
            'info',
            'warning',
            'danger',
            'default',
        ];

        return $map[$this->show_style];
    }

    public function getIsVisibleAttribute(): string
    {
        $flags = [];

        $flags[] = $this->show_only_homepage ? 'Homepage only' : 'Entire site';

        if ($this->show_guests) {
            $flags[] = 'All guests';
        }

        if ($this->show_users) {
            $flags[] = $this->show_only_new ? 'New users' : 'All users';
        }

        if ($this->show_members) {
            if ($this->show_only_firstyear && $this->show_only_active) {
                $flags[] = 'First-year and active members';
            } elseif ($this->show_only_firstyear) {
                $flags[] = 'First-year members';
            } elseif ($this->show_only_active) {
                $flags[] = 'Active members';
            } else {
                $flags[] = 'All members';
            }
        }

        if ($this->show_as_popup) {
            $flags[] = 'Pop-up';
        } else {
            $flags[] = 'Banner';
            $flags[] = $this->is_dismissable ? 'Dismissable' : 'Persistent';
        }

        $flags[] = sprintf('Style: %s', $this->bootstrap_style);

        return implode(', ', $flags);
    }

    public function getHashMapIdAttribute(): string
    {
        return sprintf('dismiss-announcement-%s', $this->id);
    }

    public function getModalIdAttribute(): string
    {
        return sprintf('modal-announcement-%s', $this->id);
    }

    public function getShowByTimeAttribute(): bool
    {
        return strtotime($this->display_from) < date('U') && strtotime($this->display_till) > date('U');
    }

    /**
     * @param  null|User  $user
     */
    public function showForUser($user = null): bool
    {
        // Check for homepage.
        if ($this->show_only_homepage && Route::current()->getName() != 'homepage') {
            return false;
        }

        // Not within the scheduled timeframe.
        if (! $this->show_by_time) {
            return false;
        }

        // Verify user type requirement.
        if (
            ! ($this->show_guests && $user == null) &&
            ! ($this->show_users && $user != null && ! $user->is_member) &&
            ! ($this->show_members && $user != null && $user->is_member)
        ) {
            return false;
        }

        // Check new user requirement
        if ($this->show_only_new && $user != null && $user->created_at < $this->display_from) {
            return false;
        }

        // Check for first years.
        if ($this->show_only_firstyear && $user != null && $user->is_member && ! $user->isFirstYear()) {
            return false;
        }

        // Check for first years.
        if ($this->show_only_active && $user != null && $user->is_member && ! $user->isActiveMember()) {
            return false;
        }

        // Check if not already dismissed.
        if ($this->is_dismissable && Cookie::get($this->hash_map_id)) {
            return false;
        }

        if ($user == null) {
            return true;
        }

        if (! $this->is_dismissable) {
            return true;
        }

        return HashMapItem::query()->where('key', $this->hash_map_id)->where('subkey', $user->id)->count() <= 0;
    }

    /** @param  User|null  $user */
    public function dismissForUser($user = null): void
    {
        if ($user) {
            HashMapItem::query()->create(['key' => $this->hash_map_id, 'subkey' => $user->id]);
        } else {
            Cookie::queue($this->hash_map_id, true, 525600);
        }
    }
}
