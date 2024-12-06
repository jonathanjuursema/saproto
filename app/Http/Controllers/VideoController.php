<?php

namespace App\Http\Controllers;

use App\Models\Event;
use App\Models\Video;
use Exception;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Session;
use Illuminate\View\View;
use Youtube;

class VideoController extends Controller
{
    /** @return View */
    public static function index()
    {
        return view('videos.index', ['videos' => Video::all()]);
    }

    /** @return View */
    public static function publicIndex()
    {
        return view('videos.public_index', ['videos' => Video::query()->orderBy('video_date', 'desc')->get()]);
    }

    /**
     * @return View
     */
    public static function show(Request $request)
    {
        return view('videos.view', ['video' => Video::query()->findOrFail($request->id)]);
    }

    /**
     * @return RedirectResponse
     *
     * @throws Exception
     */
    public static function store(Request $request)
    {
        $youtube_id = $request->youtube_id;
        $youtube_video = Youtube::getVideoInfo($youtube_id);

        if ($youtube_video != null) {
            Session::flash('flash_message', 'This is an invalid YouTube video ID!');

            return Redirect::back();
        }

        if (! $youtube_video->status->embeddable) {
            Session::flash('flash_message', 'This video is not embeddable and therefore cannot be used on the site!');

            return Redirect::back();
        }

        if (Video::query()->where('youtube_id', $youtube_video->id)->count() > 0) {
            Session::flash('flash_message', 'This video has already been added!');

            return Redirect::back();
        }

        Video::query()->create([
            'title' => $youtube_video->snippet->title,
            'youtube_id' => $youtube_video->id,
            'youtube_title' => $youtube_video->snippet->title,
            'youtube_length' => $youtube_video->contentDetails->duration,
            'youtube_user_id' => $youtube_video->snippet->channelId,
            'youtube_user_name' => $youtube_video->snippet->channelTitle,
            'youtube_thumb_url' => $youtube_video->snippet->thumbnails->high->url,
            'video_date' => date('Y-m-d', strtotime($youtube_video->snippet->publishedAt)),
        ])->save();

        Session::flash('flash_message', sprintf('The video %s has been added!', $youtube_video->snippet->title));

        return Redirect::back();
    }

    /**
     * @return View
     */
    public static function edit(Request $request)
    {
        $video = Video::query()->findOrFail($request->id);

        return view('videos.edit', ['video' => $video]);
    }

    /**
     * @return RedirectResponse
     */
    public static function update(Request $request)
    {
        /** @var Video $video */
        $video = Video::query()->findOrFail($request->id);
        $video->video_date = date('Y-m-d', strtotime($request->video_date));
        $video->save();

        if ($request->has('event')) {
            $event = Event::query()->findOrFail($request->get('event'));
            $video->event_id = $event->id;
            $video->save();
        }

        Session::flash('flash_message', sprintf('The video %s has been updated!', $video->title));

        return Redirect::route('video::admin::index');
    }

    /**
     * @return RedirectResponse
     *
     * @throws Exception
     */
    public static function destroy(Request $request)
    {
        $video = Video::query()->findOrFail($request->id);
        Session::flash('flash_message', sprintf('The video <strong>%s</strong> has been deleted!', $video->title));
        $video->delete();

        return Redirect::back();
    }
}
