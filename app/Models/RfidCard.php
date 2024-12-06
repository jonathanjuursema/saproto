<?php

namespace App\Models;

use Carbon;
use Eloquent;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Rfid Card Model.
 *
 * @property int $id
 * @property int $user_id
 * @property string $card_id
 * @property string|null $name
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read User|null $user
 *
 * @method static Builder|RfidCard whereCardId($value)
 * @method static Builder|RfidCard whereCreatedAt($value)
 * @method static Builder|RfidCard whereId($value)
 * @method static Builder|RfidCard whereName($value)
 * @method static Builder|RfidCard whereUpdatedAt($value)
 * @method static Builder|RfidCard whereUserId($value)
 * @method static Builder|RfidCard newModelQuery()
 * @method static Builder|RfidCard newQuery()
 * @method static Builder|RfidCard query()
 *
 * @mixin Eloquent
 */
class RfidCard extends Model
{
    protected $table = 'rfid';

    protected $guarded = ['id'];

    /** @return BelongsTo */
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
