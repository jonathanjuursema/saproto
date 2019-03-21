<?php

namespace Proto\Console\Commands;

use Illuminate\Console\Command;
use Proto\Mail\TestMail;

use Mail;

class TestEmail extends Command
{

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'proto:testmail';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'This command sends a test e-mail message to see if stuff works.';

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

        $email = $this->ask('What is the destination for this e-mail?');

        Mail::to($email)->queue((new TestMail())->onQueue('high'));

        $this->info('Sent!');

    }

}
