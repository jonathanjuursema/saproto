<?php

namespace Proto\Console\Commands;

use Illuminate\Console\Command;

use Proto\Http\Controllers\SpotifyController;
use Proto\Http\Controllers\SlackController;

use DB;
use Proto\Models\PlayedVideo;

class SpotifyUpdate extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'proto:spotifyupdate';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Update ProTube history with Spotify URIs.';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {

        $spotify = SpotifyController::getApi();
        $session = SpotifyController::getSession();

        $this->info('Testing if API key still works.');

        try {
            if ($spotify->me()->id != config('app-proto.spotify-user')) {
                $this->error('API key is for the wrong user!');
                SlackController::sendNotification('[console *proto:spotify*] API key is for the wrong user.');
                return;
            }
        } catch (\SpotifyWebAPI\SpotifyWebAPIException $e) {
            if ($e->getMessage() == "The access token expired") {

                $this->info('Access token expired. Trying to renew.');

                $refreshToken = $session->getRefreshToken();
                $session->refreshAccessToken($refreshToken);
                $accessToken = $session->getAccessToken();
                $spotify->setAccessToken($accessToken);

                SpotifyController::setSession($session);
                SpotifyController::setApi($spotify);

            } else {
                $this->error('Error using API key.');
                SlackController::sendNotification('[console *proto:spotify*] Error using API key, please investigate.');
                return;
            }
        }

        $this->info('Constructing ProTube hitlist.');

        $videos = PlayedVideo::whereNull('spotify_id')->orderBy('id', 'desc')->limit(1000)->get();

        $videos_to_search = [];

        $strip = [
            #"  ",
            " official", "official ", "original", "optional",
            "video", "cover", "clip",
            " - ", " + ", "|", "(", ")", ":", "\"", ".",
            " &", " ft.", " ft", " feat",
            "audio", " music",
            " hd", "hq",
            "lyrics", "lyric",
            "sing  along", "singalong", "tekst", "ondertiteld", "subs",
        ];

        foreach ($videos as $video) {
            if (!in_array($video->video_title, array_keys($videos_to_search)) && strlen($video->video_title) > 0) {
                $videos_to_search[$video->video_title] = (object)[
                    'title' => $video->video_title,
                    'video_id' => $video->video_id,
                    'spotify_id' => $video->spotify_id,
                    'title_formatted' => preg_replace('/(\(.*|[^\S{2,}\s])/', '',
                        str_replace($strip, " ", strtolower($video->video_title))
                    ),
                    'count' => $video->count
                ];
            }
        }

        $this->info("Matching to Spotify music.\n---");

        foreach ($videos_to_search as $t => $video) {

            if (!$video->spotify_id) {

                $sameVideo = PlayedVideo::where('video_id', $video->video_id)->whereNotNull('spotify_id')->first();

                if ($sameVideo) {
                    DB::table('playedvideos')->where('video_id', $video->video_id)->update(['spotify_id' => $sameVideo->spotify_id, 'spotify_name' => $sameVideo->spotify_name]);
                    $this->info("Matched { $video->title } due to earlier occurrence.");
                    continue;
                }

                try {

                    $song = $spotify->search($video->title_formatted, 'track', ['limit' => 1])->tracks->items;
                    if (count($song) < 1) {
                        $this->error("Could not match { $video->title | $video->title_formatted } to a Spotify track.");
                        DB::table('playedvideos')->where('video_id', $video->video_id)->update(['spotify_id' => '', 'spotify_name' => 'Unknown on Spotify']);
                    } else {
                        $name = $song[0]->artists[0]->name . " - " . $song[0]->name;
                        $this->info("Matched { $video->title } to Spotify track { $name }.");
                        DB::table('playedvideos')->where('video_id', $video->video_id)->update(['spotify_id' => $song[0]->uri, 'spotify_name' => $name]);
                    }

                } catch (\SpotifyWebAPI\SpotifyWebAPIException $e) {
                    $err = $e->getCode() . ' error during search (' . $video->title_formatted . ') for track (' . $video->title . ').';
                    $this->error($err);
                    SlackController::sendNotification('[console *proto:spotify*] ' . $err);
                }

            }

        }

        $this->info("Done!");

    }
}
