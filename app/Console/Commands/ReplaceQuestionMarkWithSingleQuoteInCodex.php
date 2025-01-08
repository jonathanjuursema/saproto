<?php

namespace App\Console\Commands;

use App\Models\CodexSong;
use Illuminate\Console\Command;

class ReplaceQuestionMarkWithSingleQuoteInCodex extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'proto:codexsongcleanup';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Replace all triple question marks with quotes in codex song texts.';

    /**
     * Execute the console command.
     */
    public function handle(): void
    {
        // foreach codex song loop through the lyrics and replace all triple question marks with quotes
        $songs = CodexSong::all();
        foreach ($songs as $song) {
            $song->lyrics = str_replace('???', "'", $song->lyrics);
            $song->save();
        }
    }
}
