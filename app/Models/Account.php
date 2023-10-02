<?php

namespace App\Models;

use Carbon;
use DB;
use Eloquent;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;
use stdClass;

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

    /** @return hasMany */
    public function products()
    {
        return $this->hasMany('App\Models\Product');
    }

    /**
     * @param  Collection  $orderlines
     * @return array<int, stdClass>
     */
    public static function generateAccountOverviewFromOrderlines($orderlines)
    {
        $accounts = [];

        foreach ($orderlines as $orderline) {
            // We sort by date, where a date goes from 6am - 6am.
            $sortDate = Carbon::createFromFormat('Y-m-d H:i:s', $orderline->created_at)->subHours(6)->toDateString();

            // Abbreviate variable names.
            $nr = $orderline->account_number;

            // Add account to dataset if not existing yet.
            if (! isset($accounts[$nr])) {
                $accounts[$nr] = (object) [
                    'byDate' => [],
                    'name' => $orderline->name,
                    'total' => 0,
                ];
            }

            // Add orderline to total account price.
            $accounts[$nr]->total += $orderline->total_price;

            // Add date to account data if not existing yet.
            if (! isset($accounts[$nr]->byDate[$sortDate])) {
                $accounts[$nr]->byDate[$sortDate] = 0;
            }

            // Add orderline to account-on-date total.
            $accounts[$nr]->byDate[$sortDate] += $orderline->total_price;
        }

        ksort($accounts);

        return $accounts;
    }

    /**
     * @param  int  $start
     * @param  int  $end
     * @return Collection
     */
    public function generatePeriodAggregation($start, $end)
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
            ->where('orderlines.created_at', '<', Carbon::parse(strval($end))->format('Y-m-d H:i:s'))->get();
    }
}
