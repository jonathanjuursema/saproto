@extends('website.layouts.redesign.generic')

@section('page-title')
    Complete membership profile
@endsection

@section('container')

    <div class="row justify-content-center">

        <div class="col-md-4">

            <form method="POST" action="{{ route('user::memberprofile::complete') }}">

                @csrf

                <div class="card mb-3">

                    <div class="card-header bg-dark text-white">@yield('page-title')</div>

                    <div class="card-body">

                        @include('users.registerwizard_macro')

                        <div class="row">
                            <div class="col-6">
                                <label>Birthdate</label>
                                @include('components.forms.datetimepicker', [
                                    'name' => 'birthdate',
                                    'format' => 'date',
                                    'placeholder' => strtotime('2000-01-01')
                                ])
                                <sup>Cannot be changed afterwards</sup>
                            </div>
                            <div class="col-6">
                                <label for="phone">Phone:</label>
                                <input type="tel" class="form-control" id="phone" name="phone"
                                       placeholder="+31534894423" required>
                                <sup>Can only be updated, not removed</sup>
                            </div>
                        </div>

                    </div>

                    <div class="card-footer">
                        <button type="submit" class="btn btn-outline-primary float-end btn-block">Complete profile
                        </button>
                    </div>

                </div>

            </form>

        </div>

    </div>

@endsection
