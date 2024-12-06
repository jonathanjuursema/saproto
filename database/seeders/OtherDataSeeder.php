<?php

namespace Database\Seeders;

use App\Enums\MembershipTypeEnum;
use App\Models\AchievementOwnership;
use App\Models\Activity;
use App\Models\ActivityParticipation;
use App\Models\Address;
use App\Models\Bank;
use App\Models\Committee;
use App\Models\CommitteeMembership;
use App\Models\Feedback;
use App\Models\Member;
use App\Models\Newsitem;
use App\Models\OrderLine;
use App\Models\Page;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Sequence;
use Illuminate\Database\Seeder;

class OtherDataSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run($output): void
    {
        $output->info('Seeding fake data');

        // Create users
        $n = 10;
        $output->task("creating {$n} regular users", function () use ($n) {
            $users = User::factory()->count($n)->create();
            foreach ($users as $user) {
                if (fake()->boolean(30)) {
                    Address::factory()->for($user)->create();
                }

                if (fake()->boolean(30)) {
                    Bank::factory()->for($user)->create();
                }
            }
        });

        // Create members
        $n = 90;
        $output->task("creating {$n} members", function () use ($n) {
            Member::factory()
                ->count($n - 5)
                ->create();

            Member::factory()
                ->count(4)
                ->state(new Sequence(
                    ['membership_type' => MembershipTypeEnum::LIFELONG],
                    ['membership_type' => MembershipTypeEnum::HONORARY],
                    ['membership_type' => MembershipTypeEnum::DONOR],
                    ['membership_type' => MembershipTypeEnum::PET],
                ))
                ->state([
                    'deleted_at' => null,
                ])
                ->create();
        });

        // Get users with completed membership
        $members = User::query()->whereHas('member', fn ($members) => $members->whereNot('membership_type', MembershipTypeEnum::PENDING))->get();

        // Create committee participations
        $committees = Committee::all();
        $output->task('creating committee memberships', function () use ($members, $committees) {
            foreach ($committees as $committee) {
                $n = fake()->numberBetween(1, $members->count() / 2);
                foreach ($members->random($n) as $member) {
                    CommitteeMembership::factory()
                        ->for($member)
                        ->for($committee)
                        ->create();
                }
            }
        });

        // Create orderlines
        $output->task('creating orderlines', function () use ($members) {
            foreach ($members as $member) {
                OrderLine::factory()
                    ->count(fake()->randomDigit())
                    ->for($member)
                    ->create();
            }
        });

        // Create AchievementOwnership
        $output->task('creating achievement ownerships', function () use ($members) {
            foreach ($members as $member) {
                AchievementOwnership::factory()
                    ->count(fake()->randomDigit())
                    ->for($member)
                    ->create();
            }
        });

        // Create activity participations
        $output->task('creating activity participations', function () use ($members) {
            $activities = Activity::query()->has('event')->orderBy('id', 'desc')->take(25)->get();
            foreach ($activities as $activity) {
                $n = fake()->numberBetween(1, $members->count());
                foreach ($members->random($n) as $member) {
                    ActivityParticipation::factory()
                        ->state(['activity_id' => $activity->id])
                        ->for($member)
                        ->create();
                }
            }
        });

        // Create pages
        $n = 10;
        $output->task("creating {$n} pages", fn () => Page::factory()->count($n)->create());

        //create quotes and good ideas
        $n = 100;
        $output->task("creating {$n} Good Ideas", fn () => Feedback::factory()->state(['feedback_category_id' => 1])->count($n)->create());
        $output->task("creating {$n} quotes", fn () => Feedback::factory()->state(['feedback_category_id' => 2])->count($n)->create());

        // Create newsitems and weekly newsitems
        $n = 40;
        $output->task("creating {$n} newsitems", fn () => Newsitem::factory()->count($n)->create());
    }
}
