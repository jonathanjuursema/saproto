<?php

use App\Models\Photo;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        foreach (Photo::all() as $photo) {
            $media = $photo->file->generateLocalPath();
            $photo->addMedia($media)->toMediaCollection();
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('media_library', function (Blueprint $table) {
            //
        });
    }
};
