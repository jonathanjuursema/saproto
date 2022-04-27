<?php

namespace Proto\Models;

use Carbon;
use DateTime;
use Eloquent;
use Exception;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Spatie\Permission\Models\Permission;

/**
 * Password Entry Model.
 *
 * @property int $id
 * @property int $permission_id
 * @property string|null $description
 * @property string|null $username
 * @property string|null $password
 * @property string|null $url
 * @property string|null $note
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read Permission $permission
 * @method static Builder|PasswordEntry whereCreatedAt($value)
 * @method static Builder|PasswordEntry whereDescription($value)
 * @method static Builder|PasswordEntry whereId($value)
 * @method static Builder|PasswordEntry whereNote($value)
 * @method static Builder|PasswordEntry wherePassword($value)
 * @method static Builder|PasswordEntry wherePermissionId($value)
 * @method static Builder|PasswordEntry whereUpdatedAt($value)
 * @method static Builder|PasswordEntry whereUrl($value)
 * @method static Builder|PasswordEntry whereUsername($value)
 * @mixin Eloquent
 */
class PasswordEntry extends Model
{
    protected $table = 'passwordstore';

    protected $guarded = ['id'];

    /** @return BelongsTo|Permission */
    public function permission()
    {
        return $this->belongsTo('Spatie\Permission\Models\Permission', 'permission_id');
    }

    /** @return bool */
    public function canAccess(User $user)
    {
        $permission = $this->permission;
        return $permission && $user->can($permission->name);
    }

    /**
     * @return float|int
     * @throws Exception
     */
    public function age()
    {
        return Carbon::instance(new DateTime($this->updated_at))->diffInMonths(Carbon::now());
    }
}
