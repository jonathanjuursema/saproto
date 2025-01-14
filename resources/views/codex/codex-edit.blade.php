@extends('website.layouts.redesign.dashboard')

@section('page-title')
    Edit codex
@endsection

@section('container')
    @php
        /** @var \App\Models\Codex $codex */
    @endphp

    <form
        action="{{ ! empty($codex) ? route('codex.update', ['codex' => $codex]) : route('codex.store') }}"
        method="POST"
    >
        <input
            type="hidden"
            name="_method"
            value="{{ ! empty($codex) ? 'PUT' : 'POST' }}"
        />
        {{ csrf_field() }}
        <div class="row gap-3">
            <div class="col">
                <div class="row">
                    @include('codex.includes.codex-details', ['codex' => $codex])
                </div>
                <div class="row">
                    @include('codex.includes.text_types', ['edit' => true, 'textTypes' => $textTypes, 'myTextTypes' => $myTextTypes])
                </div>
            </div>
            <div class="col">
                <div class="row">
                    <div class="card mb-3 p-3">
                        <div class="d-inline-flex justify-content-between">
                            <button
                                type="submit"
                                class="btn btn-success btn-block"
                            >
                                Save codex!
                            </button>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="card-body">
                        @include('codex.includes.song_list', ['edit' => true, 'songTypes' => $songTypes, 'mySongs' => $mySongs])
                    </div>
                </div>
            </div>
        </div>
    </form>
@endsection
