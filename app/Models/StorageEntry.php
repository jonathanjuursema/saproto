<?php

namespace App\Models;

use App\Http\Controllers\FileController;
use Carbon;
use DB;
use Eloquent;
use Illuminate\Contracts\Filesystem\FileNotFoundException;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;

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

    /**
     * **IMPORTANT!** IF YOU ADD ANY RELATION TO A FILE IN ANOTHER MODEL, DON'T FORGET TO UPDATE THIS.
     *
     * @return bool whether or not the file is orphaned (not in use, can really be deleted safely)
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
            Photo::where('file_id', $id)->count() == 0 &&
            Member::where('omnomcom_sound_id', $id)->count() == 0;
    }

    /**
     * @param  UploadedFile  $file
     * @param  string|null  $customPath
     *
     * @throws FileNotFoundException
     */
    public function createFromFile($file, $customPath = null)
    {
        $this->hash = $this->generateHash();

        $this->filename = date('Y\/F\/d').'/'.$this->hash;

        if ($customPath) {
            $this->filename = $customPath.$this->hash;
        }

        Storage::disk('local')->put($this->filename, File::get($file));

        $this->mime = $file->getClientMimeType();
        $this->original_filename = $file->getClientOriginalName();

        $this->save();
    }

    /**
     * @param  resource|string  $data
     * @param  string  $mime
     * @param  string  $name
     * @param  string|null  $customPath
     */
    public function createFromData($data, $mime, $name, $customPath = null)
    {
        $this->hash = $this->generateHash();
        $this->filename = date('Y\/F\/d').'/'.$this->hash;
        $this->mime = $mime;
        $this->original_filename = $name;

        if ($customPath) {
            $this->filename = $customPath.$this->hash;
        }

        Storage::disk('local')->put($this->filename, $data);

        $this->save();
    }

    /** @return string */
    private function generateHash()
    {
        return sha1(date('U').mt_rand(1, intval(99999999999)));
    }

    /** @return string */
    public function generatePath()
    {
        $url = route('file::get', ['id' => $this->id, 'hash' => $this->hash]);
        if (config('app-proto.assets-domain')) {
            $url = str_replace(config('app-proto.primary-domain'), config('app-proto.assets-domain'), $url);
        }

        return $url;
    }

    /**
     * @param  int|null  $w
     * @param  int|null  $h
     * @return string
     */
    public function generateImagePath($w, $h)
    {
        $url = route('image::get', ['id' => $this->id, 'hash' => $this->hash, 'w' => $w, 'h' => $h]);
        if (config('app-proto.assets-domain')) {
            $url = str_replace(config('app-proto.primary-domain'), config('app-proto.assets-domain'), $url);
        }

        return $url;
    }

    /**
     * @param  int|null  $w
     * @param  int|null  $h
     * @return string
     */
    public function getBase64($w = null, $h = null)
    {
        /* @phpstan-ignore-next-line */
        return base64_encode(FileController::makeImage($this, $w, $h));
    }

    /**
     * @param  bool  $human  Defaults to true.
     * @return string|int
     */
    public function getFileSize($human = true)
    {
        $size = File::size($this->generateLocalPath());
        if ($human) {
            if ($size < 1024) {
                return $size.' bytes';
            } elseif ($size < pow(1024, 2)) {
                return round($size / pow(1024, 1), 1).' kilobytes';
            } elseif ($size < pow(1024, 3)) {
                return round($size / pow(1024, 2), 1).' megabytes';
            } else {
                return round($size / pow(1024, 3), 1).' gigabytes';
            }
        } else {
            return $size;
        }
    }

    /** @return string */
    public function generateLocalPath()
    {
        return storage_path('app/'.$this->filename);
    }

    /**
     * @param  string  $algo  Defaults to md5.
     * @return string
     */
    public function getFileHash($algo = 'md5')
    {
        return $algo.': '.hash_file($algo, $this->generateLocalPath());
    }

    public static function boot()
    {
        parent::boot();

        static::deleting(function ($file) {
            Storage::disk('local')->delete($file->filename);
        });
    }
}
