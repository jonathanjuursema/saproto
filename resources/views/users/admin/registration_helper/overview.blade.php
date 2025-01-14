@extends('website.layouts.redesign.dashboard')

@section('page-title')
    User Registration
@endsection

@section('container')
    <div class="row justify-content-center">
        <div class="col-md-3">
            <form
                method="get"
                action="{{ route('user::registrationhelper::list') }}"
            >
                <div class="card mb-4">
                    <div class="card-header bg-dark text-white">
                        Search Pending Users
                    </div>
                    <div class="card-body">
                        <div class="input-group mb-2">
                            <input
                                type="text"
                                class="form-control"
                                placeholder="Search term"
                                name="query"
                                value="{{ $query }}"
                            />
                        </div>
                        <button type="submit" class="btn btn-info">
                            <i class="fas fa-sm fa-filter me-1"></i>
                            Filter
                        </button>
                    </div>
                </div>
            </form>
        </div>

        <div class="col-md-9">
            <div class="card mb-3">
                <div class="card-header bg-dark text-white mb-1">
                    @yield('page-title')
                </div>

                <div class="table-responsive">
                    <table class="table table-striped table-hover table-sm">
                        <thead>
                            <tr class="bg-dark text-white">
                                <td></td>
                                <td></td>
                                <td>Name</td>
                                <td>E-mail</td>
                                <td>Username</td>
                                <td>UTwente</td>
                            </tr>
                        </thead>

                        <tbody>
                            @foreach ($users as $user)
                                <tr
                                    class="{{ $user->deleted_at ? 'opacity-50' : '' }}"
                                >
                                    <td class="text-center">
                                        <a
                                            href="{{ route('user::registrationhelper::details', ['id' => $user->id]) }}"
                                        >
                                            <i class="fas fa-info-circle"></i>
                                        </a>
                                    </td>
                                    <td>#{{ $user->id }}</td>
                                    <td>{{ $user->name }}</td>
                                    <td>
                                        {{ $user->email }}
                                    </td>
                                    <td>
                                        @if ($user->is_member)
                                            {{ $user->member->proto_username }}
                                        @endif
                                    </td>
                                    <td>
                                        {{ $user->utwente_username }}
                                    </td>
                                    <td>
                                        {{ $user->utwente_department }}
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <div class="card-footer pb-0">
                    {!! $users->appends($_GET)->links() !!}
                </div>
            </div>
        </div>
    </div>
@endsection
