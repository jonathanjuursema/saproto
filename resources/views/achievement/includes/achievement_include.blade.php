<div class="card achievement-{{ strtolower($achievement->tier) }} mb-3">
    <div class="card-header">
        <i class="{{ $achievement->fa_icon }} fa-fw me-2 text-white"></i>

        <strong class="text-white">{{ $achievement->name }}</strong>
        @if (isset($obtained) && $obtained)
            <i
                class="fas fa-check text-primary fa-fw"
                aria-hidden="true"
                data-bs-toggle="tooltip"
                data-bs-placement="top"
                title="You have achieved this!"
            ></i>
        @endif

        @if (! empty($include_delete_for))
            <a
                href="{{ route('achievement::take', ['id' => $achievement->id, 'user' => $user->id]) }}"
                class="float-end text-white"
            >
                <i class="fas fa-trash fa-fw ms-3"></i>
            </a>
        @endif

        <span class="float-end">
            @for ($i = 0; $i < 5; $i++)
                @if ($i < $achievement->numberOfStars())
                    <i class="text-white fas fa-star"></i>
                @else
                    <i
                        class="achievement-{{ $achievement->tier }} fas fa-star"
                    ></i>
                @endif
            @endfor
        </span>
    </div>

    <div class="card-body text-dark">
        {{ $achievement->desc }}

        @if ($obtained?->description)
            <br />
            <div class="text-secondary fst-italic">
                "{{ $obtained->description }}"
            </div>
        @endif
    </div>

    @if (! empty($footer))
        <div class="card-footer">
            {!! $footer !!}
        </div>
    @endif
</div>
