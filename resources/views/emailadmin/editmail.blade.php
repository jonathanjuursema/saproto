@extends('website.layouts.redesign.dashboard')

@section('page-title')
    Create new e-mail
@endsection

@section('container')
    <div class="row justify-content-center">
        <div class="col-md-6">
            @include('emailadmin.admin_includes.email-details')
        </div>

        <div class="col-md-3">
            @include('emailadmin.admin_includes.variables')

            @include('emailadmin.admin_includes.attachments')

            @include('emailadmin.admin_includes.recipients')
        </div>
    </div>
@endsection
