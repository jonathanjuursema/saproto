@extends('website.layouts.redesign.generic')

@section('page-title')
    Dashboard for {{ $user->name }}
@endsection

@section('container')

    <div class="row">

        <div class="col-xl-5 col-md-12">

            @include('users.dashboard.includes.membership')

            @include('users.dashboard.includes.account')

            @include('users.dashboard.includes.deleteaccount')

            @include('users.dashboard.includes.cardinfo')

        </div>

        <div class="col-xl-3 col-md-12">

            <a href="{{ route('api::gdpr_export') }}" target="_blank" class="btn btn-outline-info btn-block mb-3 ellipsis"
               onclick="return confirm('You are about to download all your personal data as a JSON file. This can take a few seconds. Continue?');">
                <strong>Download all my personal information. (GDPR)</strong>
            </a>

            @include('users.dashboard.includes.profilepic')

            @include('users.dashboard.includes.password')

            @include('users.dashboard.includes.allergies')

            @include('users.dashboard.includes.2fa')

            @include('users.dashboard.includes.roleinfo')

        </div>

        <div class="col-xl-4 col-md-12">

            @include('users.dashboard.includes.withdrawal')

            @include('users.dashboard.includes.maillists')

            @include('users.dashboard.includes.personal_key')

        </div>

    </div>

@endsection