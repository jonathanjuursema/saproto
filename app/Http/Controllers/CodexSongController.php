<?php

namespace App\Http\Controllers;

use App\Models\CodexSong;
use App\Models\CodexSongCategory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Session;

class CodexSongController extends Controller
{
    public function index() {}

    public function create()
    {
        if (! CodexSongCategory::query()->count()) {
            Session::flash('flash_message', 'You need to add a song category first!');

            return Redirect::route('codex.index');
        }

        $categories = CodexSongCategory::query()->orderBy('name')->get();

        return view('codex.song-edit', ['song' => null, 'textType' => null, 'categories' => $categories, 'myCategories' => []]);
    }

    public function store(Request $request)
    {
        $song = new CodexSong;
        $this->saveSong($song, $request);

        return Redirect::route('codex.index');
    }

    public function show(CodexSong $codexSong) {}

    public function edit(CodexSong $codexSong)
    {
        $categories = CodexSongCategory::query()->orderBy('name')->get();

        $myCategories = $codexSong->category->id;

        return view('codex.song-edit', ['song' => $codexSong, 'categories' => $categories, 'myCategories' => $myCategories]);
    }

    public function update(Request $request, CodexSong $codexSong)
    {
        $this->saveSong($codexSong, $request);

        return Redirect::route('codex.index');
    }

    public function destroy(CodexSong $codexSong)
    {
        $codexSong->delete();

        return Redirect::route('codex.index');
    }

    private function saveSong(CodexSong $song, Request $request): void
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'artist' => 'required|string|max:255',
            'lyrics' => 'required|string',
            'youtube' => 'nullable|string|max:255',
            'category' => 'required|integer',
        ]);
        $song->title = $validated['title'];
        $song->artist = $validated['artist'];
        $song->lyrics = $validated['lyrics'];
        $song->youtube = $validated['youtube'];
        $song->category_id = $validated['category'];
        $song->save();
    }
}
