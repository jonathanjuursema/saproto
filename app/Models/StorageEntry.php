<?php

namespace App\Models;

use App\Http\Controllers\FileController;
use Barryvdh\LaravelIdeHelper\Eloquent;
use Carbon;
use Illuminate\Contracts\Filesystem\FileNotFoundException;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\Image;
use Intervention\Image\ImageManagerStatic;

/**
 * Storage Entry Model.
 *
 * @property int $id
 * @property string $filename
 * @property string $mime
 * @property string $original_filename
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property string $hash
 *
 * @method static Builder|StorageEntry whereCreatedAt($value)
 * @method static Builder|StorageEntry whereFilename($value)
 * @method static Builder|StorageEntry whereHash($value)
 * @method static Builder|StorageEntry whereId($value)
 * @method static Builder|StorageEntry whereMime($value)
 * @method static Builder|StorageEntry whereOriginalFilename($value)
 * @method static Builder|StorageEntry whereUpdatedAt($value)
 * @method static Builder|StorageEntry newModelQuery()
 * @method static Builder|StorageEntry newQuery()
 * @method static Builder|StorageEntry query()
 *
 * @mixin Eloquent
 */
class StorageEntry extends Model
{
    protected $table = 'files';

    protected $guarded = ['id'];

    protected $hidden = ['hash', 'original_filename', 'created_at', 'updated_at'];

    /**
     * **IMPORTANT!** IF YOU ADD ANY RELATION TO A FILE IN ANOTHER MODEL, DON'T FORGET TO UPDATE THIS.
     *
     * @return bool whether the file is orphaned (not in use, can really be deleted safely)
     */
    public function isOrphan()
    {
        $id = $this->id;

        return
            NarrowcastingItem::where('image_id', $id)->count() == 0 &&
            Page::where('featured_image_id', $id)->count() == 0 &&
            DB::table('pages_files')->where('file_id', $id)->count() == 0 &&
            Product::where('image_id', $id)->count() == 0 &&
            Company::where('image_id', $id)->count() == 0 &&
            User::where('image_id', $id)->count() == 0 &&
            Member::withTrashed()->where('membership_form_id', $id)->count() == 0 &&
            DB::table('emails_files')->where('file_id', $id)->count() == 0 &&
            Committee::where('image_id', $id)->count() == 0 &&
            Event::where('image_id', $id)->count() == 0 &&
            Newsitem::where('featured_image_id', $id)->count() == 0 &&
            SoundboardSound::where('file_id', $id)->count() == 0 &&
            HeaderImage::where('image_id', $id)->count() == 0 &&
            Photo::where('large_file_id', $id)->count() == 0 &&
            Photo::where('medium_file_id', $id)->count() == 0 &&
            Photo::where('small_file_id', $id)->count() == 0 &&
            Photo::where('tiny_file_id', $id)->count() == 0 &&
            Photo::where('file_id', $id)->count() == 0 &&
            Member::where('omnomcom_sound_id', $id)->count() == 0;
    }

    /**
     * @param UploadedFile $file
     * @param string|null $customPath
     *
     * @throws FileNotFoundException
     */
    public function createFromFile($file, $customPath = null, $private = false)
    {
        $this->hash = $this->generateHash();

        $this->filename = date('Y\/F\/d') . '/' . $this->hash;

        if ($customPath) {
            $this->filename = $customPath . $this->hash;
        }

        if ($private) {
            Storage::disk('local')->put($this->filename, File::get($file));
        } else {
            Storage::disk('public')->put($this->filename, File::get($file));
        }

        $this->mime = $file->getClientMimeType();
        $this->original_filename = $file->getClientOriginalName();

        $this->save();
    }

    /**
     * @param resource|string $data
     * @param string $mime
     * @param string $name
     * @param string|null $customPath
     */
    public function createFromData($data, $mime, $name, $customPath = null, $private = false)
    {
        $this->hash = $this->generateHash();
        $this->filename = date('Y\/F\/d') . '/' . $this->hash;
        $this->mime = $mime;
        $this->original_filename = $name;

        if ($customPath) {
            $this->filename = $customPath . $this->hash;
        }

        if ($private) {
            Storage::disk('local')->put($this->filename, $data);
        } else {
            Storage::disk('public')->put($this->filename, $data);
        }

        $this->save();
    }

    /**
     * @param UploadedFile|string $file
     * @param string|null $customPath
     * @param int|null $width
     * @param string|null $original_name
     * @param Image|null $watermark
     */
    public function createFromPhoto($file, $customPath = null, $width = null, $original_name = null, $watermark = null, $private = false)
    {
        $this->hash = $this->generateHash();
        $this->filename = date('Y\/F\/d') . '/' . $this->hash;
        if ($customPath) {
            $this->filename = $customPath . $this->hash;
        }
        $image = ImageManagerStatic::make($file);
        $image->orientate();
        if ($width != null) {
            $image->resize($width, null, function ($constraint) {
                $constraint->aspectRatio();
            });
        }
        if ($watermark) {
            $watermark->resize($image->width() / 5, null, function ($constraint) {
                $constraint->aspectRatio();
            });

            $offset = (int)floor($image->width() / 100 * 2.5);
            $image->insert($watermark, 'bottom-right', $offset, 2 * $offset);
        }
        $image->stream();

        $this->original_filename = $original_name;
        $this->mime = $image->mime();

        if (!File::exists(Storage::disk('local')->path($customPath))) {
            File::makeDirectory(Storage::disk('local')->path($customPath), 0777, true);
        }
        if (!File::exists(Storage::disk('public')->path($customPath))) {
            File::makeDirectory(Storage::disk('public')->path($customPath), 0777, true);
        }

        if ($private) {
            Storage::disk('local')->put($customPath . $this->hash, $image);
        } else {
            Storage::disk('public')->put($customPath . $this->hash, $image);
        }

        return back();
    }

    /** @return string */
    private function generateHash()
    {
        return sha1(date('U') . mt_rand(1, intval(99999999999)));
    }

    /** @return string */
    public function generateUrl()
    {
        if (File::exists(Storage::disk('public')->path($this->filename))) {
            $url = asset($this->filename);
        } else {
            $url = route('file::get', ['id' => $this->id, 'hash' => $this->hash]);
        }
        if (config('app-proto.assets-domain')) {
            $url = str_replace(config('app-proto.primary-domain'), config('app-proto.assets-domain'), $url);
        }

        return $url;
    }

    /**
     * @param int|null $w
     * @param int|null $h
     * @return string
     */
    public function getBase64($w = null, $h = null)
    {
        /* @phpstan-ignore-next-line */
        return base64_encode(FileController::makeImage($this, $w, $h));
    }

    /**
     * @param bool $human Defaults to true.
     * @return string|int
     */
    public function getFileSize($human = true)
    {
        $size = File::size($this->generateLocalPath());
        if ($human) {
            if ($size < 1024) {
                return $size . ' bytes';
            } elseif ($size < pow(1024, 2)) {
                return round($size / pow(1024, 1), 1) . ' kilobytes';
            } elseif ($size < pow(1024, 3)) {
                return round($size / pow(1024, 2), 1) . ' megabytes';
            } else {
                return round($size / pow(1024, 3), 1) . ' gigabytes';
            }
        } else {
            return $size;
        }
    }

    /** @return string */
    public function generateLocalPath()
    {
        if (File::exists(Storage::disk('public')->path($this->filename))) {
            return Storage::disk('public')->path($this->filename);
        }

        return Storage::disk('local')->path($this->filename);
    }

    /**
     * @param string $algo Defaults to md5.
     * @return string
     */
    public function getFileHash($algo = 'md5')
    {
        return $algo . ': ' . hash_file($algo, $this->generateLocalPath());
    }

    /**
     * @return bool
     */
    public function makePublic()
    {
        if (File::exists(Storage::disk('local')->path($this->filename))) {
            File::move(Storage::disk('local')->path($this->filename), Storage::disk('public')->path($this->filename));
        }

        return File::exists(Storage::disk('public')->path($this->filename));
    }

    /**
     * @return bool
     */
    public function makePrivate()
    {
        if (File::exists(Storage::disk('public')->path($this->filename))) {
            File::move(Storage::disk('public')->path($this->filename), Storage::disk('local')->path($this->filename));
        }

        return File::exists(Storage::disk('local')->path($this->filename));
    }

    public static function boot()
    {
        parent::boot();

        static::deleting(function ($file) {
            if (File::exists(Storage::disk('public')->path($file->filename))) {
                Storage::disk('public')->delete($file->filename);
            }
            if (File::exists(Storage::disk('local')->path($file->filename))) {
                Storage::disk('local')->delete($file->filename);
            }
        });
    }
}
