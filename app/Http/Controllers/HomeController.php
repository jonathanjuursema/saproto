<?php

namespace App\Http\Controllers;

use App\Models\Committee;
use App\Models\CommitteeMembership;
use App\Models\Company;
use App\Models\Dinnerform;
use App\Models\HeaderImage;
use App\Models\Newsitem;
use App\Models\User;
use App\Models\Video;
use App\Models\WelcomeMessage;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class HomeController extends Controller
{
    /** @return View Display the homepage. */
    public function show()
    {
        $companies = Company::query()
            ->where('in_logo_bar', true)
            ->inRandomOrder()
            ->get();

        $header = HeaderImage::inRandomOrder()->first();

        if (! Auth::user()?->is_member) {
            return view('website.home.external', ['companies' => $companies, 'header' => $header]);
        }
        $weekly = Newsitem::query()
            ->where('published_at', '<=', Carbon::now())
            ->where('published_at', '>', Carbon::now()->subWeeks(1))
            ->where('is_weekly', true)
            ->orderBy('published_at', 'desc')
            ->first();

        $newsitems = Newsitem::query()
            ->whereNotNull('published_at')
            ->where('published_at', '<=', Carbon::now())
            ->where('published_at', '>', Carbon::now()->subWeeks(2))
            ->where('id', '!=', $weekly?->id)
            ->orderBy('published_at', 'desc')
            ->take(3)
            ->get();

        $birthdays = User::query()
            ->whereHas('member', function ($q) {
                $q->where('is_pending', false);
            })
            ->where('show_birthday', true)
            ->where('birthdate', 'LIKE', date('%-m-d'))
            ->get();

        $dinnerforms = Dinnerform::query()
            ->where('closed', false)
            ->where('start', '<=', Carbon::now())
            ->where('end', '>', Carbon::now()->subHour())
            ->where('visible_home_page', true)
            ->orderBy('end')
            ->get();
        $videos = Video::query()
            ->orderBy('video_date', 'desc')
            ->where('video_date', '>', Carbon::now()->subMonths(3))
            ->limit(3)
            ->get();
        $message = WelcomeMessage::where('user_id', Auth::user()->id)->first();

        return view('website.home.members', ['companies' => $companies, 'message' => $message, 'newsitems' => $newsitems, 'weekly' => $weekly, 'birthdays' => $birthdays, 'dinnerforms' => $dinnerforms, 'header' => $header, 'videos' => $videos]);
    }

    /** @return View Display the most important page of the whole site. */
    public function developers()
    {
        $committee = Committee::where('slug', '=', config('proto.rootcommittee'))->first();
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
