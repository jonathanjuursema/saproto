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
use Illuminate\Database\Eloquent\Relations\HasOne;
use Intervention\Image\Facades\Image;

/**
 * Photo model.
 *
 * @property int $id
 * @property int $file_id
 * @property int $large_file_id
 * @property int $medium_file_id
 * @property int $small_file_id
 * @property int $tiny_file_id
 * @property int $album_id
 * @property int $date_taken
 * @property bool $private
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read PhotoAlbum $album
 * @property-read StorageEntry $file
 * @property-read StorageEntry $large_file
 * @property-read StorageEntry $medium_file
 * @property-read StorageEntry $small_file
 * @property-read StorageEntry $tiny_file
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

    public function makePhoto($photo, $original_name, $date_taken, $private = false, $pathInPhotos = null, $albumId = null, $addWatermark = false, $watermarkUserName = null)
    {
        $original_photo_storage = 'photos/original_photos/'.($albumId ?? $pathInPhotos).'/';
        $large_photos_storage = 'photos/large_photos/'.($albumId ?? $pathInPhotos).'/';
        $medium_photos_storage = 'photos/medium_photos/'.($albumId ?? $pathInPhotos).'/';
        $small_photos_storage = 'photos/small_photos/'.($albumId ?? $pathInPhotos).'/';
        $tiny_photos_storage = 'photos/tiny_photos/'.($albumId ?? $pathInPhotos).'/';

        $watermark = null;
        if ($addWatermark) {
            $watermark = Image::make(public_path('images/protography-watermark-template.png'));
            $watermark->text(strtoupper($watermarkUserName), 267, 1443, function ($font) {
                $font->file((public_path('fonts/Ubuntu-R.ttf')));
                $font->size(180);
                $font->valign('top');
            });
        }

        $original_file = new StorageEntry();
        $original_file->createFromPhoto($photo, $original_photo_storage, null, $original_name, $watermark, $private);
        $original_file->save();

        $large_file = new StorageEntry();
        $large_file->createFromPhoto($photo, $large_photos_storage, 1080, $original_name, $watermark, $private);
        $large_file->save();

        $medium_file = new StorageEntry();
        $medium_file->createFromPhoto($photo, $medium_photos_storage, 750, $original_name, $watermark, $private);
        $medium_file->save();

        $small_file = new StorageEntry();
        $small_file->createFromPhoto($photo, $small_photos_storage, 420, $original_name, $watermark, $private);
        $small_file->save();

        $tiny_file = new StorageEntry();
        $tiny_file->createFromPhoto($photo, $tiny_photos_storage, 50, $original_name, $watermark, $private);
        $tiny_file->save();

        $this->file_id = $original_file->id;
        $this->large_file_id = $large_file->id;
        $this->medium_file_id = $medium_file->id;
        $this->small_file_id = $small_file->id;
        $this->tiny_file_id = $tiny_file->id;
        $this->private = $private;

        $this->date_taken = $date_taken;
        $this->album_id = $albumId;
    }

    public function album(): BelongsTo
    {
        return $this->belongsTo(\App\Models\PhotoAlbum::class, 'album_id');
    }

    public function likes(): HasMany
    {
        return $this->hasMany(\App\Models\PhotoLikes::class);
    }

    public function file(): HasOne
    {
        return $this->hasOne(\App\Models\StorageEntry::class, 'id', 'file_id');
    }

    /** @return HasOne */
    public function large_file()
    {
        return $this->hasOne(\App\Models\StorageEntry::class, 'id', 'large_file_id');
    }

    /** @return HasOne */
    public function medium_file()
    {
        return $this->hasOne(\App\Models\StorageEntry::class, 'id', 'medium_file_id');
    }

    /** @return HasOne */
    public function small_file()
    {
        return $this->hasOne(\App\Models\StorageEntry::class, 'id', 'small_file_id');
    }

    public function tiny_file(): HasOne
    {
        return $this->hasOne(\App\Models\StorageEntry::class, 'id', 'tiny_file_id');
    }

    /**
     * @param  bool  $next
     * @param  User  $user
     */
    public function getAdjacentPhoto($next = true, $user = null): ?Photo
    {
        if ($next) {
            $ord = 'ASC';
            $comp = '>';
        } else {
            $ord = 'DESC';
            $comp = '<';
        }
        $result = self::where('album_id', $this->album_id)->where('date_taken', $comp.'=', $this->date_taken);

        if ($user == null || $user->member() == null) {
            $result = $result->where('private', false);
        }

        $result = $result->orderBy('date_taken', $ord)->orderBy('id', $ord);
        if ($result->count() > 1) {
            return $result->where('id', $comp, $this->id)->first();
        }

        return null;
    }

    public function getNextPhoto($user): ?Photo
    {
        return $this->getAdjacentPhoto(true, $user);
    }

    public function getPreviousPhoto($user): ?Photo
    {
        return $this->getAdjacentPhoto(false, $user);
    }

    /**
     * @param  int  $paginateLimit
     * @return float|int
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

    public function getLikes(): int
    {
        return $this->likes()->count();
    }

    public function likedByUser($user): bool
    {
        if ($user) {
            return $this->likes()->where('user_id', $user->id)->count() > 0;
        }

        return false;
    }

    public function getOriginalUrl(): string
    {
        return $this->file->generateUrl();
    }

    public function getLargeUrl(): string
    {
        return $this->large_file->generateUrl();
    }

    public function getMediumUrl(): string
    {
        return $this->medium_file->generateUrl();
    }

    public function getSmallUrl(): string
    {
        return $this->small_file->generateUrl();
    }

    public function getTinyUrl(): string
    {
        return $this->tiny_file->generateUrl();
    }

    public function mayViewPhoto($user): bool
    {
        if (! $this->private) {
            return true;
        }
        if ($user) {
            return $user->member() !== null;
        }

        return false;
    }

    public function makePublic(): bool
    {
        return
            $this->file->makePublic() &&
            $this->large_file->makePublic() &&
            $this->medium_file->makePublic() &&
            $this->small_file->makePublic() &&
            $this->tiny_file->makePublic();
    }

    public function makePrivate(): bool
    {
        return
            $this->file->makePrivate() &&
            $this->large_file->makePrivate() &&
            $this->medium_file->makePrivate() &&
            $this->small_file->makePrivate() &&
            $this->tiny_file->makePrivate();
    }

    public static function boot(): void
    {
        parent::boot();

        static::updated(function ($photo) {
            if ($photo->private) {
                if (! $photo->makePrivate()) {
                    $photo->private = false;
                }
            } else {
                if (! $photo->makePublic()) {
                    $photo->private = true;
                }
            }
        });

        static::deleting(function ($photo) {
            if ($photo->file()) {
                $photo->file()->delete();
            }
            if ($photo->large_file()) {
                $photo->large_file()->delete();
            }
            if ($photo->medium_file()) {
                $photo->medium_file()->delete();
            }
            if ($photo->small_file()) {
                $photo->small_file()->delete();
            }
            if ($photo->tiny_file()) {
                $photo->tiny_file()->delete();
            }
            if ($photo->album && $photo->id == $photo->album->thumb_id) {
                $album = $photo->album;
                $album->thumb_id = null;
                $album->save();
            }
        });
    }
}
