<?php

namespace Proto\Models;

use Illuminate\Database\Eloquent\Model;

class CommitteeMembership extends Validatable
{
    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'committees_users';

    /**
     * @return mixed The user this association is for.
     */
    public function user() {
        return $this->belongsTo('Proto\Models\User');
    }

    /**
     * @return mixed The committee this association is for.
     */
    public function committee() {
        return $this->belongsTo('Proto\Models\Committee');
    }
    
    protected $guarded = ['id'];

    protected $rules = array(
        'user_id' => 'required|integer',
        'committee_id' => 'required|integer',
        'start' => 'required|integer',
        'end' => 'integer',
        'role' => 'string',
        'edition' => 'string'
    );
}