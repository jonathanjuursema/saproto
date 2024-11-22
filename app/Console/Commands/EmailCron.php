<?php

namespace App\Console\Commands;

use App\Mail\ManualEmail;
use App\Models\Email;
use Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Mail;

class EmailCron extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'proto:emailcron';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Cronjob that sends all admin created e-mails';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     */
    public function handle(): void
    {

        // Send admin created e-mails.
        $emails = Email::query()->where('sent', false)->where('ready', true)->where('time', '<', Carbon::now()->getTimestamp())->get();
        $this->info('There are '.$emails->count().' queued e-mails.');

        foreach ($emails as $email) {
            /** @var Email $email */
            $this->info('Sending e-mail <'.$email->subject.'>');

            $email->ready = false;
            $email->sent = true;
            $email->sent_to = $email->recipients()->count();
            $email->save();

            foreach ($email->recipients() as $recipient) {
                Mail::to($recipient)
                    ->queue((new ManualEmail(
                        $email->sender_address.'@'.Config::string('proto.emaildomain'),
                        $email->sender_name,
                        $email->subject,
                        $email->parseBodyFor($recipient),
                        $email->attachments,
                        $email->destinationForBody(),
                        $recipient->id,
                        $email->events()->get(),
                        $email->id
                    )
                    )->onQueue('medium'));
            }

            $this->info('Sent to '.$email->recipients()->count().' people.');
        }

        $this->info(($emails->count() > 0 ? 'All e-mails sent.' : 'No e-mails to be sent.'));
    }
}
