<?php

namespace App\Console\Commands;

use DB;
use Illuminate\Console\Command;
use App\Http\Controllers\SpotifyController;

class SpotifySync extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'proto:spotifysync';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Sync Spotify playlist etc.';

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
     * @return void
     */
    public function handle()
    {
        $spotify = SpotifyController::getApi();
        $session = SpotifyController::getSession();

        $this->info('Testing if API key still works.');

        try {
            if ($spotify->me()->id != config('app-proto.spotify-user')) {
                $this->error('API key is for the wrong user!');

                return;
            }
        } catch (\SpotifyWebAPI\SpotifyWebAPIException $e) {
            if ($e->getMessage() == 'The access token expired') {
                $this->info('Access token expired. Trying to renew.');

                $refreshToken = $session->getRefreshToken();
                $session->refreshAccessToken($refreshToken);
                $accessToken = $session->getAccessToken();
                $spotify->setAccessToken($accessToken);

                SpotifyController::setSession($session);
                SpotifyController::setApi($spotify);
            } else {
                $this->error('Error using API key.');

                return;
            }
        }

        $this->info('Constructing ProTube hitlist.');

        $videos = [];

        // All-time
        $videos = array_merge($videos, DB::table('playedvideos')
            ->select(DB::raw('spotify_id, count(*) as count'))
            ->whereNotNull('spotify_id')->where('spotify_id', '!=', '')
            ->groupBy('video_title')->orderBy('count', 'desc')->limit(40)->get()->all());

        // Last month
        $videos = array_merge($videos, DB::table('playedvideos')
            ->select(DB::raw('spotify_id, count(*) as count'))
            ->whereNotNull('spotify_id')->where('spotify_id', '!=', '')
            ->where('created_at', '>', date('Y-m-d', strtotime('-1 month')))
            ->groupBy('video_title')->orderBy('count', 'desc')->limit(40)->get()->all());

        // Last week
        $videos = array_merge($videos, DB::table('playedvideos')
            ->select(DB::raw('spotify_id, count(*) as count'))
            ->whereNotNull('spotify_id')->where('spotify_id', '!=', '')
            ->where('created_at', '>', date('Y-m-d', strtotime('-1 week')))
            ->groupBy('video_title')->orderBy('count', 'desc')->limit(40)->get()->all());

        $uris = [];

        foreach ($videos as $video) {
            $uris[] = $video->spotify_id;
        }

        $uris = array_values(array_unique($uris));

        $this->info('---');

        $this->info('Updating playlist with '.count($uris).' songs.');

        $spotify->replacePlaylistTracks(config('app-proto.spotify-playlist'), []);

        $slice = 0;
        $batch_size = 75;
        while ($slice < count($uris)) {
            $add = array_values(array_slice($uris, $slice, $batch_size));
            $slice += $batch_size;
            $spotify->addPlaylistTracks(config('app-proto.spotify-user'), config('app-proto.spotify-playlist'), $add);
        }

        $this->info('Done!');
    }
}
