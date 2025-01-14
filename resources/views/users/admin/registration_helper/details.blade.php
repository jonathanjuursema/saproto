@extends('website.layouts.redesign.dashboard')

@section('page-title')
    User information: {{ $user->name }}
@endsection

@section('container')
    <div class="row justify-content-center">
        <div class="col-12 col-sm-6 col-lg-3">
            <div class="col-12">
                @include('users.admin.admin_includes.hoofd')
            </div>

            <div class="col-12">
                @include('users.admin.admin_includes.contact')
            </div>
        </div>

        <div class="col-12 col-sm-6 col-lg-3">
            @include('users.admin.admin_includes.membership')
        </div>
    </div>

    <!-- Modal for adding membership to user -->
    @include('users.admin.admin_includes.addmember-modal')
@endsection
