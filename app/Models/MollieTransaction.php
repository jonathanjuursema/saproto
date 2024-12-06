<?php

namespace App\Models;

use Carbon;
use Eloquent;
use Exception;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Config;
use Mollie;
use Mollie\Api\Exceptions\ApiException;
use Mollie\Api\Resources\Payment;

/**
 * Mollie Transaction Model.
 *
 * @property int $id
 * @property string $user_id
 * @property string $mollie_id
 * @property string $status
 * @property float $amount
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property string|null $payment_url
 * @property-read User $user
 * @property-read Collection|OrderLine[] $orderlines
 *
 * @method static Builder|MollieTransaction whereAmount($value)
 * @method static Builder|MollieTransaction whereCreatedAt($value)
 * @method static Builder|MollieTransaction whereId($value)
 * @method static Builder|MollieTransaction whereMollieId($value)
 * @method static Builder|MollieTransaction wherePaymentUrl($value)
 * @method static Builder|MollieTransaction whereStatus($value)
 * @method static Builder|MollieTransaction whereUpdatedAt($value)
 * @method static Builder|MollieTransaction whereUserId($value)
 * @method static Builder|MollieTransaction newModelQuery()
 * @method static Builder|MollieTransaction newQuery()
 * @method static Builder|MollieTransaction query()
 *
 * @mixin Eloquent
 */
class MollieTransaction extends Model
{
    protected $table = 'mollie_transactions';

    protected $guarded = ['id'];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class)->withTrashed();
    }

    public function orderlines(): HasMany
    {
        return $this->hasMany(OrderLine::class, 'payed_with_mollie');
    }

    /**
     * @throws ApiException
     */
    public function transaction(): Payment
    {
        return Mollie::api()
            ->payments
            ->get($this->mollie_id);
    }

    public static function translateStatus(string $status): string
    {
        if ($status === 'open' || $status === 'pending' || $status === 'draft') {
            return 'open';
        }

        if ($status === 'expired' ||
            $status === 'canceled' ||
            $status === 'failed' ||
            $status === 'charged_back' ||
            $status === 'refunded') {
            return 'failed';
        }

        if (in_array($status, config('omnomcom.mollie.paid_statuses'))) {
            return 'paid';
        }

        return 'unknown';
    }

    public function translatedStatus(): string
    {
        return self::translateStatus($this->status);
    }

    /**
     * @throws Exception
     */
    public function updateFromWebhook(): static
    {
        $mollie = Mollie::api()
            ->payments
            ->get($this->mollie_id);

        $new_status = self::translateStatus($mollie->status);

        $this->status = $mollie->status;

        if ($new_status !== 'open') {
            $this->payment_url = $mollie->getCheckoutUrl();
        }

        $this->save();

        if ($new_status === 'failed') {
            foreach ($this->orderlines as $orderline) {
                if ($orderline->product_id == Config::integer('omnomcom.mollie.fee_id')) {
                    $orderline->delete();

                    continue;
                }

                /*
                 * Handles the case where an orderline was an unpaid event ticket for which prepayment is required.
                 * If statement components:
                 * - The orderline should refer to a product which is a ticket.
                 * - The ticket should be a pre-paid ticket.
                 * - The ticket should not already been paid for. This would indicate someone charging back their payment. If this is the case someone should still pay.
                 */
                if (
                    $orderline->product->ticket &&
                    ! $orderline->ticketPurchase->payment_complete &&
                    ($orderline->product->ticket->is_prepaid || ! $orderline->user->is_member)
                ) {

                    $orderline->ticketPurchase?->delete();
                    $orderline->product->ticket->event->updateUniqueUsersCount();
                    $orderline->product->stock++;
                    $orderline->product->save();
                    $orderline->delete();

                    continue;
                }

                $orderline->payed_with_mollie = null;
                $orderline->save();
            }

            return $this;
        }

        if ($new_status === 'paid') {
            foreach ($this->orderlines as $orderline) {
                $orderline->ticketPurchase?->update([
                    'payment_complete' => true,
                ]);
            }
        }

        return $this;
    }
}
