@extends('website.layouts.redesign.generic')

@section('page-title')
    Participant checklist for {{ $event->title }}
    <a
        href="{{ route('event::show', ['id' => $event->getPublicId()]) }}"
        class="btn btn-default float-end"
    >
        Back to event
    </a>
@endsection

@section('container')
    <div class="row justify-content-center">
        <div class="col-md-8 col-sm-10 col-xs-12">
            <div class="card">
                <div class="card-header bg-dark text-white">
                    @yield('page-title')
                </div>

                <div class="card-body">
                    <table class="table table-responsive">
                        <thead>
                            <tr>
                                <th>Present</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td>
                                    <span id="present">
                                        {{ $event->activity->getPresent() }}
                                    </span>
                                    /{{ $event->allUsers()->count() }}
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <div class="card-body">
                    <table class="table table-responsive">
                        <thead>
                            <tr>
                                <th>Check</th>
                                <th>Type</th>
                                <th>Name</th>
                                <th>Age</th>
                                @if ($event->shouldShowDietInfo())
                                    <th width="20%">Allergies</th>
                                @endif
                            </tr>
                        </thead>

                        <tbody>
                            @foreach ($event->allUsers() as $user)
                                @php
                                    $participation = $user->pivot;
                                @endphp

                                <tr>
                                    <td>
                                        @if ($participation)
                                            <span
                                                class="cursor-pointer is_present badge bg-{{ $participation->is_present ? 'success' : 'danger' }}"
                                                data-id="{{ $participation->id }}"
                                            >
                                                {{ $participation->is_present ? 'Present' : 'Absent' }}
                                            </span>
                                        @endif
                                    </td>

                                    <td>
                                        @if ($participation)
                                            @if ($participation->committees_activities_id !== null)
                                                <span class="badge bg-success">
                                                    Helper
                                                </span>
                                            @else
                                                    Participant
                                            @endif
                                        @else
                                                Ticket
                                        @endif
                                    </td>

                                    <td><strong>{{ $user->name }}</strong></td>

                                    <td>
                                        @if ($user->age() >= 18)
                                            <span class="badge bg-success">
                                                <i
                                                    class="fas fa-check"
                                                    aria-hidden="true"
                                                ></i>
                                                18+
                                            </span>
                                        @else
                                            <span class="badge bg-danger">
                                                <i
                                                    class="fas fa-exclamation-triangle"
                                                    aria-hidden="true"
                                                ></i>
                                                18-
                                            </span>
                                        @endif
                                    </td>

                                    @if ($event->shouldShowDietInfo())
                                        <td>
                                            @if ($user->hasDiet())
                                                {!! Markdown::convert($user->diet) !!}
                                            @endif
                                        </td>
                                    @endif
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('javascript')
    <script type="text/javascript" nonce="{{ csp_nonce() }}">
        present = document.getElementById('present')

        document.querySelectorAll('.is_present').forEach((el) => {
            el.onclick = (_) => {
                get(
                    '{{ route('event::togglepresence', ['id' => 'id']) }}'.replace(
                        'id',
                        el.getAttribute('data-id')
                    )
                ).then((data) => {
                    el.classList.toggle('bg-success')
                    el.classList.toggle('bg-danger')
                    el.innerHTML =
                        el.innerHTML === 'Present' ? 'Absent' : 'Present'
                    present.innerHTML = data
                })
            }
        })
    </script>
@endpush
