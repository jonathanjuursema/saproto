<?php

namespace App\Console\Commands;

use App\Http\Controllers\SpotifyController;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;
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
     */
    public function handle(): void
    {
        $spotify = SpotifyController::getApi();
        $session = SpotifyController::getSession();

        $this->info('Testing if API key still works.');

        try {
            if ($spotify->me()->id != Config::string('app-proto.spotify-user')) {
                $this->error('API key is for the wrong user!');

                return;
            }
        } catch (SpotifyWebAPIException $spotifyWebAPIException) {
            if ($spotifyWebAPIException->getMessage() === 'The access token expired') {
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

        $this->updatePlaylist($spotify, Config::string('app-proto.spotify-alltime-playlist'), $alltime);

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

        $this->updatePlaylist($spotify, Config::string('app-proto.spotify-pastyears-playlist'), $pastYear);

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

        $this->updatePlaylist($spotify, Config::string('app-proto.spotify-recent-playlist'), $recent);

        $this->info('Done!');
    }

    public function updatePlaylist($spotify, string $playlistId, $spotifyUris): void
    {
        $this->info('---');

        $this->info('Updating playlist '.$playlistId.' with '.count($spotifyUris).' songs.');

        try {
            $spotify->replacePlaylistTracks($playlistId, $spotifyUris);
        } catch (SpotifyWebAPIException $spotifyWebAPIException) {
            $this->error('Error updating playlist '.$playlistId.': '.$spotifyWebAPIException->getMessage());

            return;
        }

        $this->info('Playlist '.$playlistId.' updated.');
    }
}
