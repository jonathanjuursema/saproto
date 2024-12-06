<?php

namespace App\Console\Commands;

use App\Http\Controllers\UserAdminController;
use App\Mail\MembershipEndedForBoard;
use App\Models\Member;
use Carbon;
use Exception;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;

class EndMemberships extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'proto:endmemberships';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'This command terminates all memberships that have an end date in the past';

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
     * @throws Exception
     */
    public function handle(): void
    {
        $deleted = [];
        foreach (Member::query()->whereNotNull('until')->get() as $member) {
            if ($member->until < Carbon::now()->timestamp) {
                (new UserAdminController)->endMembership($member->user->id);
                $this->info("Membership from $member->proto_username ended!");
                $deleted[] = $member;
            }
        }

        if ($deleted !== []) {
            Mail::queue((new MembershipEndedForBoard($deleted))->onQueue('high'));
        } else {
            $this->info("No users who's membership to end!");
        }
    }
}
