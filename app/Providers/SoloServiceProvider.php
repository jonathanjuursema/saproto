<?php

namespace App\Providers;

use AaronFrancis\Solo\Commands\EnhancedTailCommand;
use AaronFrancis\Solo\Facades\Solo;
use AaronFrancis\Solo\Manager;
use Illuminate\Support\ServiceProvider;
use Override;

class SoloServiceProvider extends ServiceProvider
{
    #[Override]
    public function register(): void
    {
        // Solo may not (should not!) exist in prod, so we have to
        // check here first to see if it's installed.
        if (class_exists(Manager::class)) {
            $this->configure();
        }
    }

    public function configure(): void
    {
        Solo::useTheme('dark')
            // Commands that auto start.
            ->addCommands([
                'About' => 'php artisan solo:about',
                EnhancedTailCommand::make('Logs', 'tail -f -n 100 '.storage_path('logs/laravel.log')),
                'Vite' => 'npm run dev',
            ])
            // Not auto-started
            ->addLazyCommands([
                'Queue' => 'php artisan queue:listen --queue=high,medium,low,default --tries=1',
                'Kernel' => 'php artisan schedule:work',
                'Linting' => 'composer fix',
            ])
            // FQCNs of trusted classes that can add commands.
            ->allowCommandsAddedFrom([
                //
            ]);
    }

    public function boot(): void
    {
        //
    }
}
