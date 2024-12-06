<?php

namespace App\Models;

use Carbon;
use Eloquent;
use File;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Support\Facades\Auth;

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
    use HasFactory;

    protected $table = 'photos';

    protected $guarded = ['id'];

    protected $with = ['file'];

    protected static function booted(): void
    {
        static::addGlobalScope('private', function (Builder $builder) {
            $builder->unless(Auth::user()?->is_member, fn ($builder) => $builder->where('private', false)
                ->whereHas('album', function ($query) {
                    $query->where('private', false);
                }));
        });

        static::addGlobalScope('published', function (Builder $builder) {
            $builder->unless(Auth::user()?->can('protography'), fn ($builder) => $builder->whereHas('album', function ($query) {
                $query->where('published', true);
            }));
        });
    }

    public function album(): BelongsTo
    {
        return $this->belongsTo(PhotoAlbum::class, 'album_id');
    }

    public function likes(): HasMany
    {
        return $this->hasMany(PhotoLikes::class);
    }

    public function file(): HasOne
    {
        return $this->hasOne(StorageEntry::class, 'id', 'file_id');
    }

    private function getAdjacentPhoto(bool $next = true): ?Photo
    {
        if ($next) {
            $ord = 'DESC';
            $comp = '<';
        } else {
            $ord = 'ASC';
            $comp = '>';
        }

        return self::query()->where(function ($query) use ($comp) {
            $query->where('date_taken', $comp, $this->date_taken)
                ->orWhere(function ($query) use ($comp) {
                    $query->where('date_taken', '=', $this->date_taken)
                        ->where('id', $comp, $this->id);
                });
        })
            ->where('album_id', $this->album_id)
            ->orderBy('date_taken', $ord)
            ->orderBy('id', $ord)
            ->first();
    }

    public function getNextPhoto(): ?Photo
    {
        return $this->getAdjacentPhoto();
    }

    public function getPreviousPhoto(): ?Photo
    {
        return $this->getAdjacentPhoto(false);
    }

    public function getAlbumPageNumber(int $paginateLimit): float|int
    {
        $photoIndex = 1;
        $photos = self::query()->where('album_id', $this->album_id)
            ->orderBy('date_taken', 'desc')->orderBy('id', 'desc')
            ->get();
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

    public function thumbnail(): string
    {
        return $this->file->generateImagePath(400, 400);
    }

    public function getUrlAttribute(): string
    {
        return $this->file->generatePath();
    }

    protected static function boot(): void
    {
        parent::boot();

        static::deleting(static function ($photo) {
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
