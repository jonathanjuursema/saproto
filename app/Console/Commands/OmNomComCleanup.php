<?php

namespace App\Console\Commands;

use App\Models\OrderLine;
use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class OmNomComCleanup extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'proto:omnomcleanup';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Anonymise all OmNomCom data older than 7 years for users who disabled their history.';

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

        $users = User::query()->where('keep_omnomcom_history', false)->pluck('id')->toArray();
        $orderlinesTable = (new OrderLine)->getTable();

        $affected = DB::table($orderlinesTable)
            ->whereIn('user_id', $users)
            ->where('created_at', '<', date('Y-m-d', strtotime('-7 years')))
            ->update(['user_id' => null]);

        $this->info("Found and anonymised {$affected} orderlines.");
    }
}
