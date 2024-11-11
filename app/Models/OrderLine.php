<?php

namespace App\Models;

use Carbon;
use Eloquent;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

/**
 * App\Models\OrderLine.
 *
 * @property int $id
 * @property int|null $user_id
 * @property int|null $cashier_id
 * @property int $product_id
 * @property float $original_unit_price
 * @property int $units
 * @property float $total_price
 * @property string $authenticated_by
 * @property string|null $payed_with_cash
 * @property string|null $payed_with_bank_card
 * @property int|null $payed_with_mollie
 * @property int|null $payed_with_withdrawal
 * @property bool $payed_with_loss
 * @property string|null $description
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read User|null $cashier
 * @property-read Product|null $product
 * @property-read MollieTransaction|null $molliePayment
 * @property-read TicketPurchase|null $ticketPurchase
 * @property-read User|null $user
 * @property-read Withdrawal|null $withdrawal
 *
 * @method static Builder|OrderLine whereAuthenticatedBy($value)
 * @method static Builder|OrderLine whereCashierId($value)
 * @method static Builder|OrderLine whereCreatedAt($value)
 * @method static Builder|OrderLine whereDescription($value)
 * @method static Builder|OrderLine whereId($value)
 * @method static Builder|OrderLine whereOriginalUnitPrice($value)
 * @method static Builder|OrderLine wherePayedWithBankCard($value)
 * @method static Builder|OrderLine wherePayedWithCash($value)
 * @method static Builder|OrderLine wherePayedWithMollie($value)
 * @method static Builder|OrderLine wherePayedWithWithdrawal($value)
 * @method static Builder|OrderLine whereProductId($value)
 * @method static Builder|OrderLine whereTotalPrice($value)
 * @method static Builder|OrderLine whereUnits($value)
 * @method static Builder|OrderLine whereUpdatedAt($value)
 * @method static Builder|OrderLine whereUserId($value)
 * @method static Builder|OrderLine newModelQuery()
 * @method static Builder|OrderLine newQuery()
 * @method static Builder|OrderLine query()
 *
 * @mixin Eloquent
 */
class OrderLine extends Model
{
    use HasFactory;

    protected $table = 'orderlines';

    protected $guarded = ['id'];

    protected function casts(): array
    {
        return [
            'payed_with_loss' => 'boolean',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class)->withTrashed();
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    public function cashier(): BelongsTo
    {
        return $this->belongsTo(User::class)->withTrashed();
    }

    public function withdrawal(): BelongsTo
    {
        return $this->belongsTo(Withdrawal::class, 'payed_with_withdrawal');
    }

    public function molliePayment(): BelongsTo
    {
        return $this->belongsTo(MollieTransaction::class, 'payed_with_mollie');
    }

    public function ticketPurchase(): HasOne
    {
        return $this->hasOne(TicketPurchase::class, 'orderline_id');
    }

    public function scopeUnpayed(Builder $query): Builder
    {
        return $query->whereNull('payed_with_cash')
            ->whereNull('payed_with_bank_card')
            ->whereNull('payed_with_withdrawal')
            ->where('payed_with_loss', false)
            ->where(function ($query) {
                $query->whereDoesntHave('molliePayment')
                    ->orWhereHas('molliePayment', static function ($query) {
                        $query->whereNotIn('status', config('omnomcom.mollie.paid_statuses'));
                    });
            })
            ->where('total_price', '!=', 0);
    }

    public function isPayed(): bool
    {
        $mollie_payment = false;
        if ($this->payed_with_mollie !== null) {
            $mollie_payment = $this->molliePayment->translatedStatus();
        }

        return
            $this->total_price == 0 ||
            $this->payed_with_loss ||
            $this->payed_with_cash !== null ||
            $this->payed_with_withdrawal !== null ||
            $mollie_payment == 'paid' ||
            $this->payed_with_bank_card !== null;
    }

    public function canBeDeleted(): bool
    {
        return $this->total_price == 0 || ! $this->isPayed();
    }

    public function generateHistoryStatus(): string
    {
        if ($this->payed_with_loss) {
            return 'Loss';
        }

        if ($this->payed_with_withdrawal !== null) {
            return "Withdrawal <a href='".
                route('omnomcom::mywithdrawal', ['id' => $this->payed_with_withdrawal]).
                "'>#".
                $this->payed_with_withdrawal.
                '</a>';
        }

        if ($this->payed_with_cash !== null) {
            return 'Cash';
        }

        if ($this->payed_with_bank_card !== null) {
            return 'Bank Card';
        }

        if ($this->total_price == 0) {
            return 'Free!';
        }

        if ($this->payed_with_mollie !== null) {
            return match ($this->molliePayment->translatedStatus()) {
                'paid' => '<i class="fas fa-check ml-2 text-success"></i> - <a href=\''.
                    route('omnomcom::mollie::status', ['id' => $this->payed_with_mollie]).
                    "'>#".
                    $this->payed_with_mollie.
                    '</a>',
                'failed' => '<i class="fas fa-times ml-2 text-danger"></i> - <a href=\''.
                    route('omnomcom::mollie::status', ['id' => $this->payed_with_mollie]).
                    "'>#".
                    $this->payed_with_mollie.
                    '</a>',
                'open' => '<i class="fas fa-spinner ml-2 text-normal"></i> - <a href=\''.
                    route('omnomcom::mollie::status', ['id' => $this->payed_with_mollie]).
                    "'>#".
                    $this->payed_with_mollie.
                    '</a>',
                default => '<i class="fas fa-question ml-2 text-normal"></i> - <a href=\''.
                    route('omnomcom::mollie::status', ['id' => $this->payed_with_mollie]).
                    "'>#".
                    $this->payed_with_mollie.
                    '</a>',
            };
        }

        return 'Unpaid';
    }
}
