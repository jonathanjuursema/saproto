<?php

namespace App\Models;

use Carbon;
use Eloquent;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Token Model.
 *
 * @property int $id
 * @property int $user_id
 * @property string $token
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read User|null $user
 *
 * @method static Builder|Token whereCreatedAt($value)
 * @method static Builder|Token whereId($value)
 * @method static Builder|Token whereToken($value)
 * @method static Builder|Token whereUpdatedAt($value)
 * @method static Builder|Token whereUserId($value)
 * @method static Builder|Token newModelQuery()
 * @method static Builder|Token newQuery()
 * @method static Builder|Token query()
 *
 * @mixin Eloquent
 */
class Token extends Model
{
    protected $table = 'tokens';

    protected $guarded = ['id'];

    /** @return BelongsTo */
    public function user()
    {
        return $this->belongsTo(\App\Models\User::class, 'user_id');
    }

    /**
     * @param  User  $user
     * @return Token
     */
    public function generate($user)
    {
        $this->user_id = $user->id;
        $this->token = uniqid();
        $this->save();

        return $this;
    }
}
