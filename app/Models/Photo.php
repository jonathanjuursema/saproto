<?php

namespace Proto\Models;

use Carbon;
use Eloquent;
use File;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

/**
 * Photo model.
 *
 * @property int $id
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property int $file_id
 * @property int $album_id
 * @property int $date_taken
 * @property int $private
 * @property-read PhotoAlbum $album
 * @property-read StorageEntry $file
 * @property-read File $url
 * @property-read PhotoLikes[] $likes
 * @method static Builder|Photo whereAlbumId($value)
 * @method static Builder|Photo whereCreatedAt($value)
 * @method static Builder|Photo whereDateTaken($value)
 * @method static Builder|Photo whereFileId($value)
 * @method static Builder|Photo whereId($value)
 * @method static Builder|Photo wherePrivate($value)
 * @method static Builder|Photo whereUpdatedAt($value)
 * @mixin Eloquent
 */
class Photo extends Model
{
    protected $table = 'photos';

    protected $guarded = ['id'];

    /** @return BelongsTo|PhotoAlbum[] */
    public function album()
    {
        return $this->belongsTo('Proto\Models\PhotoAlbum', 'album_id');
    }

    /** @return HasMany */
    public function likes()
    {
        return $this->hasMany('Proto\Models\PhotoLikes');
    }

    /** @return HasOne|StorageEntry */
    private function file()
    {
        return $this->hasOne('Proto\Models\StorageEntry', 'id', 'file_id')->first();
    }

    /**
     * @param bool $next
     * @return Photo
     */
    private function getAdjacentPhoto($next = true)
    {
        if ($next) {
            $ord = 'ASC';
            $comp = '>';
        } else {
            $ord = 'DESC';
            $comp = '<';
        }

        $result = self::where('album_id', $this->album_id)->where('date_taken', $comp.'=', $this->date_taken)->orderBy('date_taken', $ord)->orderBy('id', $ord);
        if ($result->count() > 1) {
            return $result->where('id', $comp, $this->id)->first();
        }

        return $result->first();
    }

    /** @return Photo */
    public function getNextPhoto()
    {
        return $this->getAdjacentPhoto();
    }

    /** @return Photo */
    public function getPreviousPhoto()
    {
        return $this->getAdjacentPhoto(false);
    }

    /** @return int */
    public function getLikes()
    {
        return $this->likes()->count();
    }

    /** @return string */
    public function thumb()
    {
        return $this->file()->generateImagePath(400, 400);
    }

    /** @return string */
    public function getUrlAttribute()
    {
        return $this->file()->generatePath();
    }

    public static function boot()
    {
        parent::boot();

        static::deleting(function ($photo) {
            $photo->file()->delete();
            if ($photo->id == $photo->album->thumb_id) {
                $album = $photo->album;
                $album->thumb_id = null;
                $album->save();
            }
        });
    }
}
