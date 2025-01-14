<div
    id="{{ $id ?? null }}"
    class="card mb-3 text-decoration-none {{ isset($classes) ? implode(' ', $classes) : null }} {{ isset($leftborder) ? sprintf('leftborder leftborder-%s', $leftborder) : null }}"
>
    <a
        href="{{ $url ?? '#' }}"
        class="card-body d-flex justify-content-start text-decoration-none {{ isset($photo_pop) && $photo_pop ? 'photo_pop' : 'photo' }}"
        style="
            background: center no-repeat
                {{ isset($img) ? "url($img)" : '#333' }};
            background-size: cover;
            height: {{ $height ?? 100 }}px;
        "
    >
        <p class="card-text ellipsis">
            {!! $html !!}
        </p>
    </a>

    @if (isset($footer) && $footer != null)
        <div class="card-footer ellipsis">
            {!! $footer !!}
        </div>
    @endif
</div>
