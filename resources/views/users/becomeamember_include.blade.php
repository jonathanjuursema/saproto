<a
    {{ $unlocked && ! $done && $url ? "href=$url" : 'disabled' }}
    target="{{ ! empty($shouldOpenInNewTab) ? '_blank' : '_self' }}"
    class="list-group-item text-decoration-none {{ $unlocked ? ($done ? 'list-group-item-primary' : null) : 'list-group-item-dark' }}"
>
    <h5>
        <span class="me-2">
            <i class="fa-fw {{ $done ? 'fas fa-check' : $icon }}"></i>
        </span>
        {{ $heading }}
    </h5>

    @if ($unlocked)
        {{ $text }}
    @endif
</a>
