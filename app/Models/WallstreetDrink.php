<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

/**
 * Class WallstreetDrink.
 *
 * @property int $end_time
 * @property int $start_time
 * @property string $name
 * @property int $id
 * @property float $minimum_price
 * @property float $price_decrease
 * @property float $price_increase
 * @property int $random_events_chance
 **/
class WallstreetDrink extends Model
{
    protected $table = 'wallstreet_drink';

    protected $fillable = ['end_time', 'start_time', 'name', 'minimum_price', 'price_increase', 'price_decrease'];

    public function isCurrent(): bool
    {
        return $this->start_time <= time() && $this->end_time >= time();
    }

    public function products(): BelongsToMany
    {
        return $this->belongsToMany(Product::class, 'product_wallstreet_drink');
    }

    public function orders()
    {
        $productIDs = $this->products()->pluck('id');

        return OrderLine::query()
            ->where('created_at', '>=', Carbon::createFromTimestamp($this->start_time))
            ->where('created_at', '<=', Carbon::createFromTimestamp($this->end_time))
            ->whereHas('product', function ($q) use ($productIDs) {
                $q->whereIn('id', $productIDs);
            });
    }

    public function events(): BelongsToMany
    {
        return $this->belongsToMany(WallstreetEvent::class, 'wallstreet_drink_event', 'wallstreet_drink_id', 'wallstreet_drink_events_id')->withPivot('id')->withTimestamps();
    }

    public function loss()
    {
        return $this->orders()
            ->selectRaw('(original_unit_price*units)-total_price AS loss')
            ->get()
            ->sum('loss');
    }
}
