<?php

namespace App\Console;

use App\Console\Commands\AchievementsCron;
use App\Console\Commands\AddSysadmin;
use App\Console\Commands\BirthdayCron;
use App\Console\Commands\CheckUtwenteAccounts;
use App\Console\Commands\ClearSessionTable;
use App\Console\Commands\DirectAdminSync;
use App\Console\Commands\EmailCron;
use App\Console\Commands\EndMemberships;
use App\Console\Commands\FeeCron;
use App\Console\Commands\FileCleanup;
use App\Console\Commands\GoogleSync;
use App\Console\Commands\MakeAdmin;
use App\Console\Commands\MemberCleanup;
use App\Console\Commands\MemberRenewCron;
use App\Console\Commands\NewsletterCron;
use App\Console\Commands\OmNomComCleanup;
use App\Console\Commands\PrintActiveMembers;
use App\Console\Commands\RefreshEventUniqueUsers;
use App\Console\Commands\ReplaceQuestionMarkWithSingleQuoteInCodex;
use App\Console\Commands\ReviewFeedbackCron;
use App\Console\Commands\SpotifySync;
use App\Console\Commands\SpotifyUpdate;
use App\Console\Commands\SyncRoles;
use App\Console\Commands\SyncUTAccounts;
use App\Console\Commands\SyncWikiAccounts;
use App\Console\Commands\TempAdminCleanup;
use App\Console\Commands\TestEmail;
use App\Console\Commands\TestIBANs;
use App\Console\Commands\UpdateWallstreetPrices;
use App\Console\Commands\UserCleanup;
use App\Console\Commands\VerifyPersonalDetailsEmailCron;
use App\Models\WallstreetDrink;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;
use Override;

class Kernel extends ConsoleKernel
{
    /**
     * The Artisan commands provided by your application.
     *
     * @var array
     */
    protected $commands = [
        SyncRoles::class,
        TestEmail::class,
        EmailCron::class,
        NewsletterCron::class,
        BirthdayCron::class,
        AchievementsCron::class,
        FileCleanup::class,
        FeeCron::class,
        UserCleanup::class,
        CheckUtwenteAccounts::class,
        SpotifySync::class,
        SpotifyUpdate::class,
        TestIBANs::class,
        ClearSessionTable::class,
        VerifyPersonalDetailsEmailCron::class,
        PrintActiveMembers::class,
        ReviewFeedbackCron::class,
        MemberRenewCron::class,
        OmNomComCleanup::class,
        MakeAdmin::class,
        DirectAdminSync::class,
        SyncWikiAccounts::class,
        MemberCleanup::class,
        AddSysadmin::class,
        EndMemberships::class,
        UpdateWallstreetPrices::class,
        RefreshEventUniqueUsers::class,
        ReplaceQuestionMarkWithSingleQuoteInCodex::class,
        TempAdminCleanup::class,
        SyncUTAccounts::class,
        GoogleSync::class,
    ];

    /**
     * Define the application's command schedule.
     */
    #[Override]
    protected function schedule(Schedule $schedule): void
    {
        $schedule->command('proto:emailcron')->everyMinute();
        $schedule->command('proto:spotifyupdate')->everyTenMinutes();
        $schedule->command('proto:usercleanup')->hourly();
        $schedule->command('proto:gsync')->at('00:01');
        $schedule->command('proto:birthdaycron')->daily()->at('00:01');
        $schedule->command('proto:achievementscron')->daily()->at('00:10');
        $schedule->command('proto:clearsessions')->daily()->at('01:00');
        $schedule->command('proto:endmemberships')->hourly()->at('02:00');
        $schedule->command('proto:syncutaccounts')->daily()->at('03:00');
        $schedule->command('proto:feecron')->daily()->at('03:30');
        $schedule->command('proto:membercleanup')->daily()->at('04:00');
        $schedule->command('proto:tempadmincleanup')->daily()->at('04:30');
        $schedule->command('proto:filecleanup')->daily()->at('05:00');
        $schedule->command('proto:spotifysync')->daily()->at('06:00');
        $schedule->command('proto:omnomcleanup')->daily()->at('07:00');
        $schedule->command('proto:checkutaccounts')->monthly();
        $schedule->command('proto:verifydetailscron')->monthlyOn(1, '12:00');
        $schedule->command('proto:reviewfeedbackcron')->daily()->at('16:00');

        $schedule->command('proto:updatewallstreetprices')->everyMinute()->when(static fn (): bool => WallstreetDrink::query()->where('start_time', '<=', time())->where('end_time', '>=', time())->exists());
    }
}
