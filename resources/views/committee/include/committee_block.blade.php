@php
    /** @var App\Models\Committee $committee */
    $name = isset($override_committee_name) && $override_committee_name ? $override_committee_name : $committee->name;
@endphp

@include(
    'website.home.cards.card-bg-image',
    [
        'url' =>
            $committee->public || (Auth::check() && Auth::user()->can('board'))
                ? route(
                    $committee->is_society
                        ? 'society::show'
                        : 'committee::show',
                    ['id' => $committee->getPublicId()],
                )
                : '#',
        'img' => $committee->image
            ? $committee->image->generateImagePath(450, 300)
            : null,
        'html' => ! $committee->public
            ? sprintf(
                '<i class="fas fa-lock" %s></i>&nbsp;&nbsp;%s',
                'data-bs-toggle="tooltip" data-bs-placement="top" title="This committee is hidden. You cannot see its details."',
                $name,
            )
            : sprintf('<strong>%s</strong>', $name),
        'height' => $height ?? 120,
        'classes' => ! $committee->public ? ['opacity-50'] : null,
        'photo_pop' => $photo_pop ?? true,
        'footer' => $footer ?? null,
    ]
)
