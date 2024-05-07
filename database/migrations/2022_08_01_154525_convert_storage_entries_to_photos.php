<?php

use App\Models\Committee;
use App\Models\Company;
use App\Models\Event;
use App\Models\Photo;
use App\Models\StorageEntry;
use App\Models\User;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class ConvertStorageEntriesToPhotos extends Migration
{
    private function moveOverPhotos($items, $path)
    {
        foreach ($items as $item) {
            $storageEntry = StorageEntry::find($item->photo_id);
            if ($storageEntry) {
                $photo = new Photo();
                $photo->makePhoto($storageEntry->generateLocalPath(), $storageEntry->original_filename, $storageEntry->created_at, false, $path);
                $photo->save();
                $item->photo_id = $photo->id;
                $item->save();
                $storageEntry->delete();
            }
        }
    }

    private function moveBackPhotos($items)
    {
        foreach ($items as $item) {
            $photo = Photo::find($item->photo_id);
            if ($photo && $photo->file()->first()) {
                $originalFile = $photo->file()->first();
                $newFolder = date('Y\/F\/d').'/';
                if (! File::exists(Storage::disk('local')->path($newFolder))) {
                    File::makeDirectory(Storage::disk('local')->path($newFolder), 0777, true);
                }
                $storageEntry = new StorageEntry();
                $storageEntry->original_filename = $originalFile->original_filename;
                $storageEntry->mime = $originalFile->mime;
                $storageEntry->hash = $originalFile->hash;
                $storageEntry->filename = $newFolder.$originalFile->hash;
                $storageEntry->save();
                File::move($originalFile->generateLocalPath(), Storage::disk('local')->path($newFolder.$storageEntry->hash));
                $item->photo_id = $storageEntry->id;
                $item->save();
                $photo->delete();
            }
        }
    }

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('photos', function (Blueprint $table) {
            $table->integer('album_id')->nullable()->change();
        });

        if (Schema::hasColumn('companies', 'image_id')) {
            Schema::table('companies', function (Blueprint $table) {
                $table->renameColumn('image_id', 'photo_id');
            });
        }
        if (Schema::hasColumn('committees', 'image_id')) {
            Schema::table('committees', function (Blueprint $table) {
                $table->renameColumn('image_id', 'photo_id');
            });
        }
        if (Schema::hasColumn('users', 'image_id')) {
            Schema::table('users', function (Blueprint $table) {
                $table->renameColumn('image_id', 'photo_id');
            });
        }
        if (Schema::hasColumn('events', 'image_id')) {
            Schema::table('events', function (Blueprint $table) {
                $table->renameColumn('image_id', 'photo_id');
            });
        }

        $this->moveOverPhotos(Event::all(), 'event_photos');
        $this->moveOverPhotos(Committee::all(), 'committee_photos');
        $this->moveOverPhotos(Company::all(), 'committee_photos');
        foreach (User::all() as $user) {
            $storageEntry = StorageEntry::find($user->photo_id);
            if ($storageEntry) {
                $photo = new Photo();
                $img = Image::make($storageEntry->generateLocalPath());
                $smallestSide = $img->width() < $img->height() ? $img->width : $img->height();
                $img->fit($smallestSide);
                $photo->makePhoto($img, $storageEntry->original_filename, $storageEntry->created_at, false, 'profile_pictures');
                $photo->save();
                $user->photo_id = $photo->id;
                $user->save();
                $storageEntry->delete();
            }
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {

        $this->moveBackPhotos(User::all());
        $this->moveBackPhotos(Event::all());
        $this->moveBackPhotos(Committee::all());
        $this->moveBackPhotos(Company::all());

        Schema::table('photos', function (Blueprint $table) {
            $table->integer('album_id')->change();
        });

        if (Schema::hasColumn('companies', 'photo_id')) {
            Schema::table('companies', function (Blueprint $table) {
                $table->renameColumn('photo_id', 'image_id');
            });
        }

        if (Schema::hasColumn('committees', 'photo_id')) {
            Schema::table('committees', function (Blueprint $table) {
                $table->renameColumn('photo_id', 'image_id');
            });
        }

        if (Schema::hasColumn('users', 'photo_id')) {
            Schema::table('users', function (Blueprint $table) {
                $table->renameColumn('photo_id', 'image_id');
            });
        }
        if (Schema::hasColumn('events', 'photo_id')) {
            Schema::table('events', function (Blueprint $table) {
                $table->renameColumn('photo_id', 'image_id');
            });
        }
    }
}
