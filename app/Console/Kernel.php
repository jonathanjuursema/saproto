<?php

namespace App\Console;

use App\Models\WallstreetDrink;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * The Artisan commands provided by your application.
     *
     * @var array
     */
    protected $commands = [
        Commands\SyncRoles::class,
        Commands\TestEmail::class,
        Commands\EmailCron::class,
        Commands\NewsletterCron::class,
        Commands\BirthdayCron::class,
        Commands\AchievementsCron::class,
        Commands\FileCleanup::class,
        Commands\FeeCron::class,
        Commands\UserCleanup::class,
        Commands\CheckUtwenteAccounts::class,
        Commands\SpotifySync::class,
        Commands\SpotifyUpdate::class,
        Commands\TestIBANs::class,
        Commands\ClearSessionTable::class,
        Commands\VerifyPersonalDetailsEmailCron::class,
        Commands\HelperNotificationsCron::class,
        Commands\HelperReminderCron::class,
        Commands\PrintActiveMembers::class,
        Commands\ReviewFeedbackCron::class,
        Commands\MemberRenewCron::class,
        Commands\OmNomComCleanup::class,
        Commands\MakeAdmin::class,
        Commands\DirectAdminSync::class,
        Commands\SyncWikiAccounts::class,
        Commands\MemberCleanup::class,
        Commands\AddSysadmin::class,
        Commands\EndMemberships::class,
        Commands\UpdateWallstreetPrices::class,
        Commands\CodexMarkdownConverter::class,
    ];

    /**
     * Define the application's command schedule.
     *
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {
        $schedule->command('proto:emailcron')->everyMinute();
        $schedule->command('proto:dasync')->everyTenMinutes();
        $schedule->command('proto:spotifyupdate')->everyTenMinutes();
        $schedule->command('proto:usercleanup')->hourly();
        $schedule->command('proto:birthdaycron')->daily()->at('00:01');
        $schedule->command('proto:reviewfeedbackcron')->daily()->at('16:00');
        $schedule->command('proto:achievementscron')->daily()->at('00:10');
        $schedule->command('proto:clearsessions')->daily()->at('01:00');
        $schedule->command('proto:endmemberships')->hourly()->at('02:00');
        $schedule->command('proto:feecron')->daily()->at('03:00');
        $schedule->command('proto:membercleanup')->daily()->at('04:00');
        $schedule->command('proto:filecleanup')->daily()->at('05:00');
        $schedule->command('proto:spotifysync')->daily()->at('06:00');
        $schedule->command('proto:omnomcleanup')->daily()->at('07:00');
        $schedule->command('proto:helperremindercron')->daily()->at('08:00');
        $schedule->command('proto:helpernotificationcron')->daily()->at('10:00');
        $schedule->command('proto:checkutaccounts')->monthly();
        $schedule->command('proto:verifydetailscron')->monthlyOn(1, '12:00');

        $schedule->command('proto:updatewallstreetprices')->everyMinute()->when(function () {
            return WallstreetDrink::query()->where('start_time', '<=', time())->where('end_time', '>=', time())->count() > 0;
        });
    }
}
