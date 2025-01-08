<?php

namespace App\Models;

use Carbon;
use Eloquent;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Query\Builder as QueryBuilder;
use Override;

/**
 * Committee Membership Model.
 *
 * @property int $id
 * @property int $user_id
 * @property int $committee_id
 * @property string|null $role
 * @property string|null $edition
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property Carbon|null $deleted_at
 * @property-read Committee $committee
 * @property-read User $user
 *
 * @method static bool|null forceDelete()
 * @method static bool|null restore()
 * @method static QueryBuilder|CommitteeMembership onlyTrashed()
 * @method static QueryBuilder|CommitteeMembership withTrashed()
 * @method static QueryBuilder|CommitteeMembership withoutTrashed()
 * @method static Builder|CommitteeMembership whereCommitteeId($value)
 * @method static Builder|CommitteeMembership whereCreatedAt($value)
 * @method static Builder|CommitteeMembership whereDeletedAt($value)
 * @method static Builder|CommitteeMembership whereEdition($value)
 * @method static Builder|CommitteeMembership whereId($value)
 * @method static Builder|CommitteeMembership whereRole($value)
 * @method static Builder|CommitteeMembership whereUpdatedAt($value)
 * @method static Builder|CommitteeMembership whereUserId($value)
 * @method static Builder|CommitteeMembership newModelQuery()
 * @method static Builder|CommitteeMembership newQuery()
 * @method static Builder|CommitteeMembership query()
 *
 * @mixin Eloquent
 */
class CommitteeMembership extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $table = 'committees_users';

    protected $guarded = ['id'];

    protected $hidden = ['id', 'committee_id', 'user_id'];

    /** @return BelongsTo */
    public function user()
    {
        return $this->belongsTo(User::class)->withTrashed();
    }

    /** @return BelongsTo */
    public function committee()
    {
        return $this->belongsTo(Committee::class);
    }

    #[Override]
    protected function casts(): array
    {
        return [
            'deleted_at' => 'datetime',
        ];
    }
}
