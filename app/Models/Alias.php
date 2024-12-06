<?php

namespace App\Models;

use Carbon;
use Eloquent;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Config;

/**
 * Alias Model.
 *
 * @property int $id
 * @property string $alias
 * @property int|null $user_id
 * @property string|null $destination
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read User|null $user
 *
 * @method static Builder|Alias whereAlias($value)
 * @method static Builder|Alias whereCreatedAt($value)
 * @method static Builder|Alias whereDestination($value)
 * @method static Builder|Alias whereId($value)
 * @method static Builder|Alias whereUpdatedAt($value)
 * @method static Builder|Alias whereUserId($value)
 * @method static Builder|Alias newModelQuery()
 * @method static Builder|Alias newQuery()
 * @method static Builder|Alias query()
 *
 * @mixin Eloquent
 */
class Alias extends Model
{
    protected $table = 'alias';

    protected $guarded = ['id'];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function getEmailAttribute(): string
    {
        return $this->alias.'@'.Config::string('proto.emaildomain');
    }
}
