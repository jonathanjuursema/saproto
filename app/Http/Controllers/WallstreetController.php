<?php

namespace App\Http\Controllers;

use App\Models\OrderLine;
use App\Models\StorageEntry;
use App\Models\WallstreetDrink;
use App\Models\WallstreetEvent;
use App\Models\WallstreetPrice;
use Carbon\Carbon;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Session;
use Response;

class WallstreetController extends Controller
{
    public function admin()
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
        $currentDrink = WallstreetDrink::find($id);
        $allDrinks = WallstreetDrink::query()->orderby('start_time', 'desc')->get();

        return view('wallstreet.admin', ['allDrinks' => $allDrinks, 'currentDrink' => $currentDrink]);
    }

    public function store(Request $request)
    {
        $drink = new WallstreetDrink();
        $drink->start_time = Carbon::parse($request->input('start_time'))->timestamp;
        $drink->end_time = Carbon::parse($request->input('end_time'))->timestamp;
        $drink->minimum_price = $request->input('minimum_price');
        $drink->price_increase = $request->input('price_increase');
        $drink->price_decrease = $request->input('price_decrease');
        $drink->random_events = $request->has('random_events');
        $drink->save();

        $allDrinks = WallstreetDrink::query()->orderby('start_time', 'desc')->get();

        return view('wallstreet.admin', ['allDrinks' => $allDrinks, 'currentDrink' => $drink]);
    }

    public function update(Request $request, $id)
    {
        $drink = WallstreetDrink::findOrFail($id);
        $drink->start_time = Carbon::parse($request->input('start_time'))->timestamp;
        $drink->end_time = Carbon::parse($request->input('end_time'))->timestamp;
        $drink->minimum_price = $request->input('minimum_price');
        $drink->price_increase = $request->input('price_increase');
        $drink->price_decrease = $request->input('price_decrease');
        $drink->random_events = $request->has('random_events');
        $drink->save();

        $allDrinks = WallstreetDrink::query()->orderby('start_time', 'desc')->get();

        return view('wallstreet.admin', ['allDrinks' => $allDrinks, 'currentDrink' => $drink]);
    }

    public function destroy($id)
    {
        $drink = WallstreetDrink::findOrFail($id);
        foreach ($drink->products as $product) {
            $drink->products()->detach($product->id);
        }

        $drink->delete();

        $prices = WallstreetPrice::query()->where('wallstreet_drink_id', $id)->get();
        foreach ($prices as $price) {
            $price->delete();
        }

        Session::flash('flash_message', 'Wallstreet drink and its affiliated price history deleted.');

        return Redirect::to(route('wallstreet::admin'));
    }

    public function close($id): RedirectResponse
    {
        $drink = WallstreetDrink::findOrFail($id);
        $drink->end_time = time();
        $drink->save();
        Session::flash('flash_message', 'Wallstreet drink closed.');

        return Redirect::back();
    }

    public function addProducts($id, Request $request)
    {
        $drink = WallstreetDrink::findOrFail($id);
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
        $drink = WallstreetDrink::findOrFail($id);
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
            $product->img = is_null($product->image_url) ? '' : $product->image_url;

            $newPrice = WallstreetPrice::where('product_id', $product->id)->orderBy('id', 'desc')->first();
            if (! $newPrice || $product->price === 0) {
                $product->price = $newPrice->price ?? $product->price;
                $product->diff = 0;

                continue;
            }
            $product->diff = ($newPrice->price - $product->price) / $product->price * 100;
            $product->price = $newPrice->price;
        }

        return $products;
    }

    public function getUpdatedPricesJSON(int $drinkID)
    {
        $drink = WallstreetDrink::findOrFail($drinkID);
        $prices = $this->getLatestPrices($drink);
        $loss = $this->getLoss($drink);
        $events = $this->getLatestEvents($drink);
        $wrapped = ['products' => $prices, 'loss' => $loss, 'events' => $events];

        return Response::json($wrapped);
    }

    public function getLoss(WallstreetDrink $drink)
    {
        $productIDs = $drink->products->pluck('id');
        $orderlines = OrderLine::query()
            ->selectRaw('(original_unit_price*units)-total_price AS loss')
            ->whereHas('product', function ($q) use ($productIDs) {
                $q->whereIn('id', $productIDs);
            })
            ->where('created_at', '<', Carbon::parse($drink->end_time))
            ->where('created_at', '>', Carbon::parse($drink->start_time))
            ->get()
            ->sum('loss');

        return $orderlines;
    }

    public function getAllPrices($drinkID)
    {
        return WallstreetDrink::find($drinkID)->products()->with('wallstreetPrices', function ($q) use ($drinkID) {
            $q->where('wallstreet_drink_id', $drinkID)->orderBy('id', 'asc');
        })->select('id', 'image_id', 'name')->get();
    }

    public function getLatestEvents(WallstreetDrink $drink)
    {
        $events = $drink->events()->with('products')->get();
        foreach ($events as $event) {
            $event->img = $event->image->generatePath();
        }

        return $events;
    }

    public function events()
    {
        $allEvents = WallstreetEvent::all();

        return view('wallstreet.admin_includes.wallstreetdrink-events', ['allEvents' => $allEvents, 'currentEvent' => null]);
    }

    public function addEvent(Request $request)
    {
        $event = new WallstreetEvent();
        $event->name = $request->input('title');
        $event->description = $request->input('description');
        $event->percentage = $request->integer('percentage');

        $image = $request->file('image');
        if ($image) {
            $file = new StorageEntry();
            $file->createFromFile($image);
            $event->image()->associate($file);
        } else {
            $event->image()->dissociate();
        }

        $event->save();

        return Redirect::back();
    }

    public function updateEvent(Request $request, int $id)
    {
        $event = WallstreetEvent::findOrFail($id);
        $event->name = $request->input('title');
        $event->description = $request->input('description');
        $event->percentage = $request->integer('percentage');
        $image = $request->file('image');
        if ($image) {
            $file = new StorageEntry();
            $file->createFromFile($image);
            $event->image()->associate($file);
        } else {
            $event->image()->dissociate();
        }
        $event->save();

        return Redirect::back();
    }

    public function editEvent(int $id)
    {
        $currentEvent = WallstreetEvent::find($id);
        $allEvents = WallstreetEvent::all();

        return view('wallstreet.admin_includes.wallstreetdrink-events', ['allEvents' => $allEvents, 'currentEvent' => $currentEvent]);
    }

    public function addEventProducts($id, Request $request)
    {
        $event = WallstreetEvent::findOrFail($id);
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
        $event = WallstreetEvent::findOrFail($id);
        $event->products()->detach($productId);
        Session::flash('flash_message', 'Product removed from Wallstreet Event.');

        return Redirect::back();
    }
}
