<?php

namespace App\Console\Commands;

use App\Enums\MembershipTypeEnum;
use App\Models\Achievement;
use App\Models\AchievementOwnership;
use App\Models\Activity;
use App\Models\ActivityParticipation;
use App\Models\CommitteeMembership;
use App\Models\Event;
use App\Models\OrderLine;
use App\Models\Product;
use App\Models\ProductCategory;
use App\Models\User;
use Carbon;
use Illuminate\Console\Command;

class AchievementsCron extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'proto:achievementscron';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Cronjob that automatically assigns achievements';

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
        $this->info('Automatically granting achievements to users...');

        // Get data that will be the same for every user.
        $first = [];
        foreach (Product::query()->where('is_visible')->get() as $product) {
            /** @var OrderLine $orderline */
            $orderline = $product->orderlines()->first();
            $first[] = $orderline->user_id;
        }

        $AmountOfSignupsThisMonth = Event::query()
            ->whereHas('activity')
            ->where('secret', false)
            ->where('start', '>', Carbon::now()->subMonth()->timestamp)
            ->where('end', '<', Carbon::now()->timestamp)
            ->count();

        $youDandy = $this->categoryProducts([9]);
        $fourOClock = $this->categoryProducts([11, 15, 18, 19]);
        $bigKid = $this->categoryProducts([21]);
        $goodHuman = $this->categoryProducts([28]);

        // Define the automatic achievements and their conditions.
        $achievements = [
            19 => fn ($user): bool => $this->achievementBeast($user), // Achievement Beast
            20 => fn ($user): bool => $this->nThProducts($user, [2], 5), // Hangry
            21 => fn ($user): bool => $this->nThProducts($user, [487], 15), // Cry Baby
            22 => fn ($user): bool => // weizen outside, grolsch weizen, weizen small, weizen big
            $this->nThProducts($user, [805, 211, 758, 1039], 20), // True German
            23 => fn ($user): bool => $this->oldFart($user), // Old Fart
            24 => function ($user) {
                $this->nThProducts($user, [22, 219, 419], 100);
            }, // I Am Bread
            25 => fn ($user): bool => $this->gottaCatchEmAll($user), // Gotta Catch em All
            26 => fn ($user): bool => $this->nThProducts($user, $youDandy, 3), // You Dandy
            27 => fn ($user): bool => $this->nThProducts($user, [180], 1) && ! $user->did_study_create, // Fristi Member
            28 => fn ($user): bool => $this->bigSpender($user), // Big Spender
            29 => fn ($user): bool => $this->percentageProducts($user, $fourOClock, 0.25), // Four 'O Clock
            // 30 => function($user) { return $this->percentageProducts($user, $this->categoriesProductIds([11, 15, 18, 19]), 0.25); }, # You're Special
            32 => fn ($user): bool => $this->percentageProducts($user, $bigKid, 0.25), // Big Kid
            38 => fn ($user): bool => $this->foreverMember($user), // Forever Member
            51 => fn ($user): bool => $this->first($user, $first), // First
            52 => fn ($user): bool => $this->nThProducts($user, [987], 777), // No Life
            53 => fn ($user): bool => $this->nThProducts($user, $goodHuman, 1), // Good Human
            54 => fn ($user): bool => $this->nThProducts($user, [39], 100), // I Am Noodle
            63 => fn ($user): bool => $this->nThActivity($user, 1), // First Activity
            64 => fn ($user): bool => $this->nThActivity($user, 100), // Hundredth Activity
            66 => fn ($user): bool => $this->percentageParticipation($user, 25, $AmountOfSignupsThisMonth), // 25% Participation Trophee
            67 => fn ($user): bool => $this->percentageParticipation($user, 50, $AmountOfSignupsThisMonth), // 50% Participation Trophee
            68 => fn ($user): bool => $this->percentageParticipation($user, 75, $AmountOfSignupsThisMonth), // 75% Participation Trophee
        ];

        // Check if the specified achievements actually exist.
        $existing = Achievement::all()->pluck('id')->toArray();
        foreach (array_keys($achievements) as $id) {
            if (! in_array($id, $existing)) {
                unset($achievements[$id]);
                $this->error("Achievement #{$id} does not exist, not granting this achievement.");
            }
        }

        // Loop over all current members and check if they should earn any new achievements.
        $users = User::withoutTrashed()
            ->whereHas('member', static function ($query) {
                $query->whereNot('membership_type', MembershipTypeEnum::PENDING);
            })
            ->get();
        $totalUsers = $users->count();

        foreach ($users as $index => $user) {
            $this->line(($index + 1).'/'.$totalUsers.' #'.$user->id);
            $alreadyAchieved = $user->achievements->pluck('id')->toArray();
            foreach ($achievements as $id => $check) {
                if (in_array($id, $alreadyAchieved)) {
                    continue;
                }

                if (! $check($user)) {
                    continue;
                }

                $this->giveAchievement($user, $id);
            }
        }

        $this->info('Finished automatically granting achievements!');
    }

    /**
     * Give an achievement to a user.
     *
     * @param  User  $user
     */
    private function giveAchievement($user, int $id): void
    {
        $achievement = Achievement::query()->find($id);

        AchievementOwnership::query()->updateOrCreate([
            'user_id' => $user->id,
            'achievement_id' => $id,
        ]);

        $this->line("Earned \033[32m$achievement->name\033[0m");
    }

    /**
     * Check if it is NOT the first of the month.
     */
    private function notFirstOfMonth(): bool
    {
        return Carbon::now()->day != 1;
    }

    /**
     * Achievement beast = earned 10 achievements or more.
     *
     * @param  User  $user
     */
    private function achievementBeast($user): bool
    {
        return $user->achievements->count() >= 10;
    }

    /**
     * Old Fart = member for more than 5 years.
     *
     * @param  User  $user
     */
    private function oldFart($user): bool
    {
        return $user->is_member && $user->member->created_at < Carbon::now()->subYears(5);
    }

    /**
     * Gotta catch 'em all! = be a member of at least 10 different committees.
     *
     * @param  User  $user
     */
    private function gottaCatchEmAll($user): bool
    {
        return $user->committees()->count() >= 10;
    }

    /**
     * Big spender = paid more than the max. amount of money in a month (=€250).
     *
     * @param  User  $user
     */
    private function bigSpender($user): bool
    {
        if ($this->notFirstOfMonth()) {
            return false;
        }

        $amount = $user->orderlines()
            ->where('updated_at', '>', Carbon::now()->subMonths())
            ->sum('total_price');

        return $amount >= 250;
    }

    /**
     * 4ever committee member = has been a committee member for more than three years.
     *
     * @param  User  $user
     */
    private function foreverMember($user): bool
    {
        foreach ($user->committees as $committee) {
            $memberships = CommitteeMembership::withTrashed()
                ->where('user_id', $user->id)
                ->where('committee_id', $committee->id)
                ->get();

            $days = 0;
            foreach ($memberships as $membership) {
                if ($membership->deleted_at != null) {
                    $diff = $membership->deleted_at->diff($membership->created_at);
                } else {
                    $diff = Carbon::now()->diff($membership->created_at);
                }

                $days += $diff->days;
            }

            if ($days >= 1095) {
                return true;
            }
        }

        return false;
    }

    /**
     * FIRST!!!! = the first to buy a product.
     *
     * @param  User  $user
     * @param  int[]  $firsts
     */
    private function first($user, array $firsts): bool
    {
        return in_array($user->id, $firsts);
    }

    /**
     * Attended a certain number of activities.
     *
     * @param  User  $user
     */
    private function nThActivity($user, int $n): bool
    {
        $participated = ActivityParticipation::query()->where('user_id', $user->id)->pluck('activity_id');
        $activities = Activity::query()->WhereIn('id', $participated)->pluck('event_id');
        $events = Event::query()->whereIn('id', $activities)->where('end', '<', Carbon::now()->timestamp);

        return $events->count() >= $n;
    }

    /**
     * Attended a certain percentage of signups in the last month.
     *
     * @param  User  $user
     * @param  int  $possibleSignups
     */
    private function percentageParticipation($user, int $percentage, $possibleSignups): bool
    {
        if ($this->notFirstOfMonth()) {
            return false;
        }

        if ($possibleSignups < 5) {
            return false;
        }

        $participated = ActivityParticipation::query()->where('user_id', $user->id)->pluck('activity_id');
        $activities = Activity::query()->WhereIn('id', $participated)->pluck('event_id');
        $EventsParticipated = Event::query()
            ->whereHas('activity')
            ->where('secret', false)
            ->where('start', '>', Carbon::now()->subMonth()->timestamp)
            ->where('end', '<', Carbon::now()->timestamp)
            ->whereIn('id', $activities)
            ->count();

        return floor($EventsParticipated / $possibleSignups * 100) >= $percentage;
    }

    /**
     * Bought a certain number of a set of products.
     *
     * @param  User  $user
     * @param  int[]  $products
     */
    private function nThProducts($user, array $products, int $n): bool
    {
        return $user->orderlines()->whereIn('product_id', $products)->sum('units') > $n;
    }

    /**
     * A percentage of purchases were of a certain set of products.
     *
     * @param  User  $user
     * @param  int[]  $products
     */
    private function percentageProducts($user, array $products, float $p): bool
    {
        $orders = OrderLine::query()
            ->where('updated_at', '>', Carbon::now()->subMonths())
            ->where('user_id', $user->id)
            ->count();
        $bought = OrderLine::query()
            ->where('updated_at', '>', Carbon::now()->subMonths())
            ->where('user_id', $user->id)
            ->whereIn('product_id', $products)
            ->count();

        return $bought > 0 && $orders > 0 && $bought / $orders > $p;
    }

    /**
     * Get the ids of the products in a set of categories.
     *
     * @param  int[]  $categories
     * @return int[]
     */
    private function categoryProducts(array $categories): array
    {
        $products = [];
        foreach ($categories as $category) {
            $products = array_merge($products, ProductCategory::query()->find($category)->sortedProducts()->pluck('id')->toArray());
        }

        return $products;
    }
}
