@extends('website.layouts.redesign.dashboard')

@section('page-title')
    OmNomCom statistics
@endsection

@section('container')
    <div class="row justify-content-center mb-3">
        <div class="col-md-3">
            <form method="post">
                @csrf

                <div class="card">
                    <div class="card-header bg-dark text-white">
                        @yield('page-title')
                    </div>

                    <div class="card-body">
                        <p class="card-text">
                            @if (isset($select_text))
                                {!! $select_text !!}
                            @else
                                    Select a time range to generate statistics
                            @endif
                        </p>

                        <hr />

                        @include(
                            'components.forms.datetimepicker',
                            [
                                'name' => 'start',
                                'label' => 'Start date:',
                            ]
                        )

                        @include(
                            'components.forms.datetimepicker',
                            [
                                'name' => 'end',
                                'label' => 'End date:',
                            ]
                        )
                    </div>

                    <div class="card-footer">
                        <button type="submit" class="btn btn-success btn-block">
                            Submit
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
@endsection
