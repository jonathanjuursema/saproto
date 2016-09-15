<?php

namespace Proto\Models;

use Illuminate\Database\Eloquent\Model;
use DB;

class Email extends Model
{

    protected $table = 'emails';

    protected $guarded = ['id'];

    public function lists()
    {
        return $this->belongsToMany('Proto\Models\EmailList', 'emails_lists', 'email_id', 'list_id');
    }

    public function attachments()
    {
        return $this->belongsToMany('Proto\Models\StorageEntry', 'emails_files', 'email_id', 'file_id');
    }

    public function destinationForBody()
    {
        if ($this->to_user) {
            return 'users';
        } elseif ($this->to_member) {
            return 'members';
        } elseif ($this->to_list) {
            return $this->lists->lists('name')->toArray();
        }
    }

    public function recipients()
    {
        if ($this->to_user) {
            return User::orderBy('name_first', 'asc')->get();
        } elseif ($this->to_member) {
            return User::has('member')->orderBy('name_first', 'asc')->get();
        } elseif ($this->to_list) {
            $userids = [];
            foreach ($this->lists as $list) {
                $userids = array_merge($userids, $list->users->lists('id')->toArray());
            }
            return User::whereIn('id', $userids)->orderBy('name_first', 'asc')->get();
        }
    }

    public function hasRecipientList(EmailList $list)
    {
        return DB::table('emails_lists')->where('email_id', $this->id)->where('list_id', $list->id)->count() > 0;
    }

    public function parseBodyFor(User $user)
    {
        $variable_from = ['$name_first', '$name_last'];
        $variable_to = [$user->name_first, $user->name_last];
        return str_replace($variable_from, $variable_to, $this->body);
    }

}
