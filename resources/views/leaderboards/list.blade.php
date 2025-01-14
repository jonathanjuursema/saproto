@extends('website.layouts.redesign.dashboard')

@section('page-title')
    Leaderboards
@endsection

@section('container')
    <div class="row justify-content-center">
        <div id="leaderboard-accordion" class="col-10 col-md-6">
            @foreach ($leaderboards as $leaderboard)
                <div class="card mb-2">
                    <div
                        class="card-header"
                        data-bs-toggle="collapse"
                        data-bs-target="#collapse-leaderboard-{{ $leaderboard->id }}"
                    >
                        <i class="fa {{ $leaderboard->icon }}"></i>
                        {{ $leaderboard->name }} Leaderboard
                    </div>

                    <div
                        id="collapse-leaderboard-{{ $leaderboard->id }}"
                        class="table-responsive collapse {{ $loop->index == 0 ? 'show' : '' }}"
                        data-parent="#leaderboard-accordion"
                    >
                        @if ($leaderboard->description)
                            <div class="px-3 pt-3">
                                {!! Markdown::convert($leaderboard->description) !!}
                            </div>
                        @endif

                        @if (count($leaderboard->entries) > 0)
                            <table class="table table-sm">
                                <thead>
                                    <tr>
                                        <th></th>
                                        <th>Name</th>
                                        <th>
                                            {{ $leaderboard->points_name }}
                                        </th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($leaderboard->entries->sortByDesc('points') as $entry)
                                        <tr>
                                            <td
                                                class="ps-3 place-{{ $loop->index + 1 }}"
                                                style="max-width: 50px"
                                            >
                                                <i
                                                    class="fas fa-sm fa-fw {{ $loop->index == 0 ? 'fa-crown' : 'fa-hashtag' }}"
                                                ></i>
                                                {{ $loop->index + 1 }}
                                            </td>
                                            <td>{{ $entry->user->name }}</td>
                                            <td class="pe-4">
                                                <i
                                                    class="fa {{ $leaderboard->icon }}"
                                                ></i>
                                                {{ $entry->points }}
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        @else
                            <hr />
                            <p class="text-muted text-center pt-3">
                                There are no entries yet.
                            </p>
                        @endif
                    </div>
                </div>
            @endforeach
        </div>
    </div>
@endsection
