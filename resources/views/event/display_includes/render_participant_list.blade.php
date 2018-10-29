@foreach($participants as $user)

    <?php $pid = (get_class($user) == 'Proto\Models\User' ? $user->pivot->id : $user->id) ?>
    <?php $u = (get_class($user) == 'Proto\Models\User' ? $user : $user->user) ?>

    <div class="btn-group btn-group-sm mb-1">
        <a href="{{ route("user::profile", ['id' => $u->getPublicId()]) }}"
           class="btn btn-outline-primary">
            <img src="{{ $u->generatePhotoPath(25, 25) }}" class="rounded-circle mr-1"
            style="width: 21px; height: 21px; margin-top: -3px;">
            {{ $u->name }}
        </a>
        @if(Auth::user()->can('board') && !$event->activity->closed)
            <a href="{{ route('event::deleteparticipation', ['participation_id' => $pid]) }}"
               class="btn btn-outline-warning">
                <i class="fas fa-times" aria-hidden="true"></i>
            </a>
        @endif
    </div>

@endforeach