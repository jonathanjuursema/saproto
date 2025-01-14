@extends('website.layouts.redesign.dashboard')

@section('page-title')
    Activity Administration
@endsection

@section('container')
    @if (count($activities) > 0)
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card mb-3">
                    <div class="card-header bg-dark text-white mb-1">
                        All unclosed activities.
                    </div>

                    <table class="table table-hover">
                        <thead>
                            <tr class="bg-dark text-white">
                                <td>Event</td>
                                <td>Fee</td>
                                <td>Sign ups</td>
                                <td>
                                    Attendees
                                    <i
                                        class="fas fa-info-circle"
                                        data-bs-toggle="tooltip"
                                        data-bs-placement="right"
                                        title="The estimated amount of people that actually showed up, use -1 if not applicable"
                                    ></i>
                                </td>
                                <td>Account</td>
                                <td></td>
                            </tr>
                        </thead>

                        @foreach ($activities as $activity)
                            <form
                                method="post"
                                action="{{ route('event::financial::close', ['id' => $activity->id]) }}"
                            >
                                <tr>
                                    {{ csrf_field() }}

                                    <td>
                                        @if ($activity->event)
                                            <a
                                                href="{{ route('event::show', ['id' => $activity->event->getPublicId()]) }}"
                                            >
                                                {{ $activity->event->title }}
                                            </a>
                                            <br />
                                            {{ date('D j F Y', $activity->event->start) }}
                                        @endif
                                    </td>

                                    <td>
                                        &euro;{{ number_format($activity->price, 2) }}
                                    </td>

                                    <td>{{ $activity->users->count() }}</td>

                                    <td>
                                        <input
                                            class="form-control"
                                            id="attendees"
                                            name="attendees"
                                            min="-1"
                                            type="number"
                                            required
                                        />
                                    </td>

                                    <td>
                                        <select
                                            name="account"
                                            class="form-control"
                                        >
                                            @foreach (\App\Models\Account::orderBy('account_number', 'asc')->get() as $account)
                                                <option
                                                    value="{{ $account->id }}"
                                                >
                                                    ({{ $account->account_number }}
                                                    ) {{ $account->name }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </td>

                                    <td>
                                        @if ($activity->event && ! $activity->event->over())
                                            <button
                                                class="btn btn-info btn-block"
                                                disabled
                                            >
                                                Not ended yet.
                                            </button>
                                        @else
                                            <button
                                                type="submit"
                                                class="btn btn-warning btn-block"
                                            >
                                                Close
                                            </button>
                                        @endif
                                    </td>
                                </tr>
                            </form>
                        @endforeach
                    </table>
                </div>
            </div>
        </div>
    @else
        <p class="text-center mt-3">
            There are no activities to close! Hurray!
        </p>
    @endif
@endsection
