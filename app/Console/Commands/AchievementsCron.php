<?php

namespace Proto\Console\Commands;

use Illuminate\Console\Command;

use Carbon\Carbon;

use Proto\Models\Achievement;
use Proto\Models\ProductCategory;
use Proto\Models\StudyEntry;
use Proto\Models\User;
use Proto\Models\AchievementOwnership;
use Proto\Models\OrderLine;
use Proto\Models\Member;
use Proto\Models\CommitteeMembership;

use Proto\Http\Controllers\AchievementController;

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
        $this->info('Autoassigning achievements to users...');

        $this->giveAchievement($this->AchievementBeast(), 19);
        $this->giveAchievement($this->Hangry(), 20);
        $this->giveAchievement($this->CryBaby(), 21);
        $this->giveAchievement($this->TrueGerman(), 22);
        $this->giveAchievement($this->OldFart(), 23);
        $this->giveAchievement($this->IAmBread(), 24);
        $this->giveAchievement($this->GottaCatchEmAll(), 25);
        $this->giveAchievement($this->YouDandy(), 26);
        $this->giveAchievement($this->FristiMember(), 27);
        $this->giveAchievement($this->BigSpender(), 28);
        $this->giveAchievement($this->fourOClock(), 29);
        $this->giveAchievement($this->youreSpecial(), 30);
        $this->giveAchievement($this->bigKid(), 32);

        $this->info('Auto achievement gifting done!');
    }

    /**
     * Give an achievement to a list of users
     */
    private function giveAchievement($users, $achievement_id)
    {
        $changecount = 0;

        $achievement = Achievement::find($achievement_id);

        if ($achievement) {

            foreach ($users as $user) {

                if ($user) {

                    $achieved = $user->achieved();
                    $hasAchievement = false;

                    foreach ($achieved as $test) {
                        if ($test->id == $achievement_id) {
                            $hasAchievement = true;
                            break;
                        }
                    }

                    if (!$hasAchievement) {
                        $new = array(
                            'user_id' => $user->id,
                            'achievement_id' => $achievement_id
                        );
                        $relation = new AchievementOwnership($new);
                        $relation->save();
                        $changecount += 1;
                        $this->line('Achievement "' . $achievement->name . '" given to ' . $user->name);
                    } else {
//                        $this->line($achievement->name . ' already obtained by ' . $user->name . '.');
                    }
                } else {
                    $this->error('Cant find a certain user for ' . $achievement->name . '. User probably deleted.');
                }

            }

            $this->info('Gave away ' . $changecount . ' of achievement "' . $achievement->name . '".');

        } else {
            $this->error('Error! ' . $achievement_id . ' is a non-existing achievement ID. Skipping to next auto achievement.');
        }

    }


    /**
     *    ------------------------------------------------------------------------   ACHIEVEMENT LOGIC FUNCTIONS   --------------------------------------------------------------------------------
     */


    /**
     * Achievement beast = 10 achievements or more
     */
    private function AchievementBeast()
    {
        $users = User::all();
        $selected = array();
        foreach ($users as $user) {
            if (count($user->achieved()) >= 10) {
                $selected[] = $user;
            }
        }
        return $selected;
    }

    /**
     * Hangry = bought 5 snickers or more (all time)
     */
    private function Hangry()
    {
        $users = User::all();
        $selected = array();
        foreach ($users as $user) {
            $orders = Orderline::where('user_id', $user->id)->where('product_id', 2)->get();
            $count = 0;
            foreach ($orders as $order) {
                $count += $order->units;
            }
            if ($count >= 5) {
                $selected[] = $user;
            }
        }
        return $selected;
    }

    /**
     * Cry baby = bought 15 surprise eggs or more
     */
    private function CryBaby()
    {
        $users = User::all();
        $selected = array();
        foreach ($users as $user) {
            $orders = Orderline::where('user_id', $user->id)->where('product_id', 487)->get();
            $count = 0;
            foreach ($orders as $order) {
                $count += $order->units;
            }
            if ($count >= 15) {
                $selected[] = $user;
            }
        }
        return $selected;
    }

    /**
     * True German = more than 20 Weizen in beer history
     */
    private function TrueGerman()
    {
        $users = User::all();
        $selected = array();
        foreach ($users as $user) {
            $orders = Orderline::where('user_id', $user->id)->whereIn('product_id', [805, 211, 758])->get();
            $count = 0;
            foreach ($orders as $order) {
                $count += $order->units;
            }
            if ($count >= 20) {
                $selected[] = $user;
            }
        }
        return $selected;
    }

    /**
     *  Old Fart = more than 5 years a member
     */
    private function OldFart()
    {
        $members = Member::where('deleted_at', NULL)->where('created_at', '<', Carbon::now()->subYears(5))->get();
        $selected = array();
        foreach ($members as $member) {
            $selected[] = User::find($member->user_id);
        }
        return $selected;
    }

    /**
     *  I am Bread = you bought more than 100 croque monsieurs
     */
    private function IAmBread()
    {
        $users = User::all();
        $selected = array();
        foreach ($users as $user) {
            $orders = Orderline::where('user_id', $user->id)->whereIn('product_id', [22, 219, 419])->get();
            $count = 0;
            foreach ($orders as $order) {
                $count += $order->units;
            }
            if ($count >= 100) {
                $selected[] = $user;
            }
        }
        return $selected;
    }

    /**
     *  Gotta catch 'em all! = at least in 10 different committees
     */
    private function GottaCatchEmAll()
    {
        $users = User::all();
        $selected = array();
        foreach ($users as $user) {
            $memberships = CommitteeMembership::withTrashed()->where('user_id', $user->id)->get();
            for ($i = 0; $i < count($memberships); $i++) {
                for ($j = $i + 1; $j < count($memberships); $j++) {
                    if ($memberships[$i]->committee_id == $memberships[$j]->committee_id) {
                        $memberships[$i] = NULL;
                        break;
                    }
                }
            }
            $count = 0;
            foreach ($memberships as $temp) {
                if ($temp != NULL) $count++;
            }
            if ($count >= 10) $selected[] = $user;
        }
        return $selected;
    }

    /**
     *  You dandy = you bought 3 different types of merchandise
     */
    private function YouDandy()
    {
        $users = User::all();
        $selected = array();
        $merch = ProductCategory::find(9)->products->pluck('id');
        foreach ($users as $user) {
            $merchorders = OrderLine::where('user_id', $user->id)->whereIn('product_id', $merch)->get();
            if (count($merchorders) > 3) $selected[] = $user;
        }
        return $selected;
    }

    /**
     *  Fristi Member = you're no CreaTer and you bought a Fristi
     */
    private function FristiMember()
    {
        $selected = array();
        $fristies = OrderLine::where('product_id', 180)->get();
        foreach ($fristies as $fristi) {
            $studies = StudyEntry::where('user_id', $fristi->user_id)->get();
            $creater = false;
            foreach ($studies as $study) {
                if ($study->study_id == 1) {
                    $creater = true;
                }
            }
            if (!$creater) {
                $selected[] = User::find($fristi->user_id);
            }
        }
        return $selected;
    }

    /**
     *  Big spender = you had the max money subtracted for 1 month (=€250)
     */
    private function BigSpender()
    {
        $selected = array();
        if (Carbon::now()->day == 1) {
            $users = User::all();
            foreach ($users as $user) {
                $orders = OrderLine::where('updated_at', '>', Carbon::now()->subMonths(1))->where('user_id', $user->id)->get();
                $cost = 0;
                foreach ($orders as $order) {
                    $cost += $order->total_price;
                    if ($cost >= 250) {
                        $selected[] = $user;
                        break;
                    }
                }
            }
        } else {
            $this->info('Its not the first of the month! Cancelling Big Spender...');
        }
        return $selected;
    }

    /**
     *  It’s always 4 o’clock somewhere = a month where more than 25% of OmNomCom purchases is beer
     */
    private function fourOClock()
    {
        $selected = array();
        if (Carbon::now()->day == 1) {
            $beerIDs = ProductCategory::find(11)->products->pluck('id')->toArray();
            $beerIDs = array_merge($beerIDs, ProductCategory::find(15)->products->pluck('id')->toArray());
            $beerIDs = array_merge($beerIDs, ProductCategory::find(18)->products->pluck('id')->toArray());
            $beerIDs = array_merge($beerIDs, ProductCategory::find(19)->products->pluck('id')->toArray());
            $users = User::all();
            foreach ($users as $user) {
                $orders = OrderLine::where('updated_at', '>', Carbon::now()->subMonths(1))->where('user_id', $user->id)->get();
                $beers = OrderLine::where('updated_at', '>', Carbon::now()->subMonths(1))->where('user_id', $user->id)->whereIn('product_id', $beerIDs)->get();
                if (count($beers) > 0) {
                    if (count($beers) / count($orders) > 0.25) {
                        $selected[] = $user;
                    }
                }
            }
        } else {
            $this->info('Its not the first of the month! Cancelling It\'s always 4 o\'clock somewhere...');
        }
        return $selected;
    }

    /**
     *  You’re special = more than 25% of your beer purchases this month is special beer
     */
    private function youreSpecial()
    {
        $selected = array();
        if (Carbon::now()->day == 1) {
            $beerIDs = ProductCategory::find(11)->products->pluck('id')->toArray();
            $beerIDs = array_merge($beerIDs, ProductCategory::find(15)->products->pluck('id')->toArray());
            $beerIDs = array_merge($beerIDs, ProductCategory::find(18)->products->pluck('id')->toArray());
            $beerIDs = array_merge($beerIDs, ProductCategory::find(19)->products->pluck('id')->toArray());
            $users = User::all();
            foreach ($users as $user) {
                $orders = OrderLine::where('updated_at', '>', Carbon::now()->subMonths(1))->where('user_id', $user->id)->get();
                $beers = OrderLine::where('updated_at', '>', Carbon::now()->subMonths(1))->where('user_id', $user->id)->whereIn('product_id', $beerIDs)->get();
                if (count($beers) > 0) {
                    if (count($beers) / count($orders) > 0.25) {
                        $selected[] = $user;
                    }
                }
            }
        } else {
            $this->info('Its not the first of the month! Cancelling You\'re special...');
        }
        return $selected;
    }

    /**
     *  Big kid = month where more than 25% of purchases is from the kid friendly category
     */
    private function bigKid()
    {
        $selected = array();
        if (Carbon::now()->day == 1) {
            $kidIDs = ProductCategory::find(21)->products->pluck('id')->toArray();
            $users = User::all();
            foreach ($users as $user) {
                $orders = OrderLine::where('updated_at', '>', Carbon::now()->subMonths(1))->where('user_id', $user->id)->get();
                $kidOrders = OrderLine::where('updated_at', '>', Carbon::now()->subMonths(1))->whereIn('product_id', $kidIDs)->where('user_id', $user->id)->get();
                if (count($kidOrders) > 0) {
                    if (count($kidOrders) / count($orders) > 0.25) {
                        $selected[] = $user;
                    }
                }
            }
        } else {
            $this->info('Its not the first of the month! Cancelling Big kid...');
        }
        return $selected;
    }

}