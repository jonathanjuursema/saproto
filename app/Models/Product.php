<?php

namespace Proto\Models;

use Carbon;
use Eloquent;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

/**
 * Product Model.
 *
 * @property int $id
 * @property int $account_id
 * @property int|null $image_id
 * @property string $name
 * @property float $price
 * @property int $calories
 * @property string|null $supplier_id
 * @property int $stock
 * @property int $preferred_stock
 * @property int $max_stock
 * @property int $supplier_collo
 * @property int $is_visible
 * @property int $is_alcoholic
 * @property int $is_visible_when_no_stock
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read FinancialAccount $account
 * @property-read Collection|ProductCategory[] $categories
 * @property-read StorageEntry|null $image
 * @property-read Collection|OrderLine[] $orderlines
 * @property-read Ticket $ticket
 * @method static Builder|Product whereAccountId($value)
 * @method static Builder|Product whereCalories($value)
 * @method static Builder|Product whereCreatedAt($value)
 * @method static Builder|Product whereId($value)
 * @method static Builder|Product whereImageId($value)
 * @method static Builder|Product whereIsAlcoholic($value)
 * @method static Builder|Product whereIsVisible($value)
 * @method static Builder|Product whereIsVisibleWhenNoStock($value)
 * @method static Builder|Product whereMaxStock($value)
 * @method static Builder|Product whereName($value)
 * @method static Builder|Product wherePreferredStock($value)
 * @method static Builder|Product wherePrice($value)
 * @method static Builder|Product whereStock($value)
 * @method static Builder|Product whereSupplierCollo($value)
 * @method static Builder|Product whereSupplierId($value)
 * @method static Builder|Product whereUpdatedAt($value)
 * @mixin Eloquent
 */
class Product extends Model
{
    protected $table = 'products';

    protected $guarded = ['id'];

    protected $hidden = ['created_at', 'updated_at'];

    /** @return BelongsTo|FinancialAccount */
    public function account()
    {
        return $this->belongsTo('Proto\Models\FinancialAccount');
    }

    /** @return BelongsTo|StorageEntry */
    public function image()
    {
        return $this->belongsTo('Proto\Models\StorageEntry', 'image_id');
    }

    /** @return BelongsToMany|ProductCategory */
    public function categories()
    {
        return $this->belongsToMany('Proto\Models\ProductCategory', 'products_categories', 'product_id', 'category_id');
    }

    /** @return HasOne|Ticket */
    public function ticket()
    {
        return $this->hasOne('Proto\Models\Ticket', 'product_id');
    }

    /** @return HasMany|OrderLine[] */
    public function orderlines()
    {
        return $this->hasMany('Proto\Models\OrderLine');
    }

    /** @return bool */
    public function isVisible()
    {
        return ! (! $this->is_visible || $this->stock <= 0 && ! $this->is_visible_when_no_stock);
    }

    /**
     * @param User $user
     * @param int $amount
     * @param float|null $total_price
     * @param bool $withCash
     * @param bool $withBankCard
     * @param string|null $description
     * @param string $auth_method
     * @return int OrderLine id
     */
    public function buyForUser($user, $amount, $total_price = null, $withCash = false, $withBankCard = false, $description = null, $auth_method = 'none')
    {
        $this->stock -= $amount;
        $this->save();

        $total_price = $total_price ?? $this->price * $amount;

        $has_cashier = $withCash || $withBankCard;

        $orderline = OrderLine::create([
            'user_id' => ($has_cashier ? null : $user->id),
            'cashier_id' => ($has_cashier || $total_price == 0 ? $user->id : null),
            'product_id' => $this->id,
            'original_unit_price' => $this->price,
            'units' => $amount,
            'total_price' => $total_price,
            'payed_with_cash' => ($withCash ? date('Y-m-d H:i:s') : null),
            'payed_with_bank_card' => ($withBankCard ? date('Y-m-d H:i:s') : null),
            'description' => $description == '' ? null : $description,
            'authenticated_by' => $auth_method,
        ]);

        $orderline->save();
        return $orderline->id;
    }
}
