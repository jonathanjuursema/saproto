<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * Class WallstreetPrice.
 *
 * @property int $wallstreet_drink_id
 * @property float $price
 **/
class WallstreetPrice extends Model
{
    use HasFactory;

    protected $table = 'wallstreet_drink_prices';

    protected $fillable = ['wallstreet_drink_id', 'product_id', 'price'];

    public function drink()
    {
        return $this->belongsTo(WallstreetDrink::class, 'wallstreet_drink_id');
    }

    public function product()
    {
        return $this->belongsTo(Product::class, 'product_id');
    }
}
