<?php

namespace App\Models;

use Eloquent;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Helper Reminder Model.
 *
 * @property int $id
 * @property int $user_id
 * @property int $committee_id
 * @property-read Committee $committee
 * @property-read User $user
 *
 * @method static Builder|HelperReminder whereCommitteeId($value)
 * @method static Builder|HelperReminder whereId($value)
 * @method static Builder|HelperReminder whereUserId($value)
 * @method static Builder|HelperReminder newModelQuery()
 * @method static Builder|HelperReminder newQuery()
 * @method static Builder|HelperReminder query()
 *
 * @mixin Eloquent
 */
class HelperReminder extends Model
{
    protected $table = 'committees_helper_reminders';

    public $guarded = ['id'];

    public $timestamps = false;

    /** @return BelongsTo */
    public function committee()
    {
        return $this->belongsTo('App\Models\Committee');
    }

    /** @return BelongsTo */
    public function user()
    {
        return $this->belongsTo('App\Models\User');
    }
}
