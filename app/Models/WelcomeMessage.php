<?php

namespace App\Models;

use Carbon;
use Eloquent;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Welcome Message Model.
 *
 * @property int $id
 * @property int $user_id
 * @property string $message
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read User $user
 *
 * @method static Builder|WelcomeMessage whereCreatedAt($value)
 * @method static Builder|WelcomeMessage whereId($value)
 * @method static Builder|WelcomeMessage whereMessage($value)
 * @method static Builder|WelcomeMessage whereUpdatedAt($value)
 * @method static Builder|WelcomeMessage whereUserId($value)
 * @method static Builder|WelcomeMessage newModelQuery()
 * @method static Builder|WelcomeMessage newQuery()
 * @method static Builder|WelcomeMessage query()
 *
 * @mixin Eloquent
 */
class WelcomeMessage extends Model
{
    use HasFactory;

    protected $table = 'user_welcome';

    protected $guarded = ['id'];

    /** @return BelongsTo */
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
