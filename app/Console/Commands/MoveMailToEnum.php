<?php

namespace App\Console\Commands;

use App\Enums\EmailDestination;
use App\Models\Email;
use Illuminate\Console\Command;

class MoveMailToEnum extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'proto:moveEmail';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     */
    public function handle(): void
    {
        //loop through and chunk all the Emails and change the destination to EmailDestination Enum
        Email::query()->chunk(25, function ($mails) {
            foreach ($mails as $mail) {
                if ($mail->to_user) {
                    $mail->destination = EmailDestination::ALL_USERS;
                } else if ($mail->to_member) {
                    $mail->destination = EmailDestination::ALL_MEMBERS;
                } else if ($mail->to_pending) {
                    $mail->destination = EmailDestination::PENDING_MEMBERS;
                } else if ($this->to_active) {
                    $mail->destination = EmailDestination::ACTIVE_MEMBERS;
                } else if ($mail->to_list) {
                    $mail->destination = EmailDestination::EMAIL_LISTS;
                } else if ($mail->to_event) {
                    $mail->destination = EmailDestination::EVENT;
                } else if ($mail->to_backup) {
                    $mail->destination = EmailDestination::EVENT_WITH_BACKUP;
                } else {
                    $mail->destination = EmailDestination::NO_DESTINATION;
                }
                $mail->save();
            }
        });
    }
}
