<div class="card mb-3">
    <div class="card-header bg-dark text-white">Awards</div>

    <div class="card-body">
        @if ($achievement->currentOwners(false)->count())
            @foreach ($achievement->currentOwners(false)->get() as $user)
                <div class="badge bg-primary">
                    <a
                        href="{{ route('user::profile', ['id' => $user->getPublicId()]) }}"
                        class="text-white"
                    >
                        {{ $user->name }}
                    </a>
                    <a
                        href="{{ route('achievement::take', ['id' => $achievement->id, 'user' => $user->id]) }}"
                        class="text-white"
                    >
                        <i class="fas fa-times ms-2"></i>
                    </a>
                </div>
            @endforeach
        @else
            <p class="card-text text-center">
                Nobody obtained this achievement yet
            </p>
        @endif
    </div>

    <div class="card-footer">
        <form
            method="post"
            action="{{ route('achievement::award', ['id' => $achievement->id]) }}"
        >
            @csrf

            <div class="form-group autocomplete">
                <label for="user-id">User</label>
                <input class="form-control user-search" name="user-id" />
            </div>

            @include(
                'components.forms.datetimepicker',
                [
                    'name' => 'achieved_on',
                    'label' => 'Achieved on',
                    'placeholder' => Carbon::now()->timestamp,
                    'format' => 'date',
                ]
            )

            <div class="form-group">
                <label for="description" class="text-secondary">
                    (optional) description:
                </label>
                <input
                    class="form-control"
                    id="description"
                    name="description"
                    data-label="description"
                />
            </div>

            <button type="submit" class="mt-3 btn btn-success btn-block">
                Award
            </button>

            <hr />

            @include(
                'components.modals.confirm-modal',
                [
                    'action' => route('achievement::takeAll', ['id' => $achievement->id]),
                    'classes' => 'btn-outline-danger btn-block',
                    'text' => 'Take from everyone',
                    'message' => "Are you sure you want to take $achievement->name from everyone?",
                ]
            )
        </form>
    </div>
</div>
