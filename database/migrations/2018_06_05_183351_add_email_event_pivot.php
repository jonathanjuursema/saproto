<?php

use App\Models\Email;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddEmailEventPivot extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('emails_events', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('email_id')->nullable(false);
            $table->integer('event_id')->nullable(false);
        });

        foreach (Email::query()->where('to_event', '!=', false)->get() as $email) {
            $email->events()->sync([$email->to_event]);
            $email->to_event = true;
        }

        Schema::table('emails', function (Blueprint $table) {
            $table->boolean('to_event')->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::drop('emails_events');
    }
}
