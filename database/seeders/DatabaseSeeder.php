<?php

namespace Database\Seeders;

use App;
use Exception;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Artisan;
use App\Console\ConsoleOutput;
use App\Models\User;

class DatabaseSeeder extends Seeder
{
    /**
     * Run the database seeders.
     *
     * @throws Exception
     */
    public function run()
    {
        if (App::environment('production')) {
            throw new Exception('You cannot seed your database outside the development environment.');
        }

        $output = new ConsoleOutput();

        Model::unguard();

        $output->task('setting roles and permissions', fn () => Artisan::call('proto:syncroles'));

        $adminPassword = str_random();

        $importSeeder = new ImportLiveDataSeeder();
        $importSeeder->run($adminPassword, $output);

        $otherSeeder = new OtherDataSeeder();
        $otherSeeder->run($output);

        Model::reguard();

        $adminUsername = User::find(1)->getPublicId();

        $output->info("<options=bold>password:</> <fg=green>$adminPassword</>
                       <options=bold>username:</> <fg=green>$adminUsername</>");
    }
}
