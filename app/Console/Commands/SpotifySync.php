<?php

namespace App\Console\Commands;

use App\Http\Controllers\SpotifyController;
use DB;
use Illuminate\Console\Command;
use SpotifyWebAPI\SpotifyWebAPIException;

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

    private int $spotifyUpdateLimit = 99;

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
        } catch (SpotifyWebAPIException $e) {
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

        $this->info('Constructing ProTube hitlists.');

        // All-time
        $alltime = DB::table('playedvideos')
            ->selectRaw('spotify_id, count(*) as count')
            ->whereNotNull('spotify_id')
            ->where('spotify_id', '!=', '')
            ->groupBy('video_title')
            ->orderBy('count', 'desc')
            ->limit($this->spotifyUpdateLimit)
            ->pluck('spotify_id')
            ->toArray();

        $this->updatePlaylist($spotify, config('app-proto.spotify-alltime-playlist'), $alltime);

        // Last year
        $pastYear = DB::table('playedvideos')
            ->selectRaw('spotify_id, count(*) as count')
            ->whereNotNull('spotify_id')
            ->where('spotify_id', '!=', '')
            ->where('created_at', '>', date('Y-m-d', strtotime('-1 year')))
            ->groupBy('video_title')
            ->orderBy('count', 'desc')
            ->limit($this->spotifyUpdateLimit)
            ->pluck('spotify_id')
            ->toArray();

        $this->updatePlaylist($spotify, config('app-proto.spotify-pastyears-playlist'), $pastYear);

        // Last month
        $recent = DB::table('playedvideos')
            ->selectRaw('spotify_id, count(*) as count')
            ->whereNotNull('spotify_id')
            ->where('spotify_id', '!=', '')
            ->where('created_at', '>', date('Y-m-d', strtotime('-1 month')))
            ->groupBy('video_title')
            ->orderBy('count', 'desc')
            ->limit($this->spotifyUpdateLimit)
            ->pluck('spotify_id')
            ->toArray();

        $this->updatePlaylist($spotify, config('app-proto.spotify-recent-playlist'), $recent);

        $this->info('Done!');
    }

    public function updatePlaylist($spotify, $playlistId, $spotifyUris)
    {
        $this->info('---');

        $this->info('Updating playlist '.$playlistId.' with '.count($spotifyUris).' songs.');

        try {
            $spotify->replacePlaylistTracks($playlistId, $spotifyUris);
        } catch (SpotifyWebAPIException $e) {
            $this->error('Error updating playlist '.$playlistId.': '.$e->getMessage());

            return;
        }

        $this->info('Playlist '.$playlistId.' updated.');
    }
}
