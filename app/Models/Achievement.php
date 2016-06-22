<?php

namespace Proto\Models;

use Illuminate\Database\Eloquent\Model;

class Achievement extends Model
{
    protected $table = 'achievements';

    protected $fillable = ['name', 'desc', 'img_file_id', 'tier'];

    public function users()
    {
        return $this->belongsToMany('Proto\Models\User', 'achievements_users');
    }

    public function achievementOwnership()
    {
        return $this->hasMany('Proto\Models\AchievementOwnership');
    }

    public function current($ismember = true)
    {
        $users = array();
        foreach ($this->users as $user) {
            if ((!$ismember || $user->member)) {
                $users[] = $user;
            }
        }
        return $users;
    }
}