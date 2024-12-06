<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

/**
 * Class WallstreetEvent.
 *
 * @property int $id
 * @property string $name
 * @property string $description
 * @property int $percentage
 * @property int $image_id
 * @property bool $active
 * @property StorageEntry $image
 * @property Product[] $products
 **/
class WallstreetEvent extends Model
{
    protected $table = 'wallstreet_drink_events';

    public function image(): BelongsTo
    {
        return $this->belongsTo(StorageEntry::class);
    }

    public function products(): BelongsToMany
    {
        return $this->belongsToMany(Product::class, 'wallstreet_drink_event_product', 'wallstreet_drink_event_id');
    }
}
