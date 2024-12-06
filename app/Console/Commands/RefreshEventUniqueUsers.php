<?php

namespace App\Console\Commands;

use App\Models\Event;
use Illuminate\Console\Command;

class RefreshEventUniqueUsers extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'proto:refreshuniqueusers';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'A command to recount the unique users count for all events.';

    /**
     * Execute the console command.
     */
    public function handle(): void
    {
        Event::query()->chunk(25, static function ($events) {
            foreach ($events as $event) {
                $event->updateUniqueUsersCount();
            }
        });
    }
}
