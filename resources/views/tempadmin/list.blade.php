@extends('website.layouts.redesign.dashboard')

@section('page-title')
    Temporary Admin Admin
@endsection

@section('container')

    <div class="row justify-content-center">

        <div class="col-md-6">

            <div class="card mb-3">

                <div class="card-header bg-dark text-white mb-1">
                    @yield('page-title')
                    <a href="{{ route('tempadmins.create') }}" class="float-end bg-info badge">
                        Add new temporary admin.
                    </a>
                </div>

                <table class="table table-hover table-sm">

                    <thead>

                    <tr class="bg-dark text-white">

                        <td>User</td>
                        <td>Given by</td>
                        <td>From</td>
                        <td>Until</td>
                        <td></td>

                    </tr>

                    </thead>

                    @foreach($tempadmins as $tempadmin)

                        <tr>
                            <td>
                                <a href="{{ route("user::profile", ['id' => $tempadmin->user->getPublicId()]) }}">{{ $tempadmin->user->name }}</a>
                            </td>
                            <td>
                                <a href="{{ route("user::profile", ['id' => $tempadmin->creator->getPublicId()]) }}">{{ $tempadmin->creator->name }}</a>
                            </td>
                            <td class="{{ Carbon::parse($tempadmin->start_at)->isPast() ? 'opacity-50' : '' }}">{{ $tempadmin->start_at }}</td>
                            <td>{{ $tempadmin->end_at }}</td>
                            <td>
                                <a href="{{ route("tempadmins.edit", ['tempadmin' => $tempadmin]) }}">
                                    <i class="fas fa-edit fa-fw me-2"></i>
                                </a>

                                @include('components.modals.confirm-modal', [
                                   'action' => route('tempadmin::endId', ['id' => $tempadmin->id]),
                                   'text' => Carbon::parse($tempadmin->start_at)->isFuture() ?
                                               '<i class="fas fa-trash fa-fw text-danger"></i>' :
                                               '<i class="fas fa-hourglass-end text-danger fa-fw"></i>',
                                   'title' => 'Confirm End Rights',
                                   'message' => 'Are you sure you want to end the temporary ProTube admin rights of '.$tempadmin->user->name.'?',
                                   'confirm' => 'End Rights',
                                ])
                            </td>
                        </tr>

                    @endforeach

                    @foreach($pastTempadmins as $pastTempadmin)

                        <tr class="opacity-50">
                            <td>
                                <a href="{{ route("user::profile", ['id' => $pastTempadmin->user->getPublicId()]) }}">{{ $pastTempadmin->user->name }}</a>
                            </td>
                            <td>
                                <a href="{{ route("user::profile", ['id' => $pastTempadmin->creator->getPublicId()]) }}">{{ $pastTempadmin->creator->name }}</a>
                            </td>
                            <td>{{ $pastTempadmin->start_at }}</td>
                            <td>{{ $pastTempadmin->end_at }}</td>
                            <td></td>
                        </tr>

                    @endforeach

                </table>


            </div>

        </div>

    </div>

@endsection
