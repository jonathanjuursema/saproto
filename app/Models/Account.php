<?php

namespace App\Models;

use Carbon;
use Eloquent;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

/**
 * Account Model.
 *
 * @property int $id
 * @property int $account_number
 * @property string $name
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read Collection|Product[] $products
 *
 * @method static Builder|Account whereAccountNumber($value)
 * @method static Builder|Account whereCreatedAt($value)
 * @method static Builder|Account whereId($value)
 * @method static Builder|Account whereName($value)
 * @method static Builder|Account whereUpdatedAt($value)
 * @method static Builder|Account newModelQuery()
 * @method static Builder|Account newQuery()
 * @method static Builder|Account query()
 *
 * @mixin Eloquent
 */
class Account extends Model
{
    protected $table = 'accounts';

    protected $guarded = ['id'];

    public function products(): HasMany
    {
        return $this->hasMany(Product::class);
    }

    public function generatePeriodAggregation(int $start, int $end): Collection
    {
        return DB::table('orderlines')
            ->join('products', 'orderlines.product_id', '=', 'products.id')
            ->join('accounts', 'products.account_id', '=', 'accounts.id')
            ->select(
                'orderlines.product_id',
                'products.name',
                DB::raw('SUM(orderlines.units) as number_sold'),
                DB::raw('SUM(orderlines.total_price) as total_turnover')
            )
            ->groupby('orderlines.product_id')
            ->where('accounts.id', '=', $this->id)
            ->where('orderlines.created_at', '>=', Carbon::parse(strval($start))->format('Y-m-d H:i:s'))
            ->where('orderlines.created_at', '<', Carbon::parse(strval($end))->format('Y-m-d H:i:s'))
            ->get();
    }
}
