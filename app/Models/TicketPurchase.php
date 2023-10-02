<?php

namespace App\Models;

use Carbon;
use Eloquent;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Ticket Purchase Model.
 *
 * @property int $id
 * @property int $ticket_id
 * @property int $orderline_id
 * @property int $user_id
 * @property string $barcode
 * @property bool $payment_complete
 * @property string|null $scanned
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read mixed $api_attributes
 * @property-read OrderLine $orderline
 * @property-read Ticket $ticket
 * @property-read User $user
 *
 * @method static Builder|TicketPurchase whereBarcode($value)
 * @method static Builder|TicketPurchase whereCreatedAt($value)
 * @method static Builder|TicketPurchase whereId($value)
 * @method static Builder|TicketPurchase whereOrderlineId($value)
 * @method static Builder|TicketPurchase wherePaymentComplete($value)
 * @method static Builder|TicketPurchase whereScanned($value)
 * @method static Builder|TicketPurchase whereTicketId($value)
 * @method static Builder|TicketPurchase whereUpdatedAt($value)
 * @method static Builder|TicketPurchase whereUserId($value)
 * @method static Builder|TicketPurchase newModelQuery()
 * @method static Builder|TicketPurchase newQuery()
 * @method static Builder|TicketPurchase query()
 *
 * @mixin Eloquent
 */
class TicketPurchase extends Model
{
    protected $table = 'ticket_purchases';

    protected $guarded = ['id'];

    /** @return BelongsTo */
    public function ticket()
    {
        return $this->belongsTo('App\Models\Ticket', 'ticket_id');
    }

    /** @return BelongsTo */
    public function orderline()
    {
        return $this->belongsTo('App\Models\OrderLine', 'orderline_id');
    }

    /** @return BelongsTo */
    public function user()
    {
        return $this->belongsTo('App\Models\User', 'user_id')->withTrashed();
    }

    /** @return bool*/
    public function canBeDownloaded()
    {
        return
            (! $this->ticket->is_prepaid) ||
            ($this->orderline->isPayed() && $this->orderline->payed_with_mollie === null) ||
            ($this->orderline->molliePayment?->translatedStatus() == 'paid');
    }

    /** @return array */
    public function getApiAttributesAttribute()
    {
        return [
            'id' => $this->id,
            'barcode' => $this->canBeDownloaded() ? $this->barcode : null,
            'scanned' => $this->scanned,
        ];
    }
}
