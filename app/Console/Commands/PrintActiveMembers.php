<?php

namespace App\Console\Commands;

use App\Models\Member;
use Illuminate\Console\Command;

class PrintActiveMembers extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'proto:printactivemembers';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Print all active members of the association and the committees they are currently in.';

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
        foreach (Member::query()->whereHas('user', function ($q) {
            $q->whereHas('committees');
        })->get() as $member) {
            /** @var Member $member */
            $this->info(sprintf('%s: %s', $member->user->name, implode(', ', $member->user->committees->pluck('name')->toArray())));
        }
    }
}
