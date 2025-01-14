<?php

namespace App\Models;

use Carbon;
use Eloquent;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;
use Illuminate\Support\Collection;
use Override;

/**
 * Withdrawal Model.
 *
 * @property int $id
 * @property string $date
 * @property bool $closed
 * @property int $total_users_associated
 * @property int $total_orderlines_associated
 * @property float $sum_associated_orderlines
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read Collection|Orderline[] $orderlines
 * @property-read Collection|FailedWithdrawal[] $failedWithdrawals
 * @property-read Collection|User[] $users
 * @property-read string $withdrawalId
 *
 * @method static Builder|Withdrawal whereClosed($value)
 * @method static Builder|Withdrawal whereCreatedAt($value)
 * @method static Builder|Withdrawal whereDate($value)
 * @method static Builder|Withdrawal whereId($value)
 * @method static Builder|Withdrawal whereUpdatedAt($value)
 * @method static Builder|Withdrawal newModelQuery()
 * @method static Builder|Withdrawal newQuery()
 * @method static Builder|Withdrawal query()
 *
 * @mixin Eloquent
 */
class Withdrawal extends Model
{
    protected $table = 'withdrawals';

    protected $guarded = ['id'];

    protected $appends = ['withdrawal_id'];

    #[Override]
    protected function casts(): array
    {
        return [
            'closed' => 'boolean',
        ];
    }

    public function orderlines(): HasMany
    {
        return $this->hasMany(OrderLine::class, 'payed_with_withdrawal');
    }

    public function orderlinesForUser(User $user): HasMany
    {
        return $this->orderlines()->where('user_id', $user->id);
    }

    /**
     * @return int
     */
    public function totalForUser(User $user): mixed
    {
        return OrderLine::query()->where('user_id', $user->id)->where('payed_with_withdrawal', $this->id)->sum('total_price');
    }

    public function getFailedWithdrawal(User $user): ?FailedWithdrawal
    {
        return FailedWithdrawal::query()->where('user_id', $user->id)->where('withdrawal_id', $this->id)->first();
    }

    public function failedWithdrawals(): HasMany
    {
        return $this->hasMany(FailedWithdrawal::class, 'withdrawal_id');
    }

    public function users(): HasManyThrough
    {
        return $this->hasManyThrough(User::class, OrderLine::class, 'payed_with_withdrawal', 'id', 'id', 'user_id')->distinct();
    }

    public function total(): mixed
    {
        return OrderLine::query()->where('payed_with_withdrawal', $this->id)->sum('total_price');
    }

    public function getWithdrawalIdAttribute(): string
    {
        return 'PROTO-'.$this->id.'-'.date('dmY', strtotime($this->date));
    }

    public function recalculateTotals(): void
    {
        $this->update([
            'total_users_associated' => $this->users()->count(),
            'total_orderlines_associated' => $this->orderlines()->count(),
            'sum_associated_orderlines' => $this->orderlines()->sum('total_price'),
        ]);
    }
}
