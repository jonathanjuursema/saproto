<?php

namespace App\Console\Commands;

use App\Jobs\SendManualEmail;
use App\Models\Email;
use Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Bus;
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
     * @throws \Throwable
     */
    public function handle(): void
    {
        // Send the emails from the email tool
        $emails = Email::query()->where('sent', false)->where('ready', true)->where('time', '<', Carbon::now()->timestamp)->get();
        $this->info('There are ' . $emails->count() . ' queued e-mails.');

        foreach ($emails as $email) {
            /** @var Email $email */
            $this->info('Sending e-mail <' . $email->subject . '>');

            $batch = Bus::batch($email->recipients()->map(
                fn($recipient) => new SendManualEmail($email, $recipient)
            ))->onConnection('medium')->onQueue('medium')->dispatch();

            $email->update([
                'job_batch_id' => $batch->id,
                'ready' => false,
                'sent' => true,
                'sent_to' => $email->recipients()->count()
            ]);

            $this->info('Sending email to ' . $email->recipients()->count() . ' people.');
        }

        $this->info(($emails->count() > 0 ? 'All e-mails sent.' : 'No e-mails to be sent.'));
    }
}
