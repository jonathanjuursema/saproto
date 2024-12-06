<?php

namespace App\Models;

use Carbon;
use Eloquent;
use Exception;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

/**
 * QrAuth Request Model.
 *
 * @property int $id
 * @property int $user_id
 * @property string $auth_token
 * @property string $qr_token
 * @property string $description
 * @property string|null $approved_at
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 *
 * @method static Builder|QrAuthRequest whereApprovedAt($value)
 * @method static Builder|QrAuthRequest whereAuthToken($value)
 * @method static Builder|QrAuthRequest whereCreatedAt($value)
 * @method static Builder|QrAuthRequest whereDescription($value)
 * @method static Builder|QrAuthRequest whereId($value)
 * @method static Builder|QrAuthRequest whereQrToken($value)
 * @method static Builder|QrAuthRequest whereUpdatedAt($value)
 * @method static Builder|QrAuthRequest whereUserId($value)
 * @method static Builder|QrAuthRequest newModelQuery()
 * @method static Builder|QrAuthRequest newQuery()
 * @method static Builder|QrAuthRequest query()
 *
 * @mixin Eloquent
 */
class QrAuthRequest extends Model
{
    protected $table = 'qrauth_requests';

    protected $guarded = ['id'];

    public function isApproved(): bool
    {
        return $this->approved_at !== null;
    }

    /**
     * @return false|User
     *
     * @throws Exception
     */
    public function authUser()
    {
        if ($this->approved_at) {
            /** @var User $user */
            $user = User::query()->findOrFail($this->user_id);
            $this->delete();

            return $user;
        }

        return false;
    }
}
