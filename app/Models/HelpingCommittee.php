<?php

namespace App\Models;

use Carbon;
use Eloquent;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

/**
 * Helping Committee Model.
 *
 * @property int $id
 * @property int $activity_id
 * @property int $committee_id
 * @property int $amount
 * @property bool $notification_sent
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read Activity|null $activity
 * @property-read Committee|null $committee
 * @property-read Collection|User[]|null $users
 *
 * @method static Builder|HelpingCommittee whereActivityId($value)
 * @method static Builder|HelpingCommittee whereAmount($value)
 * @method static Builder|HelpingCommittee whereCommitteeId($value)
 * @method static Builder|HelpingCommittee whereCreatedAt($value)
 * @method static Builder|HelpingCommittee whereId($value)
 * @method static Builder|HelpingCommittee whereNotificationSent($value)
 * @method static Builder|HelpingCommittee whereUpdatedAt($value)
 * @method static Builder|HelpingCommittee newModelQuery()
 * @method static Builder|HelpingCommittee newQuery()
 * @method static Builder|HelpingCommittee query()
 *
 * @mixin Eloquent
 */
class HelpingCommittee extends Validatable
{
    protected $table = 'committees_activities';

    protected $guarded = ['id'];

    protected array $rules = [
        'activity_id' => 'required|integer',
        'committee_id' => 'required|integer',
        'amount' => 'required|integer',
    ];

    public function activity(): BelongsTo
    {
        return $this->belongsTo(Activity::class);
    }

    public function committee(): BelongsTo
    {
        return $this->belongsTo(Committee::class);
    }

    public function users(): BelongsToMany
    {
        return $this
            ->belongsToMany(User::class, 'activities_users', 'committees_activities_id')
            ->whereNull('activities_users.deleted_at')
            ->withTrashed();
    }
}
