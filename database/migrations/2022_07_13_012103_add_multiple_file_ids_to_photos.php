
<?php

use App\Models\Photo;
use App\Models\StorageEntry;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\Console\Output\ConsoleOutput;

class AddMultipleFileIdsToPhotos extends Migration
{
    public function ensureLocalDirectoryExists($directoryPath, $output)
    {
        if (! File::exists(Storage::disk('local')->path($directoryPath))) {
            File::makeDirectory(Storage::disk('local')->path($directoryPath), 0777, true);
            $output->writeln('created the folder: '.$directoryPath);
        }
    }

    public function ensurePublicDirectoryExists($directoryPath, $output)
    {
        if (! File::exists(Storage::disk('public')->path($directoryPath))) {
            File::makeDirectory(Storage::disk('public')->path($directoryPath), 0777, true);
            $output->writeln('created the folder: '.$directoryPath);
        }
    }

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $output = new ConsoleOutput();
        if (! Schema::hasColumn('photos', 'large_file_id')) {
            Schema::table('photos', function (Blueprint $table) {
                $table->integer('large_file_id')->after('file_id')->nullable();
                $table->integer('medium_file_id')->after('large_file_id')->nullable();
                $table->integer('small_file_id')->after('medium_file_id')->nullable();
                $table->integer('tiny_file_id')->after('small_file_id')->nullable();
            });
        }

        foreach (Photo::all() as $photo) {
            $newPhoto = new Photo();
            $newPhoto->makePhoto($photo->file->generateLocalPath(), $photo->fileRelation->filename, $photo->created_at, $photo->private, $pathInPhotos = null, $photo->album_id);
            $newPhoto->save();
            $photo->delete();
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        $output = new ConsoleOutput();
        $output->writeln('starting moving all files back over');
        foreach (Photo::all() as $photo) {
            if ($photo->private) {
                $oldPath = Storage::disk('local')->path('photos/original_photos/'.$photo->album->id.'/'.$photo->file->hash);
            } else {
                $oldPath = Storage::disk('public')->path('photos/original_photos/'.$photo->album->id.'/'.$photo->file->hash);
            }

            $newPath = 'photos/'.$photo->album->id.'/'.$photo->file->hash;
            $newPathFromRoot = Storage::disk('local')->path($newPath);

            $this->ensureLocalDirectoryExists('photos/'.$photo->album->id.'/', $output);

            if (! File::exists($newPathFromRoot) && File::move($oldPath, $newPathFromRoot)) {
                $photo->file->filename = $newPath;
                $photo->file->save();

                $large_photo = StorageEntry::find($photo->large_file_id);
                if ($large_photo) {
                    $large_photo->delete();
                }

                $medium_photo = StorageEntry::find($photo->medium_file_id);
                if ($medium_photo) {
                    $medium_photo->delete();
                }

                $small_photo = StorageEntry::find($photo->small_file_id);
                if ($small_photo) {
                    $small_photo->delete();
                }

                $tiny_photo = StorageEntry::find($photo->tiny_file_id);
                if ($tiny_photo) {
                    $tiny_photo->delete();
                }
            }
        }
        $output->writeln('dropping the columns!');
        if (Schema::hasColumn('photos', 'large_file_id')) {
            Schema::table('photos', function (Blueprint $table) {
                $table->dropColumn('large_file_id');
                $table->dropColumn('medium_file_id');
                $table->dropColumn('small_file_id');
                $table->dropColumn('tiny_file_id');
            });
        }
        $output->writeln('deleting folders!');
        File::cleanDirectory(Storage::disk('public')->path('photos'));
        File::deleteDirectory(Storage::disk('public')->path('photos'));
        File::deleteDirectory(Storage::disk('local')->path('photos/original_photos/'));
        File::deleteDirectory(Storage::disk('local')->path('photos/large_photos/'));
        File::deleteDirectory(Storage::disk('local')->path('photos/medium_photos/'));
        File::deleteDirectory(Storage::disk('local')->path('photos/small_photos/'));
        File::deleteDirectory(Storage::disk('local')->path('photos/tiny_photos/'));
    }
}
