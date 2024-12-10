<?php

namespace App\Models;

use Carbon;
use Eloquent;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Narrowcasting Item Model.
 *
 * @property int $id
 * @property string $name
 * @property int|null $image_id
 * @property int $campaign_start
 * @property int $campaign_end
 * @property int $slide_duration
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property string|null $youtube_id
 * @property-read StorageEntry|null $image
 *
 * @method static Builder|NarrowcastingItem whereCampaignEnd($value)
 * @method static Builder|NarrowcastingItem whereCampaignStart($value)
 * @method static Builder|NarrowcastingItem whereCreatedAt($value)
 * @method static Builder|NarrowcastingItem whereId($value)
 * @method static Builder|NarrowcastingItem whereImageId($value)
 * @method static Builder|NarrowcastingItem whereName($value)
 * @method static Builder|NarrowcastingItem whereSlideDuration($value)
 * @method static Builder|NarrowcastingItem whereUpdatedAt($value)
 * @method static Builder|NarrowcastingItem whereYoutubeId($value)
 * @method static Builder|NarrowcastingItem newModelQuery()
 * @method static Builder|NarrowcastingItem newQuery()
 * @method static Builder|NarrowcastingItem query()
 *
 * @mixin Eloquent
 */
class NarrowcastingItem extends Model
{
    protected $table = 'narrowcasting';

    protected $guarded = ['id'];

    public function image(): BelongsTo
    {
        return $this->belongsTo(StorageEntry::class);
    }
}
