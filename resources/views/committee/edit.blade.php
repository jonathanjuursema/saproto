@extends('website.layouts.redesign.dashboard')

@section('page-title')
    @if ($new)
        Create new committee
    @else
        Edit: {{ $committee->name }}
    @endif
@endsection

@section('container')
    <div class="row justify-content-center">
        <div class="col-md-5">
            @include('committee.include.form-committee')
        </div>

        @if (! $new)
            <div class="col-md-3">
                @include('committee.include.form-image')

                @include('committee.include.form-members')
            </div>

            <div class="col-md-4">
                @include('committee.include.members-list', ['edit' => true])
            </div>
        @endif
    </div>
@endsection
