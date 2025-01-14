<div class="card mb-3 leftborder leftborder-info">
    <div class="card-body text-start">
        <a
            class="stretched-link text-decoration-none"
            href="{{ route('dinnerform::show', ['id' => $dinnerform->id]) }}"
        >
            @if ($dinnerform->hasExpired())
                <div class="btn btn-danger btn-block mb-3">
                    <i class="fas fa-lock fa-fw me-2" aria-hidden="true"></i>
                    <span>This dinnerform has ended</span>
                </div>
            @else
                <div class="btn btn-info btn-block mb-3">
                    <i
                        class="fas fa-circle-notch fa-fw fa-spin me-2"
                        aria-hidden="true"
                    ></i>
                    <span
                        class="proto-countdown"
                        data-countdown-start="{{ $dinnerform->end->timestamp }}"
                        data-countdown-text-counting="Closes in {}"
                        data-countdown-text-finished="Food is on its way!"
                    >
                        Counting down...
                    </span>
                </div>
            @endif
        </a>

        <span>
            <i class="fas fa-utensils fa-fw text-gold" aria-hidden="true"></i>
            <strong>{{ $dinnerform->restaurant }}</strong>
        </span>

        <br />

        <span>
            <i class="fas fa-clock fa-fw" aria-hidden="true"></i>
            {{ $dinnerform->generateTimespanText('D j M, H:i', 'H:i', '-') }}
        </span>

        <br />

        <span>
            <i class="fas fa-quote-left fa-fw" aria-hidden="true"></i>
            {{ $dinnerform->description }}
        </span>
    </div>
</div>
