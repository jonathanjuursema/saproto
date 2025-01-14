<div class="card mb-3">
    <div class="card-header bg-dark text-white">Personal key</div>

    <div class="card-body">
        <p class="card-text">
            Your personal key is:
            <br />
            <sup><em>{{ $user->getPersonalKey() }}</em></sup>
        </p>

        <p class="card-text">
            If you don't know what you need this for, you don't need it.
            <i class="far fa-smile-wink"></i>
        </p>
    </div>

    <div class="card-footer">
        @include(
            'components.modals.confirm-modal',
            [
                'action' => route('user::personal_key::generate'),
                'classes' => 'btn btn-outline-danger btn-block',
                'text' => 'Mine has been compromised, generate me a new one',
                'title' => 'Confirm Regenerate',
                'message' =>
                    'Are you sure? This will invalidate any personal links including your personalized calendar.',
                'confirm' => 'Regenerate',
            ]
        )
    </div>
</div>
