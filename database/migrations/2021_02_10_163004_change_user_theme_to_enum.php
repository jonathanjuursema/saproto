<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class ChangeUserThemeToEnum extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        $i = 0;
        foreach (config('proto.themes') as $theme) {
            DB::table('users')->where('theme', "assets/application-{$theme}.css")->update(['theme' => $i]);
            $i++;
        }

        Schema::table('users', function (Blueprint $table) {
            $table->integer('theme')->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('theme')->change();
        });

        $i = 0;
        foreach (config('proto.themes') as $theme) {
            DB::table('users')->where('theme', $i)->update(['theme' => "assets/application-{$theme}.css"]);
            $i++;
        }
    }
}
