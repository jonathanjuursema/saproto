<?php

namespace App\Http\Controllers;

use App\Models\Activity;
use App\Models\Event;
use App\Models\OrderLine;
use App\Models\StorageEntry;
use App\Models\TicketPurchase;
use Illuminate\Database\Query\Builder;

class WrappedController extends Controller
{
    public function index()
    {
        $timespan = [now()->startOfYear(), now()->endOfYear()];
        $total_spent = round(OrderLine::whereBetween('created_at', $timespan)->sum('total_price'), 2);

        return response()->json([
            'total_spent' => $total_spent,
            'order_totals' => $this->orderTotals(),
            'events' => $this->eventList(),
            'user' => auth()->user(),
        ], 200, [], JSON_NUMERIC_CHECK);
    }

    public function orderTotals()
    {
        $totals = OrderLine::query()
            ->whereBetween('created_at', [now()->startOfYear(), now()->endOfYear()])
            ->selectRaw('product_id, SUM(units) as total')
            ->groupBy('product_id')
            ->groupBy('user_id')
            ->orderBy('product_id')
            ->orderBy('total')
            ->get();

        return $totals->groupBy('product_id')->map(function ($product) {
            return $product->pluck('total');
        });
    }

    public function eventList()
    {
        $events = Event::query()
            ->whereBetween('start', [now()->startOfYear()->timestamp, now()->endOfYear()->timestamp])
            ->where(function ($query) {
                $query->whereIn('id', function (Builder $query) {
                    $query->select('event_id')
                        ->from('tickets')
                        ->join('ticket_purchases', 'tickets.id', '=', 'ticket_purchases.ticket_id')
                        ->where('ticket_purchases.user_id', auth()->user()->id);
                })
                    ->orWhereIn('id', function (Builder $query) {
                        $query->select('event_id')
                            ->from('activities')
                            ->join('activities_users', 'activities.id', '=', 'activities_users.activity_id')
                            ->where('activities_users.user_id', auth()->user()->id);
                    });
            })
            ->select(['title', 'start', 'location', 'image_id', 'id'])
            ->groupBy('id')
            ->orderBy('start')
            ->get();

        $activity_prices = Activity::query()
            ->whereIn('event_id', $events->pluck('id'))
            ->select(['event_id', 'price'])->get();

        $ticket_prices = TicketPurchase::query()
            ->join('tickets', 'ticket_purchases.ticket_id', '=', 'tickets.id')
            ->join('products', 'tickets.product_id', '=', 'products.id')
            ->whereIn('tickets.event_id', $events->pluck('id'))
            ->where('ticket_purchases.user_id', auth()->user()->id)
            ->selectRaw('event_id, SUM(products.price) as total')
            ->groupBy('event_id')
            ->get();

        $images = StorageEntry::query()
            ->whereIn('files.id', $events->pluck('image_id'))
            ->join('events', 'events.image_id', '=', 'files.id')
            ->select(['files.*', 'events.id as event_id'])
            ->get();

        return $events
            ->map(function (Event $event) use ($activity_prices, $ticket_prices, $images) {
                $activity_price = $activity_prices->where('event_id', $event->id)->sum('price');
                $ticket_price = $ticket_prices->where('event_id', $event->id)->sum('total');

                $event->price = $activity_price + $ticket_price;

                $event->image_url = $images->where('event_id', $event->id)->first()->generateImagePath(null, null);

                return $event->only([
                    'title',
                    'start',
                    'location',
                    'formatted_date',
                    'image_url',
                    'price',
                ]);
            });

    }
}
