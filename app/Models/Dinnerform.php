<?php

namespace App\Models;

use Carbon;
use Eloquent;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Auth;

/**
 * Dinnerform Model.
 *
 * @property int $id
 * @property string $restaurant
 * @property string $description
 * @property string $url
 * @property bool $closed
 * @property bool $visible_home_page
 * @property float $helper_discount
 * @property float $regular_discount
 * @property float $regular_discount_percentage
 * @property int $ordered_by_user_id
 * @property User $orderedBy
 * @property Carbon $start
 * @property Carbon $end
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read Event|null $event
 * @property-read Collection|Orderline[]|null $orderlines
 *
 * @method static Builder|Dinnerform whereCreatedAt($value)
 * @method static Builder|Dinnerform whereDescription($value)
 * @method static Builder|Dinnerform whereEnd($value)
 * @method static Builder|Dinnerform whereId($value)
 * @method static Builder|Dinnerform whereRestaurant($value)
 * @method static Builder|Dinnerform whereStart($value)
 * @method static Builder|Dinnerform whereUpdatedAt($value)
 * @method static Builder|Dinnerform whereUrl($value)
 * @method static Builder|Dinnerform newModelQuery()
 * @method static Builder|Dinnerform newQuery()
 * @method static Builder|Dinnerform query()
 *
 * @mixin Eloquent
 */
class Dinnerform extends Model
{
    use HasFactory;

    protected $table = 'dinnerforms';

    protected $guarded = ['id'];

    protected $hidden = ['created_at', 'updated_at'];

    protected $with = ['event'];

    /** @return BelongsTo */
    public function event()
    {
        return $this->belongsTo(Event::class);
    }

    public function orderedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'ordered_by_user_id');
    }

    /** @return HasMany */
    public function orderlines()
    {
        return $this->hasMany(DinnerformOrderline::class);
    }

    /** @return float The regular discount as a percentage out of 100. */
    public function getRegularDiscountPercentageAttribute(): int|float
    {
        return 100 - ($this->regular_discount * 100);
    }

    /** @return string A timespan string with format 'D H:i'. */
    public function generateTimespanText(): string
    {
        return $this->start->format('D H:i').' - '.Carbon::parse($this->end)->format('D H:i');
    }

    /** @return bool Whether the dinnerform is currently open. */
    public function isCurrent(): bool
    {
        return $this->start->isPast() && $this->end->isFuture();
    }

    /** @return bool Whether the dinnerform is more than 1 hour past it's end time. */
    public function hasExpired()
    {
        return $this->end->addHour()->isPast();
    }

    /** @return float Total amount of dinnerform orders without discounts. */
    public function totalAmount(): mixed
    {
        return $this->orderlines()->sum('price');
    }

    /** @return float Total amount of orderlines reduced by discounts. */
    public function totalAmountWithDiscount()
    {
        return $this->orderlines()->get()
            ->sum(static fn (DinnerformOrderline $orderline) => $orderline->price_with_discount);
    }

    /** @return int Number of orders. */
    public function orderCount(): int
    {
        return $this->orderlines()->count();
    }

    /** @return int Number of helpers. */
    public function helperCount()
    {
        return $this->orderlines()->where('helper', true)->distinct('user_id')->count();
    }

    /** @return bool Whether the current user is a helper for the event related to the dinnerform or has marked themselves as a helper. */
    public function isHelping(): bool
    {
        if ($this->orderlines()->where('user_id', Auth::id())->where('helper', true)->exists()) {
            return true;
        }

        return $this->event?->activity && $this->event->activity->isHelping(Auth::user());
    }

    /** @return bool Whether the current user has any discounts. */
    public function hasDiscount(): bool
    {
        return $this->regular_discount_percentage || ($this->helper_discount && $this->isHelping());
    }

    /** @return bool Whether the current user has made an order yet. */
    public function hasOrdered(): bool
    {
        return $this->orderlines()->where('user_id', Auth::id())->exists();
    }

    /** Delete related orders with dinnerform. */
    protected static function boot()
    {
        parent::boot();
        static::deleting(static function ($dinnerform) {
            foreach ($dinnerform->orderlines()->get() as $orderline) {
                $orderline->delete();
            }
        });
    }

    protected function casts(): array
    {
        return [
            'start' => 'datetime',
            'end' => 'datetime',
        ];
    }
}
