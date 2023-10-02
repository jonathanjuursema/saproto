<?php

namespace App\Console\Commands;

use Carbon;
use Illuminate\Console\Command;
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
    public function handle()
    {
        $this->info('Automatically granting achievements to users...');

        // Get data that will be the same for every user.
        $first = [];
        foreach (Product::where('is_visible')->get() as $product) {
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
            19 => function ($user) {
                return $this->achievementBeast($user);
            }, // Achievement Beast
            20 => function ($user) {
                return $this->nThProducts($user, [2], 5);
            }, // Hangry
            21 => function ($user) {
                return $this->nThProducts($user, [487], 15);
            }, // Cry Baby
            22 => function ($user) {
                return $this->nThProducts($user, [805, 211, 758], 20);
            }, // True German
            23 => function ($user) {
                return $this->oldFart($user);
            }, // Old Fart
            24 => function ($user) {
                $this->nThProducts($user, [22, 219, 419], 100);
            }, // I Am Bread
            25 => function ($user) {
                return $this->gottaCatchEmAll($user);
            }, // Gotta Catch em All
            26 => function ($user) use ($youDandy) {
                return $this->nThProducts($user, $youDandy, 3);
            }, // You Dandy
            27 => function ($user) {
                return $this->nThProducts($user, [180], 1) && ! $user->did_study_create;
            }, // Fristi Member
            28 => function ($user) {
                return $this->bigSpender($user);
            }, // Big Spender
            29 => function ($user) use ($fourOClock) {
                return $this->percentageProducts($user, $fourOClock, 0.25);
            }, // Four 'O Clock
            // 30 => function($user) { return $this->percentageProducts($user, $this->categoriesProductIds([11, 15, 18, 19]), 0.25); }, # You're Special
            32 => function ($user) use ($bigKid) {
                return $this->percentageProducts($user, $bigKid, 0.25);
            }, // Big Kid
            38 => function ($user) {
                return $this->foreverMember($user);
            }, // Forever Member
            51 => function ($user) use ($first) {
                return $this->first($user, $first);
            }, // First
            52 => function ($user) {
                return $this->nThProducts($user, [987], 777);
            }, // No Life
            53 => function ($user) use ($goodHuman) {
                return $this->nThProducts($user, $goodHuman, 1);
            }, // Good Human
            54 => function ($user) {
                return $this->nThProducts($user, [39], 100);
            }, // I Am Noodle
            63 => function ($user) {
                return $this->nThActivity($user, 1);
            }, // First Activity
            64 => function ($user) {
                return $this->nThActivity($user, 100);
            }, // Hundredth Activity
            66 => function ($user) use ($AmountOfSignupsThisMonth) {
                return $this->percentageParticipation($user, 25, $AmountOfSignupsThisMonth);
            }, // 25% Participation Trophee
            67 => function ($user) use ($AmountOfSignupsThisMonth) {
                return $this->percentageParticipation($user, 50, $AmountOfSignupsThisMonth);
            }, // 50% Participation Trophee
            68 => function ($user) use ($AmountOfSignupsThisMonth) {
                return $this->percentageParticipation($user, 75, $AmountOfSignupsThisMonth);
            }, // 75% Participation Trophee
        ];

        // Check if the specified achievements actually exist.
        $existing = Achievement::all()->pluck('id')->toArray();
        foreach (array_keys($achievements) as $id) {
            if (! in_array($id, $existing)) {
                unset($achievements[$id]);
                $this->error("Achievement #$id does not exist, not granting this achievement.");
            }
        }

        // Loop over all current members and check if they should earn any new achievements.
        $users = User::withoutTrashed()
            ->whereHas('member', function ($query) {
                $query->where('is_pending', false);
            })
            ->get();
        $totalUsers = $users->count();

        foreach ($users as $index => $user) {
            $this->line(($index + 1).'/'.$totalUsers.' #'.$user->id);
            $alreadyAchieved = $user->achievements->pluck('id')->toArray();
            foreach ($achievements as $id => $check) {
                if (! in_array($id, $alreadyAchieved) && $check($user)) {
                    $this->giveAchievement($user, $id);
                }
            }
        }

        $this->info('Finished automatically granting achievements!');
    }

    /**
     * Give an achievement to a user.
     *
     * @param  User  $user
     * @param  int  $id
     */
    private function giveAchievement($user, $id)
    {
        $achievement = Achievement::find($id);

        AchievementOwnership::updateOrCreate([
            'user_id' => $user->id,
            'achievement_id' => $id,
        ]);

        $this->line("Earned \033[32m$achievement->name\033[0m");
    }

    /**
     * Check if it is NOT the first of the month.
     *
     * @return bool
     */
    private function notFirstOfMonth()
    {
        return Carbon::now()->day != 1;
    }

    /**
     * Achievement beast = earned 10 achievements or more.
     *
     * @param  User  $user
     * @return bool
     */
    private function achievementBeast($user)
    {
        return count($user->achieved()) >= 10;
    }

    /**
     * Old Fart = member for more than 5 years.
     *
     * @param  User  $user
     * @return bool
     */
    private function oldFart($user)
    {
        return $user->is_member && $user->member->created_at < Carbon::now()->subYears(5);
    }

    /**
     * Gotta catch 'em all! = be a member of at least 10 different committees.
     *
     * @param  User  $user
     * @return bool
     */
    private function gottaCatchEmAll($user)
    {
        return $user->committees()->count() >= 10;
    }

    /**
     * Big spender = paid more than the max. amount of money in a month (=€250).
     *
     * @param  User  $user
     * @return bool
     */
    private function bigSpender($user)
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
     * @return bool
     */
    private function foreverMember($user)
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
     * @return bool
     */
    private function first($user, $firsts)
    {
        return in_array($user->id, $firsts);
    }

    /**
     * Attended a certain number of activities.
     *
     * @param  User  $user
     * @param  int  $n
     * @return bool
     */
    private function nThActivity($user, $n)
    {
        $participated = ActivityParticipation::where('user_id', $user->id)->pluck('activity_id');
        $activities = Activity::WhereIn('id', $participated)->pluck('event_id');
        $events = Event::whereIn('id', $activities)->where('end', '<', Carbon::now()->timestamp);

        return $events->count() >= $n;
    }

    /**
     * Attended a certain percentage of signups in the last month.
     *
     * @param  User  $user
     * @param  int  $percentage
     * @param  int  $possibleSignups
     * @return bool
     */
    private function percentageParticipation($user, $percentage, $possibleSignups)
    {
        if ($this->notFirstOfMonth()) {
            return false;
        }
        if ($possibleSignups < 5) {
            return false;
        }

        $participated = ActivityParticipation::where('user_id', $user->id)->pluck('activity_id');
        $activities = Activity::WhereIn('id', $participated)->pluck('event_id');
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
     * @param  int  $n
     * @return bool
     */
    private function nThProducts($user, $products, $n)
    {
        return $user->orderlines()->whereIn('product_id', $products)->sum('units') > $n;
    }

    /**
     * A percentage of purchases were of a certain set of products.
     *
     * @param  User  $user
     * @param  int[]  $products
     * @param  float  $p
     * @return bool
     */
    private function percentageProducts($user, $products, $p)
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
    private function categoryProducts($categories)
    {
        $products = [];
        foreach ($categories as $category) {
            $products = array_merge($products, ProductCategory::find($category)->products()->pluck('id')->toArray());
        }

        return $products;
    }
}
