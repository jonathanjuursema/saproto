<div class="form-group">
    @isset($label)
        <label for="iconpicker-{{ $name }}">{{ $label }}</label>
    @endisset

    <div class="input-group mb-3 iconpicker-wrapper">
        <div class="input-group-prepend">
            <span class="input-group-text h-100 selected-icon"></span>
        </div>
        <input
            id="iconpicker-{{ $name }}"
            type="text"
            name="{{ $name }}"
            class="form-control iconpicker rounded-2"
            value="{{ $placeholder ?? '' }}"
        />
    </div>
</div>
