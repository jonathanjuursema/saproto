@foreach ($participants as $user)
    <?php $pid =
        $user::class == \App\Models\User::class && $event
            ? $user->pivot->id
            : $user->id; ?>

    <?php $u = $user::class == \App\Models\User::class ? $user : $user->user; ?>

    <div class="btn-group btn-group-sm mb-1">
        <a
            href="{{ route('user::profile', ['id' => $u->getPublicId()]) }}"
            class="btn btn-outline-primary"
        >
            <img
                src="{{ $u->generatePhotoPath(25, 25) }}"
                class="rounded-circle me-1"
                style="width: 21px; height: 21px; margin-top: -3px"
            />
            {{ $u->name }}
        </a>
        @if (Auth::user()->can('board') && $event && ! $event->activity->closed)
            <a
                href="{{ route('event::deleteparticipation', ['participation_id' => $pid]) }}"
                class="btn btn-outline-warning"
            >
                <i class="fas fa-times" aria-hidden="true"></i>
            </a>
        @endif
    </div>
@endforeach
