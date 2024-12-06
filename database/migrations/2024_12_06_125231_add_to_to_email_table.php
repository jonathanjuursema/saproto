<?php

use App\Enums\EmailDestination;
use App\Models\Email;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('emails', function (Blueprint $table) {
            $table->integer('destination')->default(EmailDestination::NO_DESTINATION);
            $table->string('job_batch_id')->nullable();
            $table->foreign('job_batch_id')->references('id')->on('job_batches');
        });

        //loop through and chunk all the Emails and change the destination to EmailDestination Enum
        Email::query()->chunk(25, function ($mails) {
            foreach ($mails as $mail) {
                if ($mail->to_user) {
                    $mail->destination = EmailDestination::ALL_USERS;
                } elseif ($mail->to_member) {
                    $mail->destination = EmailDestination::ALL_MEMBERS;
                } elseif ($mail->to_pending) {
                    $mail->destination = EmailDestination::PENDING_MEMBERS;
                } elseif ($mail->to_active) {
                    $mail->destination = EmailDestination::ACTIVE_MEMBERS;
                } elseif ($mail->to_list) {
                    $mail->destination = EmailDestination::EMAIL_LISTS;
                } elseif ($mail->to_event) {
                    $mail->destination = EmailDestination::EVENT;
                } elseif ($mail->to_backup) {
                    $mail->destination = EmailDestination::EVENT_WITH_BACKUP;
                } else {
                    $mail->destination = EmailDestination::NO_DESTINATION;
                }

                $mail->save();
            }
        });

        //drop the old columns
        Schema::table('emails', function (Blueprint $table) {
            $table->dropColumn('to_user');
            $table->dropColumn('to_member');
            $table->dropColumn('to_pending');
            $table->dropColumn('to_active');
            $table->dropColumn('to_list');
            $table->dropColumn('to_event');
            $table->dropColumn('to_backup');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('emails', function (Blueprint $table) {
            $table->boolean('to_user')->default(false);
            $table->boolean('to_member')->default(false);
            $table->boolean('to_pending')->default(false);
            $table->boolean('to_active')->default(false);
            $table->boolean('to_list')->default(false);
            $table->boolean('to_event')->default(false);
            $table->boolean('to_backup')->default(false);
        });

        //loop through and chunk all the Emails and change the destination to EmailDestination Enum
        Email::query()->chunk(25, function ($mails) {
            foreach ($mails as $mail) {
                if ($mail->destination === EmailDestination::ALL_USERS) {
                    $mail->to_user = true;
                } elseif ($mail->destination === EmailDestination::ALL_MEMBERS) {
                    $mail->to_member = true;
                } elseif ($mail->destination === EmailDestination::PENDING_MEMBERS) {
                    $mail->to_pending = true;
                } elseif ($mail->destination === EmailDestination::ACTIVE_MEMBERS) {
                    $mail->to_active = true;
                } elseif ($mail->destination === EmailDestination::EMAIL_LISTS) {
                    $mail->to_list = true;
                } elseif ($mail->destination === EmailDestination::EVENT) {
                    $mail->to_event = true;
                } elseif ($mail->destination === EmailDestination::EVENT_WITH_BACKUP) {
                    $mail->to_backup = true;
                }

                $mail->save();
            }
        });

        Schema::table('emails', function (Blueprint $table) {
            $table->dropColumn('destination');
            $table->dropColumn('job_batch_id');
        });
    }
};
