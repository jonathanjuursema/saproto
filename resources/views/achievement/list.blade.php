@extends('website.layouts.redesign.dashboard')

@section('page-title')
    Achievement Administration
@endsection

@section('container')
    <div class="row justify-content-end">
        <div class="col">

            <div class="card mb-3">

                <div class="card-header bg-dark text-white mb-1">
                    @yield('page-title')
                    <a href="{{ route('achievement::create') }}" class="badge bg-info float-end">
                        Create a new achievement.
                    </a>
                </div>

                <table class="table table-hover table-sm">

                    <thead>

                    <tr class="bg-dark text-white">

                        <td></td>
                        <td>Title</td>
                        <td>Awarded</td>
                        <td>Description</td>
                        <td>Tier</td>
                        <td class="text-dark">Controls</td>

                    </tr>

                    </thead>

                    @foreach($achievements as $achievement)

                        <tr class="{{ $achievement->is_archived ? "text-muted" : null }}">

                            <td class="hidden-sm hidden-xs {{ $achievement->tier }}">
                                @if($achievement->fa_icon)
                                    <div><i class="{{ $achievement->fa_icon }} fa-fw"></i></div>
                                @else
                                    No icon available
                                @endif
                            </td>
                            <td>{{ $achievement->name }}</td>
                            <td>{{ $achievement->currentOwners(true)->count() }}</td>
                            <td>{{ $achievement->desc }}</td>
                            <td>{{ $achievement->tier }}</td>
                            <td class="text-end">
                                <a href="{{ route('achievement::edit', ['id' => $achievement->id]) }}">
                                    <i class="fas fa-edit me-2"></i>
                                </a>
                                <a href="{{ route('achievement::delete', ['id' => $achievement->id]) }}">
                                    <i class="fas fa-trash text-danger"></i>
                                </a>
                            </td>

                        </tr>

                    @endforeach

                </table>

                <div class="card-footer pb-0">
                    {!! $achievements->links() !!}
                </div>

            </div>

        </div>

        <div class="col-md-auto">
            @include('achievement.admin_includes.awards-addone')
        </div>
    </div>
@endsection

