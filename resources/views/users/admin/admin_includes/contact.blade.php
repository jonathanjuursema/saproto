<div class="card mb-3">
    <div class="card-header bg-dark text-white">
        Contact details for {{ $user->calling_name }}
    </div>

    <div class="card-body">
        <p class="card-text">
            <i class="fas fa-at fa-fw me-2"></i>
            <a href="mailto:{{ $user->email }}">{{ $user->email }}</a>
            @if ($user->id !== Auth::user()->id && Auth::user()->can('board'))
                <a
                    href="#"
                    class="ms-1"
                    data-bs-toggle="modal"
                    data-bs-target="#changeEmail"
                >
                    <i class="fas fa-edit me-4"></i>
                </a>
            @endif

            <br />
            @if ($user->phone)
                <i class="fas fa-phone fa-fw me-2"></i>
                <a href="tel:{{ $user->phone }}">{{ $user->phone }}</a>
                <br />
            @endif

            @if ($user->address)
                <i class="fas fa-home fa-fw me-2"></i>
                {{ $user->address->street }} {{ $user->address->number }}
                <br />
                <i class="fas fa-fw me-2"></i>
                {{ $user->address->zipcode }} {{ $user->address->city }}
                ({{ $user->address->country }})
            @endif
        </p>
    </div>
</div>
