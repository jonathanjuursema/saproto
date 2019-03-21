<?php

namespace Proto\Http\Controllers;

use Illuminate\Http\Request;

use Proto\Models\EmailList;
use Proto\Models\Event;
use Proto\Models\Account;
use Proto\Models\Achievement;
use Proto\Models\Activity;
use Proto\Models\Committee;
use Proto\Models\HelpingCommittee;
use Proto\Models\Permission;
use Proto\Models\Product;
use Proto\Models\ProductCategory;
use Proto\Models\ProductCategoryEntry;
use Proto\Models\Role;
use Proto\Models\Ticket;
use Proto\Models\User;

class ExportController extends Controller
{
    public function export($table, $personal_key)
    {

        $user = User::where('personal_key', $personal_key)->first();
        if (!$user || !$user->member) {
            abort(403, 'You do not have access to this data. You need a membership to access it.');
        }

        $data = null;

        switch ($table) {
            case 'user':
                $data = $user;
                break;
            case 'accounts':
                $data = Account::all();
                break;
            case 'achievement':
                $data = Achievement::all();
                break;
            case 'activities':
                if ($user->can('board')) {
                    $data = Activity::all();
                } else {
                    $data = Activity::with('event')->get()->filter(function ($val) {
                        return $val->event && $val->event->secret == 0;
                    });
                    foreach ($data as $key => $val) {
                        unset($data[$key]->event);
                    }
                }
                break;
            case 'committees':
                if ($user->can('board')) {
                    $data = Committee::all();
                } else {
                    $data = Committee::where('public', 1)->orWhereIn('id', array_values(config('proto.committee')))->get();
                }
                break;
            case 'committees_activities':
                $data = HelpingCommittee::all();
                break;
            case 'events':
                if ($user->can('board')) {
                    $data = Event::all();
                } else {
                    $data = Event::where('secret', 0)->get();
                }
                break;
            case 'mailinglists':
                if ($user->member) {
                    $data = EmailList::all();
                } else {
                    $data = EmailList::where('is_member_only', 0);
                }
                break;
            case 'permissions':
                $data = Permission::all();
                break;
            case 'products':
                $data = Product::all();
                break;
            case 'products_categories':
                $data = ProductCategoryEntry::all();
                break;
            case 'product_categories':
                $data = ProductCategory::all();
                break;
            case 'roles':
                $data = Role::all();
                break;
            case 'tickets':
                if ($user->member) {
                    $data = Ticket::all();
                } else {
                    $data = Ticket::where('members_only', 0);
                }
                break;
        }

        return $data;

    }
}
