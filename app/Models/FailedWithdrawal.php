<?php

namespace App\Models;

use Eloquent;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

/**
 * Failed Withdrawal Model.
 *
 * @property int $id
 * @property int $withdrawal_id
 * @property int $user_id
 * @property int $correction_orderline_id
 * @property-read OrderLine $correction_orderline
 * @property-read User $user
 * @property-read Withdrawal $withdrawal
 *
 * @method static Builder|FailedWithdrawal whereCorrectionOrderlineId($value)
 * @method static Builder|FailedWithdrawal whereId($value)
 * @method static Builder|FailedWithdrawal whereUserId($value)
 * @method static Builder|FailedWithdrawal whereWithdrawalId($value)
 * @method static Builder|FailedWithdrawal newModelQuery()
 * @method static Builder|FailedWithdrawal newQuery()
 * @method static Builder|FailedWithdrawal query()
 *
 * @mixin Eloquent
 */
class FailedWithdrawal extends Model
{
    protected $table = 'withdrawals_failed';

    protected $guarded = ['id'];

    public $timestamps = false;

    /** @return BelongsTo */
    public function correctionOrderline()
    {
        return $this->belongsTo(OrderLine::class, 'correction_orderline_id');
    }

    /** @return HasOne */
    public function withdrawal()
    {
        return $this->hasOne(Withdrawal::class, 'withdrawal_id');
    }

    /** @return HasOne */
    public function user()
    {
        return $this->hasOne(User::class, 'user_id');
    }
}
