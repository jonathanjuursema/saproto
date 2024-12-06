<?php

namespace App\Http\Controllers;

use App\Models\CodexText;
use App\Models\CodexTextType;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Session;

class CodexTextController extends Controller
{
    public function index() {}

    public function create()
    {
        if (! CodexTextType::query()->count()) {
            Session::flash('flash_message', 'You need to add a text type first!');

            return Redirect::route('codex.index');
        }

        $textTypes = CodexTextType::query()->orderBy('type')->get();

        return view('codex.text-edit', ['text' => null, 'textTypes' => $textTypes, 'selectedTextType' => null]);
    }

    public function store(Request $request)
    {
        $text = new CodexText;
        $this->saveText($text, $request);

        return Redirect::route('codex.index');
    }

    public function show(CodexText $codexText) {}

    public function edit(CodexText $codexText)
    {
        $textTypes = CodexTextType::query()->orderBy('type')->get();
        $selectedTextType = $codexText->type;

        return view('codex.text-edit', ['text' => $codexText, 'textTypes' => $textTypes, 'selectedTextType' => $selectedTextType]);
    }

    public function update(Request $request, CodexText $codexText)
    {
        $this->saveText($codexText, $request);

        return Redirect::route('codex.index');
    }

    public function destroy(CodexText $codexText)
    {
        $codexText->codices()->detach();
        $codexText->delete();

        return Redirect::route('codex.index');
    }

    private function saveText(CodexText $codexText, Request $request): void
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'category' => 'required|integer',
            'text' => 'required|string',
        ]);
        $codexText->name = $validated['name'];
        $codexText->type_id = $validated['category'];
        $codexText->text = $validated['text'];
        $codexText->save();
    }
}
