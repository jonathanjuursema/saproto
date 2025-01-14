<div class="card mb-3">
    <div class="card-header bg-dark text-white">
        Discord
        <i class="far fa-brands fa-discord"></i>
    </div>

    <div class="card-body">
        @if (! $user->discord_id)
            <p class="card-text">
                <i class="fa-solid fa-xmark"></i>
                You currently do not have a Discord account linked.
                <br />
            </p>
        @else
            <p class="card-text">
                <i class="fa-solid fa-check"></i>
                You have linked a Discord account!
            </p>
        @endif
    </div>

    <div class="card-footer">
        @if (! $user->discord_id)
            <a
                href="{{ route('api::discord::redirect') }}"
                class="btn btn-outline-info btn-block"
            >
                <i class="fa-solid fa-link"></i>
                Link Discord
            </a>
        @else
            <a
                href="{{ route('api::discord::unlink') }}"
                class="btn btn-outline-info btn-block"
            >
                <i class="fa-solid fa-link-slash"></i>
                Unlink Discord
            </a>
        @endif
    </div>
</div>
