@extends('website.layouts.redesign.dashboard')

@section('page-title')
    User Administration
@endsection

@section('container')
    <div id="user-admin" class="row justify-content-center">
        <div class="col-md-3">
            <form method="get" action="{{ route('user::admin::index') }}">
                <div class="card mb-4">
                    <div class="card-header bg-dark text-white">
                        Search in users
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
                        <b>Filter users</b>
                        <div class="input-group mb-2">
                            <div class="form-check me-2">
                                <input
                                    class="form-check-input"
                                    type="radio"
                                    name="filter"
                                    id="users"
                                    value="users"
                                    @if($filter == 'users') checked @endif
                                />
                                <label class="form-check-label" for="users">
                                    Users
                                </label>
                            </div>
                            <div class="form-check me-2">
                                <input
                                    class="form-check-input"
                                    type="radio"
                                    name="filter"
                                    id="members"
                                    value="members"
                                    @if($filter == 'members') checked @endif
                                />
                                <label class="form-check-label" for="members">
                                    Members
                                </label>
                            </div>
                            <div class="form-check me-2">
                                <input
                                    class="form-check-input"
                                    type="radio"
                                    name="filter"
                                    id="pending"
                                    value="pending"
                                    @if($filter == 'pending') checked @endif
                                />
                                <label class="form-check-label" for="pending">
                                    Pending
                                </label>
                            </div>
                            <div class="form-check">
                                <input
                                    class="form-check-input"
                                    type="radio"
                                    id="all"
                                    name="filter"
                                    value=""
                                    @if($filter == null) checked @endif
                                />
                                <label class="form-check-label" for="all">
                                    All
                                </label>
                            </div>
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
                                <td class="ps-3">Controls</td>
                                <td></td>
                                <td>Calling Name</td>
                                <td>Full Name</td>
                                <td>Type</td>
                                <td>E-mail</td>
                                <td>Username</td>
                                <td>UTwente</td>
                                <td></td>
                            </tr>
                        </thead>

                        <tbody>
                            @php
                                /** @var \App\Models\User[] $users */
                            @endphp

                            @foreach ($users as $user)
                                <tr
                                    class="{{ $user->deleted_at ? 'opacity-50' : '' }}"
                                >
                                    <td class="controls" class="ps-3">
                                        @if (! $user->deleted_at)
                                            <a
                                                href="{{ route('user::admin::details', ['id' => $user->id]) }}"
                                                data-bs-toggle="tooltip"
                                                data-bs-placement="top"
                                                title="Go to user admin"
                                                class="text-decoration-none"
                                            >
                                                <i
                                                    class="fas fa-info-circle fa-fw me-1 text-info"
                                                    aria-hidden="true"
                                                ></i>
                                            </a>

                                            @if ($user->is_member)
                                                <a
                                                    href="{{ route('user::profile', ['id' => $user->getPublicId()]) }}"
                                                    data-bs-toggle="tooltip"
                                                    data-bs-placement="top"
                                                    title="Go to public profile"
                                                    class="text-decoration-none"
                                                >
                                                    <i
                                                        class="fas fa-user-circle fa-fw me-1 text-primary"
                                                        aria-hidden="true"
                                                    ></i>
                                                </a>
                                            @else
                                                <i
                                                    class="fas fa-user-circle fa-fw me-1 text-muted"
                                                    aria-hidden="true"
                                                ></i>
                                            @endif
                                            <a
                                                href="{{ route('user::member::impersonate', ['id' => $user->id]) }}"
                                                data-bs-toggle="tooltip"
                                                data-bs-placement="top"
                                                title="Impersonate"
                                                class="text-decoration-none"
                                            >
                                                <i
                                                    class="fas fa-sign-in-alt fa-fw me-1 text-warning"
                                                    aria-hidden="true"
                                                ></i>
                                            </a>

                                            @if ($user->isTempadmin())
                                                <a
                                                    href="{{ route('tempadmin::end', ['id' => $user->id]) }}"
                                                    data-bs-toggle="tooltip"
                                                    data-bs-placement="top"
                                                    title="Revoke temp admin"
                                                    class="text-decoration-none"
                                                >
                                                    <i
                                                        class="fas fa-user-lock fa-fw text-dark"
                                                        aria-hidden="true"
                                                    ></i>
                                                </a>
                                            @else
                                                <a
                                                    href="{{ route('tempadmins.create', ['id' => $user->id]) }}"
                                                    data-bs-toggle="tooltip"
                                                    data-bs-placement="top"
                                                    title="Grant temp admin till midnight"
                                                    class="text-decoration-none"
                                                >
                                                    <i
                                                        class="fas fa-user-clock fa-fw text-dark"
                                                        aria-hidden="true"
                                                    ></i>
                                                </a>
                                            @endif
                                        @endif
                                    </td>
                                    <td class="userid text-end">
                                        #{{ $user->id }}
                                    </td>
                                    <td>{{ $user->calling_name }}</td>
                                    <td class="fullname">{{ $user->name }}</td>
                                    <td class="membertype">
                                        @if ($user->deleted_at)
                                            Deleted
                                        @elseif ($user->member)
                                            @if ($user->member->membership_type === \App\Enums\MembershipTypeEnum::PENDING)
                                                <strong class="text-warning">
                                                    Pending
                                                </strong>
                                            @else
                                                <strong>Member</strong>
                                            @endif
                                        @else
                                                User
                                        @endif
                                    </td>
                                    <td class="proto-email">
                                        {{ $user->deleted_at ? '' : $user->email }}
                                    </td>
                                    <td class="proto-username">
                                        @if ($user->is_member)
                                            {{ $user->member->proto_username }}
                                        @endif
                                    </td>
                                    <td class="utwente-username">
                                        {{ $user->utwente_username }}
                                    </td>
                                    <td class="utwente-department">
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
