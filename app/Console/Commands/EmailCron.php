<?php

namespace Proto\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;

use Proto\Mail\ManualEmail;
use Proto\Models\Email;

use Mail;

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
    public function handle()
    {

        // Send admin created e-mails.
        $emails = Email::where('sent', false)->where('ready', true)->where('time', '<', date('U'))->get();
        $this->info('There are ' . $emails->count() . ' queued e-mails.');

        foreach ($emails as $email) {

            $this->info('Sending e-mail <' . $email->subject . '>');

            $email->ready = false;
            $email->sent = true;
            $email->sent_to = $email->recipients()->count();
            $email->save();

            foreach ($email->recipients() as $recipient) {

                Mail::to($recipient)
                    ->queue((new ManualEmail(
                        ['email' => $email->sender_address . '@' . config('proto.emaildomain'), 'name' => $email->sender_name],
                        $email->subject,
                        $email->parseBodyFor($recipient),
                        $email->attachments,
                        $email->destinationForBody(),
                        $recipient->id,
                        $email->getEventName(),
                        $email->id)
                    )->onQueue('medium'));

            }

            $this->info('Sent to ' . $email->recipients()->count() . ' people.');

        }

        $this->info(($emails->count() > 0 ? 'All e-mails sent.' : 'No e-mails to be sent.'));

    }

}
