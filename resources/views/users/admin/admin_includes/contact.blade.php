<div class="card mb-3">

    <div class="card-header bg-dark text-white">
        Contact details for {{ $user->calling_name }}
    </div>

    <div class="card-body">

        <p class="card-text">
            <i class="fas fa-at fa-fw mr-2"></i> <a href="mailto:{{ $user->email }}">{{ $user->email }}</a><br>
            @if($user->phone)
                <i class="fas fa-phone fa-fw mr-2"></i> <a href="tel:{{ $user->phone }}">{{ $user->phone }}</a><br>
            @endif
            @if($user->address)
                <i class="fas fa-home fa-fw mr-2"></i>
                {{ $user->address->street }} {{ $user->address->number }}<br>
                <i class="fas fa-fw mr-2"></i>
                {{ $user->address->zipcode }} {{ $user->address->city }} ({{ $user->address->country }})
            @endif
        </p>

    </div>

</div>