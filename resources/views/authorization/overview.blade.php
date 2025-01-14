@extends('website.layouts.redesign.dashboard')

@section('page-title')
    Authorization Matrix
@endsection

@section('container')
    <div class="row justify-content-center">
        <div class="col-xl-6">
            <div class="card mb-3">
                <div class="card-header bg-dark text-white mb-1">
                    @yield('page-title')
                </div>

                <div class="card-body overflow-auto">
                    <table class="table table-bordered table-sm">
                        <thead>
                            <tr class="bg-dark text-white">
                                <th></th>
                                @foreach ($permissions as $permission)
                                    <th
                                        class="text-end align-middle py-2"
                                        style="writing-mode: vertical-lr"
                                    >
                                        {{ $permission->name }}
                                    </th>
                                @endforeach
                            </tr>
                        </thead>

                        <tbody>
                            @foreach ($roles as $role)
                                <tr>
                                    <th class="text-end px-2">
                                        {{ $role->name }}
                                    </th>
                                    @foreach ($permissions as $permission)
                                        <td class="text-center">
                                            <span
                                                class="{{ $role->hasPermissionTo($permission) ? '' : 'opacity-25' }}"
                                            >
                                                {{ $role->hasPermissionTo($permission) ? 'x' : 'o' }}
                                            </span>
                                        </td>
                                    @endforeach
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <div class="col-xl-3">
            <div class="card mb-3">
                <div class="card-header bg-dark text-white mb-1">
                    Manage role membership
                </div>

                <div class="card-body accordion" id="role-accordion">
                    @foreach ($roles as $i => $role)
                        <form
                            method="post"
                            action="{{ route('authorization::grant', ['id' => $role->id]) }}"
                        >
                            <div class="card mb-2">
                                <div
                                    class="card-header bg-info text-white cursor-pointer"
                                    data-bs-toggle="collapse"
                                    data-bs-target="#role-accordion-{{ $role->id }}"
                                >
                                    {{ $role->name }}
                                </div>

                                <div
                                    class="collapse"
                                    data-parent="#role-accordion"
                                    id="role-accordion-{{ $role->id }}"
                                >
                                    <div class="card-body">
                                        @foreach ($role->users as $user)
                                            @include(
                                                'users.includes.usercard',
                                                [
                                                    'user' => $user,
                                                    'subtitle' => sprintf(
                                                        '<div class="badge bg-%s text-white"><i class="fas fa-fw %s"></i> NDA</div>
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                        <a href="%s"><div class="badge bg-warning"><i class="fas fa-fw fa-undo"></i> Revoke</div></a>',
                                                        $user->signed_nda ? 'primary' : 'danger',
                                                        $user->signed_nda ? 'fa-user-shield' : 'fa-user-times',
                                                        route('authorization::revoke', [
                                                            'id' => $role->id,
                                                            'user' => $user->id,
                                                        ]),
                                                    ),
                                                ]
                                            )
                                        @endforeach
                                    </div>

                                    <div class="card-footer">
                                        <div class="form-group autocomplete">
                                            <input
                                                class="form-control user-search"
                                                name="user"
                                            />
                                        </div>

                                        <input
                                            type="submit"
                                            class="btn btn-success btn-block mt-3"
                                            value="Grant"
                                        />
                                    </div>
                                </div>
                            </div>

                            @csrf
                        </form>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
@endsection
