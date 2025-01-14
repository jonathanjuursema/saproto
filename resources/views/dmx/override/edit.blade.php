@extends('website.layouts.redesign.dashboard')

@section('page-title')
    {{ $override == null ? 'Create new override.' : 'Edit override.' }}
@endsection

@section('container')
    <form
        action="{{ ! empty($override) ? route('dmx.overrides.update', ['override' => $override->id]) : route('dmx.overrides.store') }}"
        method="POST"
    >
        <input
            type="hidden"
            name="_method"
            value="{{ ! empty($override) ? 'PUT' : 'POST' }}"
        />
        {{ csrf_field() }}

        <div class="row justify-content-center">
            <div class="col-md-4">
                <div class="card mb-3">
                    <div class="card-header bg-dark text-white">
                        @yield('page-title')
                    </div>

                    <div class="card-body">
                        <div class="form-group">
                            <label for="name">Fixtures to override:</label>
                            <select
                                class="form-control"
                                name="fixtures[]"
                                multiple
                                required
                            >
                                @foreach ($fixtures as $fixture)
                                    <option
                                        value="{{ $fixture->id }}"
                                        @selected($override && in_array($fixture->id, $override->getFixtureIds()))
                                    >
                                        {{ $fixture->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="form-group">
                            <div class="row">
                                <div class="col-md-4">
                                    <label for="red">Red:</label>
                                    <input
                                        type="number"
                                        class="form-control"
                                        id="red"
                                        name="red"
                                        value="{{ $override ? $override->red() : '' }}"
                                        required
                                    />
                                </div>
                                <div class="col-md-4">
                                    <label for="green">Green:</label>
                                    <input
                                        type="number"
                                        class="form-control"
                                        id="green"
                                        name="green"
                                        value="{{ $override ? $override->green() : '' }}"
                                        required
                                    />
                                </div>
                                <div class="col-md-4">
                                    <label for="blue">Blue:</label>
                                    <input
                                        type="number"
                                        class="form-control"
                                        id="blue"
                                        name="blue"
                                        value="{{ $override ? $override->blue() : '' }}"
                                        required
                                    />
                                </div>
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="brightness">Brightness:</label>
                            <input
                                type="number"
                                class="form-control"
                                id="brightness"
                                name="brightness"
                                value="{{ $override ? $override->brightness() : '' }}"
                                required
                            />
                        </div>

                        <hr />

                        @include(
                            'components.forms.datetimepicker',
                            [
                                'name' => 'start',
                                'label' => 'Override start:',
                                'placeholder' => $override
                                    ? $override->start
                                    : strtotime(Carbon::now()),
                            ]
                        )

                        <div class="form-group">
                            <label for="end">Override end:</label>
                            @include(
                                'components.forms.datetimepicker',
                                [
                                    'name' => 'end',
                                    'label' => 'Override end:',
                                    'placeholder' => $override
                                        ? $override->end
                                        : strtotime(Carbon::now()->endOfDay()),
                                ]
                            )
                        </div>
                    </div>

                    <div class="card-footer">
                        <button type="submit" class="btn btn-success float-end">
                            Submit
                        </button>

                        <a
                            href="{{ route('dmx.overrides.index') }}"
                            class="btn btn-default"
                        >
                            Cancel
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </form>
@endsection
