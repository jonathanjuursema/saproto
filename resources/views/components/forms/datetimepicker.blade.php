@php
    $strFormat = [
        'date' => 'Y-m-d',
        'time' => 'TH:i',
        'datetime-local' => 'Y-m-d\TH:i',
    ];
@endphp

<div
    id="datetimepicker-{{ $name }}-form"
    class="form-group {{ $form_class_name ?? '' }}"
>
    @isset($label)
        <label for="datetimepicker-{{ $name }}">
            {!! $label !!}
        </label>
    @endisset

    <input
        type="{{ $format ?? 'datetime-local' }}"
        id="datetimepicker-{{ $name }}"
        class="form-control datetimepicker {{ $input_class_name ?? '' }}"
        name="{{ $name }}"
        value="{{ isset($placeholder) ? date($strFormat[$format ?? 'datetime-local'], $placeholder) : '' }}"
        {{ isset($not_required) && $not_required ? '' : 'required' }}
    />
</div>
