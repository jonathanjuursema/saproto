<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Models\Email;

class AddEmailEventPivot extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('emails_events', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('email_id')->nullable(false);
            $table->integer('event_id')->nullable(false);
        });

        foreach (Email::where('to_event', '!=', false)->get() as $email) {
            $email->events()->sync([$email->to_event]);
            $email->to_event = true;
        }

        Schema::table('emails', function (Blueprint $table) {
            $table->boolean('to_event')->change();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('emails_events');
    }
}
