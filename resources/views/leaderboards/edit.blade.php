@extends('website.layouts.redesign.dashboard')

@section('page-title')
    {{ $leaderboard == null ? 'Create new leaderboard.' : 'Edit leaderboard: ' . $leaderboard->name . '.' }}
@endsection

@section('container')
    <div class="row justify-content-center">
        <div class="col-md-5 mb-3">
            <form
                method="post"
                action="{{ $leaderboard == null ? route('leaderboards::store') : route('leaderboards::update', ['id' => $leaderboard->id]) }}"
                enctype="multipart/form-data"
            >
                @csrf

                <div class="card md-3">
                    <div class="card-header bg-dark text-white">
                        @yield('page-title')
                    </div>

                    <div class="card-body">
                        @can('board')
                            <div class="form-group autocomplete">
                                <label for="organisation">
                                    Committee:
                                    {{ $leaderboard->committee->name ?? '' }}
                                </label>
                                <input
                                    class="form-control committee-search"
                                    id="organisation"
                                    name="committee"
                                    value="value"
                                    ="{{ $leaderboard?->committee_id ? $leaderboard->committee_id : '' }}"
                                    required
                                />
                            </div>

                            @include(
                                'components.forms.checkbox',
                                [
                                    'name' => 'featured',
                                    'checked' => $leaderboard?->featured,
                                    'label' =>
                                        'Feature this leaderboard on the home page. <i class="fas fa-sm fa-star"></i>',
                                ]
                            )
                        @endcan

                        <div class="form-group">
                            <label for="name">Leaderboard name:</label>
                            <input
                                type="text"
                                class="form-control"
                                id="name"
                                name="name"
                                placeholder="Proto drink beer scores"
                                value="{{ $leaderboard->name ?? '' }}"
                                required
                            />
                        </div>

                        <div class="form-group">
                            <label for="points_name">Points name:</label>
                            <input
                                type="text"
                                class="form-control"
                                id="points_name"
                                name="points_name"
                                placeholder="Beers"
                                value="{{ $leaderboard->points_name ?? '' }}"
                                required
                            />
                        </div>

                        <input type="hidden" name="icon" id="icon" required />

                        @include(
                            'components.forms.iconpicker',
                            [
                                'name' => 'icon',
                                'placeholder' => isset($leaderboard) ? $leaderboard->icon : null,
                                'label' => 'Icon:',
                            ]
                        )

                        <div class="form-group">
                            <label for="editor">Description:</label>
                            @include(
                                'components.forms.markdownfield',
                                [
                                    'name' => 'description',
                                    'placeholder' => ! $leaderboard
                                        ? 'A small paragraph about the leaderboard.'
                                        : null,
                                    'value' => ! $leaderboard ? null : $leaderboard->description,
                                ]
                            )
                        </div>
                    </div>

                    <div class="card-footer">
                        <button
                            type="submit"
                            class="btn btn-success float-end ms-2"
                        >
                            Submit
                        </button>
                        @if ($leaderboard != null)
                            <a
                                class="btn btn-danger float-end"
                                href="{{ route('leaderboards::delete', ['id' => $leaderboard->id]) }}"
                            >
                                Delete
                            </a>
                        @endif
                    </div>
                </div>
            </form>
        </div>

        @if ($leaderboard)
            <div class="col-md-5 mb-3">
                <form
                    method="post"
                    action="{{ route('leaderboards::entries::create') }}"
                    enctype="multipart/form-data"
                >
                    @csrf

                    <input
                        type="hidden"
                        name="leaderboard_id"
                        value="{{ $leaderboard->id }}"
                    />

                    <div class="card md-3">
                        <div class="card-header bg-dark text-white">
                            {{ $leaderboard == null ? 'Add entries' : 'Edit entries for Leaderboard: ' . $leaderboard->name }}
                        </div>

                        <div class="card-body">
                            @if (count($entries) > 0)
                                <form action="">
                                    <div class="table-responsive">
                                        <table
                                            class="table table-sm table-hover mb-0"
                                        >
                                            <thead>
                                                <tr>
                                                    <th></th>
                                                    <th>Name</th>
                                                    <th>
                                                        {{ $leaderboard->points_name }}
                                                        <i
                                                            class="ms-1 {{ $leaderboard->icon }}"
                                                        ></i>
                                                    </th>
                                                    <th></th>
                                                    <th></th>
                                                </tr>
                                            </thead>

                                            <tbody>
                                                @foreach ($entries as $entry)
                                                    <tr
                                                        class="le-points"
                                                        data-id="{{ $entry->id }}"
                                                    >
                                                        <td>
                                                            #{{ $loop->index + 1 }}
                                                        </td>
                                                        <td>
                                                            {{ $entry->user->name }}
                                                        </td>
                                                        <td style="width: 80px">
                                                            <input
                                                                id="le_{{ $entry->id }}"
                                                                value="{{ $entry->points }}"
                                                                class="le-points-input"
                                                            />
                                                        </td>
                                                        <td
                                                            class="cursor-pointer"
                                                            style="
                                                                min-width: 60px;
                                                            "
                                                        >
                                                            <span
                                                                class="fa fas fa-lg fa-caret-up ms-2 le-points-increase"
                                                            ></span>
                                                            <span
                                                                class="fa fas fa-lg fa-caret-down ms-1 le-points-decrease"
                                                            ></span>
                                                        </td>
                                                        <td>
                                                            <a
                                                                href="{{ route('leaderboards::entries::delete', ['id' => $entry->id]) }}"
                                                            >
                                                                <i
                                                                    class="fas fa-trash text-danger fa-fw"
                                                                ></i>
                                                            </a>
                                                        </td>
                                                    </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </div>
                                </form>
                            @else
                                <p>No entries yet, add entries here.</p>
                            @endif
                        </div>

                        <div class="card-footer">
                            <div class="form-group mb-0">
                                <div class="row">
                                    <div class="col-9">
                                        <div class="form-group autocomplete">
                                            <input
                                                class="form-control user-search"
                                                name="user_id"
                                                required
                                            />
                                        </div>
                                    </div>
                                    <div class="col-3">
                                        <button
                                            class="btn btn-outline-primary btn-block"
                                            type="submit"
                                        >
                                            <i class="fas fa-plus-circle"></i>
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        @endif
    </div>
@endsection

@push('javascript')
    <script type="text/javascript" nonce="{{ csp_nonce() }}">
        Array.from(document.getElementsByClassName('le-points')).forEach(
            (el) => {
                ;['click', 'keyup'].forEach((e) =>
                    el.addEventListener(e, (e) => {
                        const id = el.getAttribute('data-id')
                        const input = el.querySelector('.le-points-input')
                        let points = input.value
                        if (e.target.classList.contains('le-points-increase'))
                            points++
                        else if (
                            e.target.classList.contains('le-points-decrease')
                        )
                            points--
                        input.value = points
                        updatePoints(id, points)
                    })
                )
            }
        )

        function updatePoints(id, points) {
            post('{{ route('leaderboards::entries::update') }}', {
                id: id,
                points: points,
            }).catch((err) => {
                console.error(err)
                window.alert(
                    'Something went wrong while updating the points. Please try again.'
                )
            })
        }
    </script>
@endpush
