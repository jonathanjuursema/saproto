@extends('website.layouts.redesign.dashboard')

@section('page-title')
    DMX Overrides
@endsection

@section('container')

    <div class="row justify-content-center">

        <div class="col-md-8">

            <div class="card mb-3">

                <div class="card-header bg-dark text-white">
                    @yield('page-title')
                    <a href="{{ route('dmx.overrides.create') }}" class="badge bg-info float-end">
                        Create a new override.
                    </a>
                </div>

                @if (count($overrides) > 0)

                    <div class="table-responsive">
                        <table class="table table-hover">

                            <thead>

                            <tr>

                                <th>Fixtures</th>
                                <th colspan="4">Color</th>
                                <th>When</th>
                                <th></th>

                            </tr>

                            </thead>

                            <tr>
                                <th>
                                    <i>Active overrides</i>
                                </th>
                                <th colspan="6">
                                    <i>Overrides are applied from the bottom of this list to the top. The top most
                                        override is applied last.</i>
                                </th>
                            </tr>

                            @foreach($overrides as $override)
                                @include('dmx.override.override_macro', [
                                    'override' => $override
                                ])
                            @endforeach

                            <tr>
                                <th colspan="7">
                                    <i>Upcoming overrides</i>
                                </th>
                            </tr>

                            @foreach($upcoming_overrides as $override)
                                @include('dmx.override.override_macro', [
                                    'override' => $override
                                ])
                            @endforeach

                            <tr>
                                <th colspan="7">
                                    <i>Previous overrides</i>
                                </th>
                            </tr>

                            @foreach($past_overrides as $override)
                                @include('dmx.override.override_macro', [
                                    'override' => $override
                                ])
                            @endforeach

                        </table>
                    </div>

                @else

                    <div class="card-body">
                        <p class="card-text text-center">
                            There are no configured overrides.
                        </p>
                    </div>

                @endif

            </div>

        </div>

    </div>

@endsection
