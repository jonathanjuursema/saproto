<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Schema;

class Initialmigration extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('users', function ($table) {
            $table->dropColumn('name');

            $table->string('name_first');
            $table->string('name_last');
            $table->string('name_initials');

            $table->date('birthdate');
            $table->integer('gender');
            $table->string('nationality');

            $table->string('phone');
            $table->string('website')->nullable();
            $table->text('biography')->nullable();

            $table->boolean('phone_visible')->default(true);
            $table->boolean('address_visible')->default(true);
            $table->boolean('receive_newsletter')->default(true);
            $table->boolean('receive_sms')->default(true);

            $table->string('utwente_username')->nullable();
        });

        Schema::create('sessions', function ($table) {
            $table->string('id')->unique();
            $table->text('payload');
            $table->integer('last_activity');
        });

        Schema::create('addresses', function ($table) {
            $table->increments('id');
            $table->integer('user_id');

            $table->string('street');
            $table->string('number');
            $table->string('zipcode');
            $table->string('city');
            $table->string('country');

            $table->boolean('is_primary');

            $table->timestamps();
        });

        Schema::create('bankaccounts', function ($table) {
            $table->increments('id');
            $table->integer('user_id');

            $table->string('iban');
            $table->string('bic');
            $table->string('machtigingid');

            $table->enum('withdrawal_type', ['FRST', 'RCUR']);

            $table->timestamps();
        });

        Schema::create('members', function ($table) {
            $table->increments('id');
            $table->integer('user_id');

            $table->string('proto_mail')->nullable();
            $table->date('till')->nullable();
            $table->boolean('primary_member')->default(true);

            $table->enum('type', ['ORDINARY', 'ASSOCIATE', 'HONORARY', 'DONATOR']);
            $table->enum('fee_cycle', ['YEARLY', 'FULLTIME']);

            $table->timestamps();
        });

        Schema::create('studies', function ($table) {
            $table->increments('id');

            $table->string('name');
            $table->string('faculty');

            $table->timestamps();
        });

        Schema::create('studies_users', function ($table) {
            $table->increments('id');

            $table->integer('study_id');
            $table->integer('user_id');

            $table->date('till')->nullable();

            $table->timestamps();
        });

        Schema::create('utwentes', function ($table) {
            $table->increments('id');
            $table->integer('user_id');

            $table->string('organisational_unit');
            $table->string('mail');

            $table->string('room_number')->nullable();
            $table->string('phone_number')->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::drop('sessions');
        Schema::drop('addresses');
        Schema::drop('bankaccounts');
        Schema::drop('members');
        Schema::drop('studies');
        Schema::drop('studies_users');
        Schema::drop('utwentes');
    }
}
