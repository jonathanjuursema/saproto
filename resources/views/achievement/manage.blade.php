@extends('website.layouts.redesign.dashboard')

@section('page-title')
    {{ ! $achievement ? 'Create a new Achievement' : 'Edit Achievement ' . $achievement->name }}
@endsection

@section('container')
    <div class="row justify-content-center">
        <div class="col-md-3">
            @if ($achievement)
                @include(
                    'achievement.includes.achievement_include',
                    [
                        'achievement' => $achievement,
                        'obtained' => null,
                    ]
                )
            @endif

            @include('achievement.admin_includes.edit')
        </div>

        @if ($achievement)
            <div class="col-md-3">
                @include('achievement.admin_includes.icon')
            </div>
            <div class="col-md-3">
                @include('achievement.admin_includes.awards')
            </div>
        @endif
    </div>
@endsection
