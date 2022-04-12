@extends('website.layouts.redesign.dashboard')

@section('page-title')
    User information: {{ $user->name }}
@endsection

@section('container')

    <div class="row">

        <div class="col-md-3">

            @include('users.admin.admin_includes.contact')

            @include('users.admin.admin_includes.update')

        </div>

        <div class="col-md-3">

            @include('users.admin.admin_includes.actions')

        </div>

        <div class="col-md-3">

            @include('users.admin.admin_includes.membership')

        </div>

        <div class="col-md-3">

            @include('users.admin.admin_includes.hoofd')

        </div>

    </div>

    <!-- Modal for adding membership to user -->
    @include("users.admin.admin_includes.addmember-modal")

    <!-- Modal for removing membership from user -->
    @include("users.admin.admin_includes.removemember-modal")

    <!-- Modal for removing signed membership contract -->
    @include("users.admin.admin_includes.removememberform-modal")

    <!-- Modal for setting membership type -->
    @if($user->is_member)
        @include("users.admin.admin_includes.setmembershiptype-modal")
    @endif

@endsection
