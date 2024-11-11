<?php

namespace App\Models;

use App\Http\Controllers\FileController;
use Carbon;
use Eloquent;
use Illuminate\Contracts\Filesystem\FileNotFoundException;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
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
    use HasFactory;

    protected $table = 'files';

    protected $guarded = ['id'];

    /**
     * **IMPORTANT!** IF YOU ADD ANY RELATION TO A FILE IN ANOTHER MODEL, DON'T FORGET TO UPDATE THIS.
     *
     * @return bool whether or not the file is orphaned (not in use, can really be deleted safely)
     */
    public function isOrphan(): bool
    {
        $id = $this->id;

        return
            NarrowcastingItem::query()->where('image_id', $id)->count() == 0 &&
            Page::query()->where('featured_image_id', $id)->count() == 0 &&
            DB::table('pages_files')->where('file_id', $id)->count() == 0 &&
            Product::query()->where('image_id', $id)->count() == 0 &&
            Company::query()->where('image_id', $id)->count() == 0 &&
            User::query()->where('image_id', $id)->count() == 0 &&
            Member::withTrashed()->where('membership_form_id', $id)->count() == 0 &&
            DB::table('emails_files')->where('file_id', $id)->count() == 0 &&
            Committee::query()->where('image_id', $id)->count() == 0 &&
            Event::query()->where('image_id', $id)->count() == 0 &&
            Newsitem::query()->where('featured_image_id', $id)->count() == 0 &&
            SoundboardSound::query()->where('file_id', $id)->count() == 0 &&
            HeaderImage::query()->where('image_id', $id)->count() == 0 &&
            Photo::query()->withoutGlobalScopes()->where('file_id', $id)->count() == 0 &&
            Member::query()->where('omnomcom_sound_id', $id)->count() == 0 &&
            WallstreetEvent::query()->where('image_id', $id)->count() == 0;
    }

    /**
     * @throws FileNotFoundException
     */
    public function createFromFile(UploadedFile $file, ?string $customPath = null): void
    {
        $this->hash = $this->generateHash();

        $this->filename = date('Y\/F\/d').'/'.$this->hash;

        if ($customPath !== null && $customPath !== '' && $customPath !== '0') {
            $this->filename = $customPath.$this->hash;
        }

        Storage::disk('local')->put($this->filename, File::get($file));

        $this->mime = $file->getClientMimeType();
        $this->original_filename = $file->getClientOriginalName();

        $this->save();
    }

    public function createFromData(string $data, string $mime, string $name, ?string $customPath = null): void
    {
        $this->hash = $this->generateHash();
        $this->filename = date('Y\/F\/d').'/'.$this->hash;
        $this->mime = $mime;
        $this->original_filename = $name;

        if ($customPath !== null && $customPath !== '' && $customPath !== '0') {
            $this->filename = $customPath.$this->hash;
        }

        Storage::disk('local')->put($this->filename, $data);

        $this->save();
    }

    private function generateHash(): string
    {
        return sha1(date('U').mt_rand(1, 99999999999));
    }

    public function generatePath(): string
    {
        $url = route('file::get', ['id' => $this->id, 'hash' => $this->hash]);
        if (config('app-proto.assets-domain')) {
            return str_replace(config('app-proto.primary-domain'), config('app-proto.assets-domain'), $url);
        }

        return $url;
    }

    public function generateImagePath(?int $w, ?int $h): string
    {
        $url = route('image::get', ['id' => $this->id, 'hash' => $this->hash, 'w' => $w, 'h' => $h]);
        if (config('app-proto.assets-domain')) {
            return str_replace(config('app-proto.primary-domain'), config('app-proto.assets-domain'), $url);
        }

        return $url;
    }

    public function getBase64(?int $w = null, ?int $h = null): string
    {
        return base64_encode(FileController::makeImage($this, $w, $h));
    }

    /**
     * @param  bool  $human  Defaults to true.
     */
    public function getFileSize(bool $human = true): int|string
    {
        $size = File::size($this->generateLocalPath());
        if (! $human) {
            return $size;
        }

        if ($size < 1024) {
            return $size.' bytes';
        }

        if ($size < 1024 ** 2) {
            return round($size / 1024 ** 1, 1).' kilobytes';
        }

        if ($size < 1024 ** 3) {
            return round($size / 1024 ** 2, 1).' megabytes';
        }

        return round($size / 1024 ** 3, 1).' gigabytes';
    }

    public function generateLocalPath(): string
    {
        return storage_path('app/'.$this->filename);
    }

    /**
     * @param  string  $algo  Defaults to md5.
     */
    public function getFileHash(string $algo = 'md5'): string
    {
        return $algo.': '.hash_file($algo, $this->generateLocalPath());
    }

    protected static function boot(): void
    {
        parent::boot();

        static::deleting(static function ($file) {
            Storage::disk('local')->delete($file->filename);
        });
    }
}
