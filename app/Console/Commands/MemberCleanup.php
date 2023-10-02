<?php

namespace App\Console\Commands;

use Carbon;
use Exception;
use Illuminate\Console\Command;
use App\Models\Member;

class MemberCleanup extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'proto:membercleanup';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Delete all pending memberships older than a month.';

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
     *
     * @return int
     *
     * @throws Exception
     */
    public function handle()
    {
        $old_pending_memberships = Member::where('is_pending', true)->where('created_at', '<', Carbon::now()->subMonth())->get();
        foreach ($old_pending_memberships as $pending_membership) {
            $pending_membership->delete();
        }

        return 0;
    }
}
