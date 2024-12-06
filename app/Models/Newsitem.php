<?php

namespace App\Models;

use Barryvdh\LaravelIdeHelper\Eloquent;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Query\Builder as QueryBuilder;

/**
 * News Item Model.
 *
 * @property int $id
 * @property int $user_id
 * @property string $title
 * @property string $content
 * @property bool $is_weekly
 * @property Event[] $events
 * @property int|null $featured_image_id
 * @property string|null $published_at
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property string|null $deleted_at
 * @property-read User $user
 * @property-read string $url
 * @property-read StorageEntry|null $featuredImage
 *
 * @method static bool|null forceDelete()
 * @method static bool|null restore()
 * @method static QueryBuilder|Newsitem onlyTrashed()
 * @method static QueryBuilder|Newsitem withTrashed()
 * @method static QueryBuilder|Newsitem withoutTrashed()
 * @method static Builder|Newsitem whereContent($value)
 * @method static Builder|Newsitem whereCreatedAt($value)
 * @method static Builder|Newsitem whereDeletedAt($value)
 * @method static Builder|Newsitem whereFeaturedImageId($value)
 * @method static Builder|Newsitem whereId($value)
 * @method static Builder|Newsitem wherePublishedAt($value)
 * @method static Builder|Newsitem whereTitle($value)
 * @method static Builder|Newsitem whereUpdatedAt($value)
 * @method static Builder|Newsitem whereUserId($value)
 * @method static Builder|Newsitem newModelQuery()
 * @method static Builder|Newsitem newQuery()
 * @method static Builder|Newsitem query()
 *
 * @mixin Eloquent
 */
class Newsitem extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $table = 'newsitems';

    protected $guarded = ['id'];

    protected $with = ['featuredImage'];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function events(): BelongsToMany
    {
        return $this->belongsToMany(Event::class, 'event_newsitem');
    }

    public function featuredImage(): BelongsTo
    {
        return $this->belongsTo(StorageEntry::class, 'featured_image_id');
    }

    public function isPublished(): bool
    {
        return Carbon::parse($this->published_at)->isPast();
    }

    public function getUrlAttribute(): string
    {
        if ($this->is_weekly) {
            return route('news::showWeeklyPreview', ['id' => $this->id]);
        }

        return route('news::show', ['id' => $this->id]);
    }
}
