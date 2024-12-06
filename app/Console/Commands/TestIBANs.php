<?php

namespace App\Console\Commands;

use App\Models\Bank;
use Illuminate\Console\Command;

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
    public function handle(): void
    {
        $this->info('Starting clean-up.');

        foreach (Bank::all() as $bank) {
            if (! verify_iban($bank->iban)) {
                $this->info('INVALID -- '.$bank->iban.' of '.$bank->user->name.'(#'.$bank->user->id.')');

                continue;
            }

            if (! iban_country_is_sepa(iban_get_country_part($bank->iban))) {
                $this->info('NONSEPA -- '.$bank->iban.' of '.$bank->user->name.'(#'.$bank->user->id.')');

                continue;
            }
        }

        $this->info('Check complete.');
    }
}
