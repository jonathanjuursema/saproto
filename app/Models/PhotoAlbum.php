<?php

namespace App\Models;

use Carbon;
use Eloquent;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

/**
 * App\Models\PhotoAlbum.
 *
 * @property int $id
 * @property string $name
 * @property int $date_create
 * @property int $date_taken
 * @property int $thumb_id
 * @property int|null $event_id
 * @property bool $private
 * @property bool $published
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read Event|null $event
 * @property-read Photo $thumb_photo
 * @property-read Collection|Photo[] $items
 *
 * @method static Builder|PhotoAlbum whereCreatedAt($value)
 * @method static Builder|PhotoAlbum whereDateCreate($value)
 * @method static Builder|PhotoAlbum whereDateTaken($value)
 * @method static Builder|PhotoAlbum whereEventId($value)
 * @method static Builder|PhotoAlbum whereId($value)
 * @method static Builder|PhotoAlbum whereName($value)
 * @method static Builder|PhotoAlbum wherePrivate($value)
 * @method static Builder|PhotoAlbum wherePublished($value)
 * @method static Builder|PhotoAlbum whereThumbId($value)
 * @method static Builder|PhotoAlbum whereUpdatedAt($value)
 * @method static Builder|PhotoAlbum newModelQuery()
 * @method static Builder|PhotoAlbum newQuery()
 * @method static Builder|PhotoAlbum query()
 *
 * @mixin Eloquent
 */
class PhotoAlbum extends Model
{
    protected $table = 'photo_albums';

    protected $guarded = ['id'];

    /** @return BelongsTo */
    public function event()
    {
        return $this->belongsTo('App\Models\Event', 'event_id');
    }

    /** @return HasOne */
    private function thumbPhoto()
    {
        return $this->hasOne('App\Models\Photo', 'id', 'thumb_id');
    }

    /** @return HasMany */
    public function items()
    {
        return $this->hasMany('App\Models\Photo', 'album_id');
    }

    /** @return string|null */
    public function thumb()
    {
        if ($this->thumb_id) {
            return $this->thumbPhoto()->first()->thumbnail();
        } else {
            return null;
        }
    }
}
