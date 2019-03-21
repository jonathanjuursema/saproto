@extends('website.layouts.redesign.dashboard')

@section('page-title')
    Announcements
@endsection

@section('container')

    <div class="row justify-content-center">

        <div class="col-md-8">

            <div class="card mb-3">

                <div class="card-header bg-dark text-white mb-1">
                    @yield('page-title')
                    <a href="{{ route('announcement::clear') }}" class="badge badge-info float-right">
                        Delete all past announcements.
                    </a>
                    <a href="{{ route('announcement::add') }}" class="badge badge-info float-right mr-2">
                        Add announcement.
                    </a>
                </div>

                @if (count($announcements) > 0)

                    <table class="table table-hover table-sm">

                        <thead>

                        <tr class="bg-dark text-white">

                            <td></td>
                            <td>Start</td>
                            <td>End</td>
                            <td>Visibility</td>
                            <td></td>

                        </tr>

                        </thead>

                        @foreach($announcements as $announcement)

                            <tr {!! (!$announcement->showByTime() ? 'style="opacity: 0.5;"': '') !!}>

                                <td>{{ $announcement->description }}</td>
                                <td>{{ $announcement->display_from }}</td>
                                <td>{{ $announcement->display_till }}</td>
                                <td>{{ $announcement->textualVisibility() }}</td>
                                <td>
                                    <a href="{{ route('announcement::edit', ['id' => $announcement->id]) }}">
                                        <i class="fas fa-edit mr-2"></i>
                                    </a>
                                    <a href="{{ route('announcement::delete', ['id' => $announcement->id]) }}">
                                        <i class="fas fa-trash"></i>
                                    </a>
                                </td>

                            </tr>

                        @endforeach

                    </table>

                @endif

            </div>

        </div>

    </div>

@endsection