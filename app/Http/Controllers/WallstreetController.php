<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\StorageEntry;
use App\Models\WallstreetDrink;
use App\Models\WallstreetEvent;
use App\Models\WallstreetPrice;
use Carbon\Carbon;
use Illuminate\Contracts\Filesystem\FileNotFoundException;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Session;

class WallstreetController extends Controller
{
    public function index()
    {
        $allDrinks = WallstreetDrink::query()->orderby('start_time', 'desc')->get();

        return view('wallstreet.admin', ['allDrinks' => $allDrinks, 'currentDrink' => null]);
    }

    public function statistics($id)
    {
        return view('wallstreet.price-history', ['id' => $id]);
    }

    public function marquee()
    {
        $activeDrink = WallstreetController::active();

        if (! $activeDrink) {
            Session::flash('flash_message', 'There is no active drink to show the marquee screen for!');

            return Redirect::back();
        }

        $prices = $this->getLatestPrices($activeDrink);
        $sound_path = asset('sounds/kaching.mp3');

        return view('wallstreet.marquee', ['activeDrink' => $activeDrink, 'prices' => $prices, 'sound_path' => $sound_path]);
    }

    public function edit($id)
    {
        $currentDrink = WallstreetDrink::query()->find($id);
        $allDrinks = WallstreetDrink::query()->orderby('start_time', 'desc')->get();

        return view('wallstreet.admin', ['allDrinks' => $allDrinks, 'currentDrink' => $currentDrink]);
    }

    public function store(Request $request)
    {
        $drink = new WallstreetDrink;
        $drink->start_time = Carbon::parse($request->input('start_time'))->timestamp;
        $drink->end_time = Carbon::parse($request->input('end_time'))->timestamp;
        $drink->minimum_price = $request->input('minimum_price');
        $drink->price_increase = $request->input('price_increase');
        $drink->price_decrease = $request->input('price_decrease');
        $drink->random_events_chance = $request->input('random_events_chance');
        $drink->save();

        $allDrinks = WallstreetDrink::query()->orderby('start_time', 'desc')->get();
        Session::flash('flash_message', 'Wallstreet drink created. Do not forget to add products below!');

        return view('wallstreet.admin', ['allDrinks' => $allDrinks, 'currentDrink' => $drink]);
    }

    public function update(Request $request, $id)
    {
        $drink = WallstreetDrink::query()->findOrFail($id);
        $drink->start_time = Carbon::parse($request->input('start_time'))->timestamp;
        $drink->end_time = Carbon::parse($request->input('end_time'))->timestamp;
        $drink->minimum_price = $request->input('minimum_price');
        $drink->price_increase = $request->input('price_increase');
        $drink->price_decrease = $request->input('price_decrease');
        $drink->random_events_chance = $request->input('random_events_chance');
        $drink->save();

        $allDrinks = WallstreetDrink::query()->orderby('start_time', 'desc')->get();

        return view('wallstreet.admin', ['allDrinks' => $allDrinks, 'currentDrink' => $drink]);
    }

    public function destroy($id)
    {
        $drink = WallstreetDrink::query()->findOrFail($id);
        foreach ($drink->products as $product) {
            $drink->products()->detach($product->id);
        }

        $drink->delete();

        $prices = WallstreetPrice::query()->where('wallstreet_drink_id', $id)->get();
        foreach ($prices as $price) {
            $price->delete();
        }

        Session::flash('flash_message', 'Wallstreet drink and its affiliated price history deleted.');

        return Redirect::to(route('wallstreet::index'));
    }

    public function close($id): RedirectResponse
    {
        /** @var WallstreetDrink $drink */
        $drink = WallstreetDrink::query()->findOrFail($id);
        $drink->end_time = time();
        $drink->save();
        Session::flash('flash_message', 'Wallstreet drink closed.');

        return Redirect::back();
    }

    public function addProducts($id, Request $request)
    {
        /** @var WallstreetDrink $drink */
        $drink = WallstreetDrink::query()->findOrFail($id);
        $products = $request->input('product');
        $products = array_unique($products);
        foreach ($products as $product) {
            $drink->products()->syncWithoutDetaching($product);
        }

        Session::flash('flash_message', count($products).' Products added to Wallstreet drink.');

        return Redirect::to(route('wallstreet::edit', ['id' => $id]));
    }

    public function removeProduct($id, $productId)
    {
        /** @var WallstreetDrink $drink */
        $drink = WallstreetDrink::query()->findOrFail($id);
        $drink->products()->detach($productId);
        Session::flash('flash_message', 'Product removed from Wallstreet drink.');

        return Redirect::back();
    }

    public static function active()
    {
        return WallstreetDrink::query()->where('start_time', '<=', time())->where('end_time', '>=', time())->first();
    }

    public function getLatestPrices($drink)
    {
        $products = $drink->products()->select('name', 'price', 'id', 'image_id')->get();
        foreach ($products as $product) {
            /** @var Product $product */
            /** @phpstan-ignore-next-line */
            $product->img = is_null($product->image_url) ? '' : $product->image_url;

            $newPrice = WallstreetPrice::query()->where('product_id', $product->id)->orderBy('id', 'desc')->first();
            if (! $newPrice || $product->price === 0.0) {
                $product->price = $newPrice->price ?? $product->price;
                /** @phpstan-ignore-next-line */
                $product->diff = 0;

                continue;
            }

            /** @phpstan-ignore-next-line */
            $product->diff = ($newPrice->price - $product->price) / $product->price * 100;
            $product->price = $newPrice->price;
        }

        return $products;
    }

    public function getUpdatedPricesJSON(int $drinkID)
    {
        $drink = WallstreetDrink::query()->findOrFail($drinkID);
        $prices = $this->getLatestPrices($drink);
        $wrapped = ['products' => $prices];

        return Response::json($wrapped);
    }

    public function getAllPrices($drinkID)
    {
        return WallstreetDrink::query()->find($drinkID)->products()->with('wallstreetPrices', static function ($q) use ($drinkID) {
            $q->where('wallstreet_drink_id', $drinkID)->orderBy('id', 'asc');
        })->select('id', 'image_id', 'name')->get();
    }

    public function events()
    {
        $allEvents = WallstreetEvent::all();

        return view('wallstreet.admin_includes.wallstreetdrink-events', ['allEvents' => $allEvents, 'currentEvent' => null]);
    }

    /**
     * @throws FileNotFoundException
     */
    public function addEvent(Request $request)
    {
        $event = new WallstreetEvent;
        $event->name = $request->input('title');
        $event->description = $request->input('description');
        $event->percentage = $request->integer('percentage');

        $image = $request->file('image');
        if ($image) {
            $file = new StorageEntry;
            $file->createFromFile($image);
            $event->image()->associate($file);
        }

        $event->save();
        Session::flash('flash_message', 'Wallstreet event created. Do not forget to add products above!');

        return Redirect::to(route('wallstreet::events::edit', ['id' => $event->id]));
    }

    /**
     * @throws FileNotFoundException
     */
    public function updateEvent(Request $request, int $id)
    {
        $event = WallstreetEvent::query()->findOrFail($id);
        /** @var WallstreetEvent $event */
        $event->name = $request->input('title');
        $event->description = $request->input('description');
        $event->percentage = $request->integer('percentage');
        $image = $request->file('image');
        if ($image) {
            $file = new StorageEntry;
            $file->createFromFile($image);
            $event->image()->associate($file);
        } else {
            $event->image()->dissociate();
        }

        $event->save();

        return Redirect::to(route('wallstreet::events::edit', ['id' => $id]));
    }

    public function editEvent(int $id)
    {
        $currentEvent = WallstreetEvent::query()->find($id);
        $allEvents = WallstreetEvent::all();

        return view('wallstreet.admin_includes.wallstreetdrink-events', ['allEvents' => $allEvents, 'currentEvent' => $currentEvent]);
    }

    public function destroyEvent($id)
    {
        $currentEvent = WallstreetEvent::query()->findOrFail($id);
        /** @var WallstreetEvent $currentEvent */
        $currentEvent->products()->detach();
        $currentEvent->image()->dissociate();
        $currentEvent->save();
        $currentEvent->delete();

        return Redirect::to(route('wallstreet::events::index'));
    }

    public function toggleEvent(Request $request)
    {
        $event = WallstreetEvent::query()->findOrFail($request->input('id'));
        /** @var WallstreetEvent $event */
        $event->active = ! $event->active;
        $event->save();

        return Response::json(['active' => $event->active, 'id' => $event->id]);
    }

    public function addEventProducts($id, Request $request)
    {
        $event = WallstreetEvent::query()->findOrFail($id);
        $products = $request->input('product');
        $products = array_unique($products);
        foreach ($products as $product) {
            $event->products()->syncWithoutDetaching($product);
        }

        Session::flash('flash_message', count($products).' Products added to Wallstreet event.');

        return Redirect::to(route('wallstreet::events::edit', ['id' => $id]));
    }

    public function removeEventProduct($id, $productId)
    {
        $event = WallstreetEvent::query()->findOrFail($id);
        /** @var WallstreetEvent $event */
        $event->products()->detach($productId);
        Session::flash('flash_message', 'Product removed from Wallstreet Event.');

        return Redirect::back();
    }
}
