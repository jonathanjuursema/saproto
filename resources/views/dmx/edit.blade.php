@extends('website.layouts.redesign.dashboard')

@section('page-title')
    {{ $fixture == null ? 'Create new fixture.' : 'Edit fixture ' . $fixture->name . '.' }}
@endsection

@section('container')
    <form
        action="{{ ! empty($fixture) ? route('dmx.fixtures.update', ['fixture' => $fixture]) : route('dmx.fixtures.store') }}"
        method="POST"
    >
        <input
            type="hidden"
            name="_method"
            value="{{ ! empty($fixture) ? 'PUT' : 'POST' }}"
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
                            <label for="name">Fixture name:</label>
                            <input
                                type="text"
                                class="form-control"
                                id="name"
                                name="name"
                                placeholder="Ground Lights Section 1"
                                value="{{ $fixture->name ?? '' }}"
                                required
                            />
                        </div>

                        <div class="form-group">
                            <label for="channel_start">Fixture behavior:</label>
                            <select
                                class="form-control"
                                name="follow_timetable"
                            >
                                <option
                                    value="0"
                                    @selected($fixture && ! $fixture->follow_timetable)
                                >
                                    Manual
                                </option>
                                <option
                                    value="1"
                                    @selected($fixture?->follow_timetable)
                                >
                                    Automatic via timetable
                                </option>
                            </select>
                        </div>

                        <div class="form-group">
                            <label for="channel_start">First channel:</label>
                            <input
                                type="number"
                                class="form-control"
                                id="channel_start"
                                name="channel_start"
                                value="{{ $fixture->channel_start ?? '' }}"
                                required
                            />
                        </div>

                        <div class="form-group">
                            <label for="channel_end">Last channel:</label>
                            <input
                                type="number"
                                class="form-control"
                                id="channel_end"
                                name="channel_end"
                                value="{{ $fixture->channel_end ?? '' }}"
                                required
                            />
                        </div>

                        @if ($fixture != null)
                            <hr />

                            @foreach ($fixture->getChannels() as $channel)
                                <div class="form-group">
                                    <div class="row">
                                        <div class="col-6">
                                            <label>
                                                Name for channel
                                                {{ $channel->id }}:
                                            </label>
                                            <input
                                                type="text"
                                                class="form-control"
                                                name="channel_name[{{ $channel->id }}]"
                                                value="{{ $channel->name }}"
                                                required
                                            />
                                        </div>
                                        <div class="col-6">
                                            <div class="form-group">
                                                <label for="channel_start">
                                                    Channel type:
                                                </label>
                                                <select
                                                    class="form-control"
                                                    name="special_function[{{ $channel->id }}]"
                                                >
                                                    <option value="none">
                                                        None
                                                    </option>
                                                    <option
                                                        value="red"
                                                        @selected($channel->special_function == 'red')
                                                    >
                                                        Red
                                                    </option>
                                                    <option
                                                        value="green"
                                                        @selected($channel->special_function == 'green')
                                                    >
                                                        Green
                                                    </option>
                                                    <option
                                                        value="blue"
                                                        @selected($channel->special_function == 'blue')
                                                    >
                                                        Blue
                                                    </option>
                                                    <option
                                                        value="brightness"
                                                        @selected($channel->special_function == 'brightness')
                                                    >
                                                        Brightness
                                                    </option>
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        @endif
                    </div>

                    <div class="card-footer">
                        <button type="submit" class="btn btn-success float-end">
                            Submit
                        </button>

                        <a
                            href="{{ route('dmx.index') }}"
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
