@foreach ($methods as $index => $method)
    <div
        class="mx-2 col-lg-3 col-5 my-2 h-md-75 rounded btn btn-outline-info"
        style="position: relative; height: 145px"
    >
        <input
            type="radio"
            name="method"
            class="custom-control-label"
            id="{{ $method->id }}"
            autocomplete="off"
            value="{{ $method->id }}"
            {{ count($methods) == 1 ? 'checked' : '' }}
        />
        <span>{{ $method->description }}</span>
        <svg
            class="mb-2 mb-md-3"
            style="
                position: absolute;
                bottom: 0;
                left: 0;
                right: 0;
                margin: auto;
            "
            width="90"
            height="68"
            data-html="true"
            data-bs-toggle="tooltip"
            data-bs-placement="bottom"
            title="
            @foreach ($method->pricing as $fee)
                <span>{{ $fee->description }} fee: €{{ $fee->fixed->value }} + {{ $fee->variable }}%</span>
                <br/>
            @endforeach
"
        >
            <image
                xlink:href="{{ $method->image->svg }}"
                src="{{ $method->image->size1x }}"
                width="90"
                height="68"
            />
        </svg>
    </div>
@endforeach
