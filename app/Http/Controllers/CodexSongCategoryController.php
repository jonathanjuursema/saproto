<?php

namespace App\Http\Controllers;

use App\Models\CodexSongCategory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;

class CodexSongCategoryController extends Controller
{
    public function index() {}

    public function create()
    {
        return view('codex.song-category-edit', ['category' => null]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
        ]);

        $category = new CodexSongCategory;
        $category->name = $validated['name'];
        $category->save();

        return Redirect::route('codex.index');
    }

    public function show($id) {}

    public function edit(CodexSongCategory $codexSongCategory)
    {
        return view('codex.song-category-edit', ['category' => $codexSongCategory]);
    }

    public function update(Request $request, CodexSongCategory $codexSongCategory)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
        ]);

        $codexSongCategory->name = $validated['name'];
        $codexSongCategory->save();

        return Redirect::route('codex.index');
    }

    public function destroy(CodexSongCategory $codexSongCategory)
    {
        $codexSongCategory->songs()->delete();
        $codexSongCategory->delete();

        return Redirect::route('codex.index');
    }
}
