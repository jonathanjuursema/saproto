<?php

namespace Proto\Console\Commands;

use Illuminate\Console\Command;

use Proto\Models\Bank;

class TestIBANs extends Command
{

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'proto:testibans';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Verify all active SEPA withdrawal contracts and check whether they seem valid and within the SEPA zone.';

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

        $this->info('Starting clean-up.');

        $count = 0;

        foreach (Bank::all() as $bank) {

            if (!verify_iban($bank->iban)) {
                $this->info('INVALID -- ' . $bank->iban . ' of ' . $bank->user->name . '(#' . $bank->user->id . ')');
                continue;
            }

            if (!iban_country_is_sepa(iban_get_country_part($bank->iban))) {
                $this->info('NONSEPA -- ' . $bank->iban . ' of ' . $bank->user->name . '(#' . $bank->user->id . ')');
                continue;
            }

        }

        $this->info("Check complete.");

    }

}
