<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * App\Models\StockMutation.
 *
 * @property int $id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property int $user_id
 * @property int $product_id
 * @property int $before
 * @property int $after
 * @property int $is_bulk
 * @property-read \App\Models\Product|null $product
 * @property-read \App\Models\User|null $user
 *
 * @method static \Illuminate\Database\Eloquent\Builder|StockMutation newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|StockMutation newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|StockMutation query()
 * @method static \Illuminate\Database\Eloquent\Builder|StockMutation whereAfter($value)
 * @method static \Illuminate\Database\Eloquent\Builder|StockMutation whereBefore($value)
 * @method static \Illuminate\Database\Eloquent\Builder|StockMutation whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|StockMutation whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|StockMutation whereIsBulk($value)
 * @method static \Illuminate\Database\Eloquent\Builder|StockMutation whereProductId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|StockMutation whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|StockMutation whereUserId($value)
 */
class StockMutation extends Model
{
    use HasFactory;

    protected $table = 'stock_mutations';

    protected $fillable = ['before', 'after', 'is_bulk'];

    public function product(): BelongsTo
    {
        return $this->belongsTo("App\Models\Product");
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo("App\Models\User");
    }

    public function date(): string
    {
        return $this->created_at->toDateString();
    }

    public function delta(): int
    {
        return $this->after - $this->before;
    }
}
