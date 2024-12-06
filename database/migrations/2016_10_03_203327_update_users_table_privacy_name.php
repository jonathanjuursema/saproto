<?php

use App\Models\User;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Schema;

class UpdateUsersTablePrivacyName extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('users', function ($table) {
            $table->date('birthdate')->nullable()->default(null)->change();
            $table->integer('gender')->nullable()->default(null)->change();
            $table->string('nationality')->nullable()->default(null)->change();
            $table->string('name')->after('id');
            $table->string('calling_name')->after('name');
        });

        foreach (User::all() as $user) {
            $user->name = $user->name_first.' '.$user->name_last;
            $user->calling_name = $user->name_first;
            $user->save();
        }

        Schema::table('users', function ($table) {
            $table->dropColumn('name_initials');
            $table->dropColumn('name_first');
            $table->dropColumn('name_last');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function ($table) {
            $table->string('name_first')->after('id');
            $table->string('name_last')->after('name_first');
            $table->string('name_initials')->after('name_last');
        });

        foreach (User::all() as $user) {
            $name = explode(' ', $user->name);
            $user->name_first = $name[0];
            $user->name_last = implode(' ', array_slice($name, 1));
            $user->save();
        }

        Schema::table('users', function ($table) {
            $table->date('birthdate')->nullable(false)->change();
            $table->integer('gender')->nullable(false)->change();
            $table->string('nationality')->nullable(false)->change();
            $table->dropColumn('name');
            $table->dropColumn('calling_name');
        });
    }
}
