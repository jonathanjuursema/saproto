<?php

namespace App\Models;

use Eloquent;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;

/**
 * Password Reset Model.
 *
 * @property string $email
 * @property string $token
 * @property int $valid_to
 * @property-read User $user
 *
 * @method static Builder|PasswordReset whereEmail($value)
 * @method static Builder|PasswordReset whereToken($value)
 * @method static Builder|PasswordReset whereValidTo($value)
 * @method static Builder|PasswordReset newModelQuery()
 * @method static Builder|PasswordReset newQuery()
 * @method static Builder|PasswordReset query()
 *
 * @mixin Eloquent
 */
class PasswordReset extends Model
{
    protected $table = 'password_resets';

    protected $guarded = [];

    public $timestamps = false;

    /** @return HasOne */
    public function user()
    {
        return $this->hasOne(User::class, 'email', 'email');
    }
}
