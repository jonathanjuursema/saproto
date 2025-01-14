@extends('website.layouts.redesign.dashboard')

@section('page-title')
    View all codices
@endsection

@section('container')
    <div class="row">
        <div class="col">
            @include('codex.includes.codex_list')
        </div>

        <div class="col">
            @include('codex.includes.text_types', ['textTypes' => $textTypes])
        </div>

        <div class="col">
            @include('codex.includes.song_list', ['songTypes' => $songTypes])
        </div>
    </div>
@endsection
