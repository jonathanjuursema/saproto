<?php

namespace App\Models;

use Carbon;
use Eloquent;
use File;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * Photo model.
 *
 * @property int $id
 * @property int $file_id
 * @property int $album_id
 * @property int $date_taken
 * @property int $private
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read PhotoAlbum $album
 * @property-read StorageEntry $file
 * @property-read File $url
 * @property-read Collection|PhotoLikes[] $likes
 *
 * @method static Builder|Photo whereAlbumId($value)
 * @method static Builder|Photo whereCreatedAt($value)
 * @method static Builder|Photo whereDateTaken($value)
 * @method static Builder|Photo whereFileId($value)
 * @method static Builder|Photo whereId($value)
 * @method static Builder|Photo wherePrivate($value)
 * @method static Builder|Photo whereUpdatedAt($value)
 * @method static Builder|Photo newModelQuery()
 * @method static Builder|Photo newQuery()
 * @method static Builder|Photo query()
 *
 * @mixin Eloquent
 */
class Photo extends Model
{
    protected $table = 'photos';

    protected $guarded = ['id'];

    /** @return BelongsTo */
    public function album()
    {
        return $this->belongsTo('App\Models\PhotoAlbum', 'album_id');
    }

    /** @return HasMany */
    public function likes()
    {
        return $this->hasMany('App\Models\PhotoLikes');
    }

    /** @return hasOne */
    public function file()
    {
        return $this->hasOne('App\Models\StorageEntry', 'id', 'file_id');
    }

    /**
     * @param  bool  $next
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

    /**
     * @param  int  $paginateLimit
     * @return false|float|int
     */
    public function getAlbumPageNumber($paginateLimit)
    {
        $photoIndex = 1;
        $photos = self::where('album_id', $this->album_id)->orderBy('date_taken', 'ASC')->orderBy('id', 'ASC')->get();
        foreach ($photos as $photoItem) {
            if ($this->id == $photoItem->id) {
                return ceil($photoIndex / $paginateLimit);
            }
            $photoIndex++;
        }

        return 1;
    }

    /** @return int */
    public function getLikes()
    {
        return $this->likes()->count();
    }

    /** @return string */
    public function thumbnail()
    {
        return $this->file->generateImagePath(400, 400);
    }

    /** @return string */
    public function getUrlAttribute()
    {
        return $this->file->generatePath();
    }

    public static function boot()
    {
        parent::boot();

        static::deleting(function ($photo) {
            /* @var Photo $photo */
            $photo->file->delete();
            if ($photo->id == $photo->album->thumb_id) {
                $album = $photo->album;
                $album->thumb_id = null;
                $album->save();
            }
        });
    }
}
