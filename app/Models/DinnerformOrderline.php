<?php

namespace App\Models;

use Carbon;
use Eloquent;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * DinnerformOrderline Model.
 *
 * @property int $id
 * @property int|null $user_id
 * @property int $dinnerform_id
 * @property string $description
 * @property float $price
 * @property bool $closed
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property bool|null $helper
 * @property-read User $user
 * @property-read Dinnerform $dinnerform
 * @property-read Dinnerform $price_with_discount
 *
 * @mixin Eloquent
 **/
class DinnerformOrderline extends Model
{
    use HasFactory;

    protected $table = 'dinnerform_orderline';

    protected $guarded = ['id'];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class)->withTrashed();
    }

    public function dinnerform(): BelongsTo
    {
        return $this->belongsTo(Dinnerform::class);
    }

    /** @return float Price of orderline reduced by possible discounts. */
    public function getPriceWithDiscountAttribute(): float
    {
        $with_regular_discount = $this->price * $this->dinnerform->regular_discount;
        $price = round($with_regular_discount, 2, PHP_ROUND_HALF_DOWN);

        if ($this->helper && $this->dinnerform->helper_discount) {
            $with_helper_discount = $price - $this->dinnerform->helper_discount;

            return max(0, $with_helper_discount);
        }

        return $price;
    }
}
