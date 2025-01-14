@if (count($user->roles) > 0)
    <div class="card mb-3">
        <div class="card-header bg-dark text-white">Roles and permissions</div>
        <div class="card-body">
            <p class="card-text">
                You have the following roles and permissions on this site:

                @foreach ($user->roles as $role)
                    <p class="card-text">
                        <i class="fas fa-user-friends fa-fw me-2"></i>
                        <strong>{{ $role->display_name }}</strong>
                        <br />
                        <small>{{ $role->description }}</small>
                        <br />
                        @foreach ($role->permissions as $permission)
                            <span class="badge rounded-pill bg-info">
                                <i class="fas fa-plus fa-fw me-1"></i>
                                {{ $permission->description }}
                            </span>
                            <br />
                        @endforeach
                    </p>
                @endforeach
            </p>
        </div>
    </div>
@endif
