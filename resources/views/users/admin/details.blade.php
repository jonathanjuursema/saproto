@extends('website.layouts.redesign.dashboard')

@section('page-title')
    User information: {{ $user->name }}
@endsection

@section('container')
    <div class="row">
        <div class="col-md-3">
            @include('users.admin.admin_includes.contact')

            @include('users.admin.admin_includes.update')

            @include('users.admin.admin_includes.omnomcomsound')
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
    @include('users.admin.admin_includes.addmember-modal')

    <!-- Modal for setting membership type -->
    @includeWhen($user->is_member, 'users.admin.admin_includes.setmembershiptype-modal')

    <!-- Modal for changing a users email -->
    @include('users.admin.admin_includes.changemail-modal')
@endsection
