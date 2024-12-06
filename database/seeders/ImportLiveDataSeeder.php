<?php

namespace Database\Seeders;

use App\Models\Committee;
use App\Models\CommitteeMembership;
use App\Models\Member;
use App\Models\User;
use Exception;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;

class ImportLiveDataSeeder extends Seeder
{
    /**
     * This seeder imports some non-sensitive data from the live environment to make your development environment more 'real'.
     *
     *
     * @throws Exception
     */
    public function run(string $password, $output): void
    {
        // First let's create our admin user.
        $output->task('creating admin user.', fn () => self::createAdminUser($password));

        $output->info('Importing live data');

        // Now let's import all data we can from the live environment.
        $tables = [
            ['name' => 'accounts'],
            ['name' => 'achievement'],
            ['name' => 'activities'],
            ['name' => 'committees', 'excluded_columns' => ['image']],
            ['name' => 'committees_activities'],
            ['name' => 'companies'],
            ['name' => 'events', 'excluded_columns' => ['formatted_date', 'is_future', 'activity', 'category']],
            ['name' => 'event_categories'],
            ['name' => 'mailinglists'],
            ['name' => 'menuitems'],
            ['name' => 'products', 'excluded_columns' => ['image_url']],
            ['name' => 'products_categories'],
            ['name' => 'product_categories'],
            ['name' => 'tickets'],
        ];

        foreach ($tables as $table) {
            $output->task('importing '.$table['name'], fn () => self::createEntries(self::getDataFromExportApi($table['name']), $table));
        }

        // Now let's add our admin user to the committee and give them the sysadmin role.
        $output->task('assigning admin roles.', fn () => self::assignAdminRole());

    }

    /**
     * Import data from the live website export API.
     *
     * @param  $tableName  string The table to import from the live website.
     * @return mixed|null
     */
    public static function getDataFromExportApi($tableName)
    {
        $local_url = route('api::user::dev_export', ['personal_key' => Config::string('app-proto.personal-proto-key'), 'table' => $tableName]);
        $remote_url = str_replace(Config::string('app-proto.app-url'), 'https://www.proto.utwente.nl', $local_url);
        $response = Http::get($remote_url);
        if ($response->failed()) {
            return null;
        }

        return json_decode($response);
    }

    /**
     * @param  $entries  mixed
     * @param  $table  array
     */
    public static function createEntries($entries, array $table): void
    {
        foreach ($entries as $entry) {
            $entry = (array) $entry;

            if (array_key_exists('excluded_columns', $table)) {
                foreach ($table['excluded_columns'] as $column) {
                    unset($entry[$column]);
                }
            }

            DB::table($table['name'])->insert($entry);
        }
    }

    /**
     * @throws Exception
     */
    public static function createAdminUser(string $password): void
    {
        $userData = (array) self::getDataFromExportApi('user');
        if ($userData == null) {
            /** @var User $adminUser */
            $adminUser = User::factory()->has(Member::factory())->create(['id' => 1]);
            $adminUser->setPassword($password);

            // Stop the import dataseeder from here as the user does not have enough rights.
            throw new Exception(
                'You are not allowed to import data from the live website.'.PHP_EOL.
                'Make sure you are a member of the HYTTIOAOAc and have signed an NDA.'.PHP_EOL.
                'Otherwise you can continue without seeding the database.'
            );
        }

        $memberData = (array) ($userData['member'] ?? null);
        unset($userData['member']);
        unset($userData['photo']);
        unset($userData['roles']);
        unset($userData['is_member']);
        unset($userData['photo_preview']);
        unset($userData['welcome_message']);
        unset($userData['is_protube_admin']);
        unset($userData['use_dark_theme']);
        unset($userData['created_at']);
        unset($userData['permissions']);
        unset($memberData['created_at']);
        $userData['id'] = 1;
        $adminUser = User::query()->create($userData);
        $adminUser->save();
        if ($memberData !== []) {
            $newMember = Member::query()->create($memberData);
            $newMember->user_id = 1;
            $newMember->save();
        }

        $adminUser->setPassword($password);
    }

    public static function assignAdminRole(): void
    {
        $adminUser = User::query()->find(1);
        $root = Committee::query()->where('slug', Config::string('proto.rootcommittee'))->first();
        CommitteeMembership::query()->create([
            'user_id' => $adminUser->id,
            'committee_id' => $root->id,
            'role' => 'Automatically Added',
        ]);
        $adminUser->assignRole('sysadmin');
    }
}
