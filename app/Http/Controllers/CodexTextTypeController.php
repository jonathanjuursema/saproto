<?php

namespace App\Http\Controllers;

use App\Models\CodexTextType;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;

class CodexTextTypeController extends Controller
{
    public function index() {}

    public function create()
    {
        return view('codex.text-type-edit', ['textType' => null]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'type' => 'required|string|max:255',
        ]);
        $type = new CodexTextType;
        $type->type = $validated['type'];
        $type->save();

        return Redirect::route('codex.index');
    }

    public function show(CodexTextType $codexTextType) {}

    public function edit(CodexTextType $codexTextType)
    {
        return view('codex.text-type-edit', ['textType' => $codexTextType]);
    }

    public function update(Request $request, CodexTextType $codexTextType)
    {
        $validated = $request->validate([
            'type' => 'required|string|max:255',
        ]);
        $codexTextType->type = $validated['type'];
        $codexTextType->save();

        return Redirect::route('codex.index');
    }

    public function destroy(CodexTextType $codexTextType)
    {
        $codexTextType->texts()->delete();
        $codexTextType->delete();

        return Redirect::route('codex.index');
    }
}
