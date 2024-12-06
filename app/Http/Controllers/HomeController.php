<?php

namespace App\Http\Controllers;

use App\Enums\MembershipTypeEnum;
use App\Models\Committee;
use App\Models\CommitteeMembership;
use App\Models\Company;
use App\Models\Dinnerform;
use App\Models\Event;
use App\Models\HeaderImage;
use App\Models\Newsitem;
use App\Models\PhotoAlbum;
use App\Models\User;
use App\Models\Video;
use App\Models\WelcomeMessage;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Config;
use Illuminate\View\View;

class HomeController extends Controller
{
    /** Display the homepage. */
    public function show()
    {

        $companies = Company::query()
            ->where('in_logo_bar', true)
            ->with('image')
            ->inRandomOrder()
            ->get();

        $header = HeaderImage::query()->inRandomOrder()->first();

        $albums = PhotoAlbum::query()->orderBy('date_taken', 'desc')
            ->with('thumbPhoto')
            ->where('published', true)
            ->take(4)
            ->get();

        if (! Auth::user()?->is_member) {
            return view('website.home.external', ['companies' => $companies, 'header' => $header, 'albums' => $albums]);
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
            ->whereHas('member', static function ($q) {
                $q->whereNot('membership_type', MembershipTypeEnum::PENDING);
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

        $message = WelcomeMessage::query()->where('user_id', Auth::user()->id)->first();

        $upcomingEventQuery = Event::getEventBlockQuery()
            ->where([
                ['end', '>=', date('U')],
                ['secret', false],
                [static function ($query) {
                    $query->where('publication', '<', date('U'))
                        ->orWhereNull('publication');
                }],
            ])
            ->orderBy('start')
            ->limit(6);

        $upcomingEvents = $upcomingEventQuery->clone()
            ->where('is_featured', false)
            ->get();

        $featuredEvents = $upcomingEventQuery->clone()
            ->where('is_featured', true)
            ->get();

        return view('website.home.members', [
            'upcomingEvents' => $upcomingEvents,
            'featuredEvents' => $featuredEvents,
            'companies' => $companies,
            'message' => $message,
            'newsitems' => $newsitems,
            'weekly' => $weekly,
            'birthdays' => $birthdays,
            'dinnerforms' => $dinnerforms,
            'header' => $header,
            'videos' => $videos,
            'albums' => $albums,
        ]);
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
