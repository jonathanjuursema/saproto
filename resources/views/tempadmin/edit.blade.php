@extends('website.layouts.redesign.dashboard')

@section('page-title')
    @if ($new)
        New temporary admin
    @else
            Edit temporary admin
    @endif
@endsection

@section('container')
    <div class="row justify-content-center">
        <div class="col-md-4">
            <form
                action="{{ ! empty($item) ? route('tempadmins.update', ['tempadmin' => $item]) : route('tempadmins.store') }}"
                method="POST"
            >
                <input
                    type="hidden"
                    name="_method"
                    value="{{ ! empty($item) ? 'PUT' : 'POST' }}"
                />
                {{ csrf_field() }}

                <div class="card mb-3">
                    <div class="card-header bg-dark text-white">
                        @yield('page-title')
                    </div>

                    <div class="card-body">
                        @if ($new)
                            <div class="form-group autocomplete">
                                <label for="user-id">User:</label>
                                <input
                                    id="user-id"
                                    class="form-control user-search"
                                    name="user_id"
                                    required
                                />
                            </div>
                        @else
                            <div class="input-group">
                                <label for="user-id">User:&nbsp;</label>
                                <strong>{{ $item->user->name }}</strong>
                            </div>
                        @endif

                        @include(
                            'components.forms.datetimepicker',
                            [
                                'name' => 'start_at',
                                'label' => 'Start at:',
                                'placeholder' => $new
                                    ? strtotime(Carbon::now())
                                    : strtotime($item->start_at),
                            ]
                        )

                        @include(
                            'components.forms.datetimepicker',
                            [
                                'name' => 'end_at',
                                'label' => 'End at:',
                                'placeholder' => $new
                                    ? strtotime(Carbon::now()->endOfDay())
                                    : strtotime($item->end_at),
                            ]
                        )
                    </div>

                    <div class="card-footer">
                        <button type="submit" class="btn btn-success float-end">
                            Submit
                        </button>
                        <a
                            href="{{ route('tempadmins.index') }}"
                            class="btn btn-default"
                        >
                            Cancel
                        </a>
                    </div>
                </div>
            </form>
        </div>
    </div>
@endsection
