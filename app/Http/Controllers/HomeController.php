<?php

namespace App\Http\Controllers;

use App\Mail\NewManualEmail;
use App\Models\Committee;
use App\Models\CommitteeMembership;
use App\Models\Email;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Config;
use Illuminate\View\View;

class HomeController extends Controller
{
    /** Display the homepage. */
    public function show(): NewManualEmail
    {
        return new NewManualEmail(Email::query()->firstOrFail(), Auth::user());
    }

    /** @return View Display the most important page of the whole site. */
    public function developers()
    {
        $committee = Committee::query()->where('slug', '=', Config::string('proto.rootcommittee'))->first();
        $developers = [
            'current' => CommitteeMembership::query()
                ->where('committee_id', $committee->id)
                ->groupBy('user_id')
                ->get(),
            'old' => CommitteeMembership::withTrashed()
                ->where('committee_id', $committee->id)
                ->whereNotNull('deleted_at')
                ->orderBy('created_at', 'ASC')
                ->groupBy('user_id')
                ->get(),
        ];

        return view('website.developers', ['developers' => $developers, 'committee' => $committee]);
    }

    /** @return View Display FishCam. */
    public function fishcam()
    {
        return view('misc.fishcam');
    }
}
