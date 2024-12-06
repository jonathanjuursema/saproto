<?php

namespace App\Console\Commands;

use App\Models\OrderLine;
use App\Models\WallstreetDrink;
use App\Models\WallstreetEvent;
use App\Models\WallstreetPrice;
use Carbon;
use Illuminate\Console\Command;

class UpdateWallstreetPrices extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'proto:updatewallstreetprices';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Update the prices when a wallstreet drink is active';

    protected float $maxPriceMultiplier = 1.2;

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
    public function handle(): ?int
    {
        //get the wallstreet drink that is currently active
        /** @var WallstreetDrink|null $currentDrink */
        $currentDrink = WallstreetDrink::query()->where('start_time', '<=', time())->where('end_time', '>=', time())->first();
        if ($currentDrink === null) {
            $this->info('No active wallstreet drink found');

            return 0;
        }

        foreach ($currentDrink->products()->get() as $product) {
            //search for the latest price of the current product and if it does not exist take the current price
            $latestPrice = WallstreetPrice::query()->where('product_id', $product->id)->where('wallstreet_drink_id', $currentDrink->id)->orderBy('id', 'desc')->first();
            if ($latestPrice === null) {
                $this->info('No price found for product '.$product->id.' creating new price object with current price ('.$product->price.') for drink '.$currentDrink->id);
                $latestPrice = new WallstreetPrice([
                    'wallstreet_drink_id' => $currentDrink->id,
                    'product_id' => $product->id,
                    'price' => $product->price,
                ]);
                $latestPrice->save();

                continue;
            }

            $newOrderlines = OrderLine::query()->where('created_at', '>=', Carbon::now()->subMinute())->where('product_id', $product->id)->sum('units');
            //heighten the price if there are new orders and the price is not the actual price
            if ($newOrderlines > 0) {
                $delta = $newOrderlines * $currentDrink->price_increase;
                $newPriceObject = new WallstreetPrice([
                    'wallstreet_drink_id' => $currentDrink->id,
                    'product_id' => $product->id,
                    'price' => min($latestPrice->price + $delta, $product->price * $this->maxPriceMultiplier),
                ]);
                $newPriceObject->save();
                $this->info($product->id.' has '.$newOrderlines.' new orderlines, increasing price by '.$delta.' to '.$newPriceObject->price);

                continue;
            }

            //lower the price if no orders have been made and the price is not the minimum price
            if ($latestPrice->price !== $currentDrink->minimum_price) {
                $newPriceObject = new WallstreetPrice([
                    'wallstreet_drink_id' => $currentDrink->id,
                    'product_id' => $product->id,
                    'price' => max($latestPrice->price - $currentDrink->price_decrease, $currentDrink->minimum_price),
                ]);

                $newPriceObject->save();

                $this->info($product->id.' has no new orderlines, lowering price by '.$currentDrink->price_decrease.' to '.$newPriceObject->price);

            } else {
                $this->info($product->id.' has no new orderlines and the price is already the minimum price');
            }
        }

        $randomEventQuery = WallstreetEvent::query()->inRandomOrder()->whereHas('products', static function ($q) use ($currentDrink) {
            $q->whereIn('products.id', $currentDrink->products->pluck('id'));
        })->where('active', true);

        //chance of 1 in random_events_chance (so about every random_events_chance minutes that a random event is triggered)
        if ($currentDrink->random_events_chance > 0 && $randomEventQuery->count() > 0 && random_int(1, $currentDrink->random_events_chance) === 1) {
            /** @var WallstreetEvent $randomEvent */
            $randomEvent = $randomEventQuery->first();
            $this->info('Random event '.$randomEvent->name.' triggered');
            $currentDrink->events()->attach($randomEvent->id);
            foreach ($randomEvent->products()->whereIn('products.id', $currentDrink->products->pluck('id'))->get() as $product) {
                $latestPrice = WallstreetPrice::query()->where('product_id', $product->id)->where('wallstreet_drink_id', $currentDrink->id)->orderBy('id', 'desc')->first();
                $delta = ($randomEvent->percentage / 100) * $product->price;
                $newPriceObject = new WallstreetPrice([
                    'wallstreet_drink_id' => $currentDrink->id,
                    'product_id' => $product->id,
                    'price' => max($currentDrink->minimum_price, min($delta + $latestPrice->price, $product->price * $this->maxPriceMultiplier)),
                ]);
                $newPriceObject->save();
            }
        }

        return null;
    }
}
