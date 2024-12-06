<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Models\Tempadmin;
use Carbon;
use Illuminate\Console\Command;

class TempAdminCleanup extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'proto:tempadmincleanup';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Deletes all Temp Protube Admin users before today.';

    /**
     * Execute the console command.
     */
    public function handle(): void
    {
        $this->info('Cleaning up Temp Protube Admin users...');

        $tempAdmins = Tempadmin::query()->where('end_at', '<', Carbon::now()->startOfDay())->get();
        $tempAdmins->each(function (Tempadmin $tempAdmin) {
            $tempAdmin->delete();
        });

        $this->info('Deleted '.$tempAdmins->count().' Temp Protube Admin users.');
    }
}
