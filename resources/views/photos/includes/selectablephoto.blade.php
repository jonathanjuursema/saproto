<div class="col-lg-2 col-lg-3 col-md-4 col-sm-6">
    <div class="photo-select">
        <input
            name="photos[]"
            value="{{ $photo->id }}"
            type="checkbox"
            class="align-self-start"
            id="photo_{{ $photo->id }}"
        />
        <div class="card mb-3">
            <label
                for="photo_{{ $photo->id }}"
                class="card-img photo_pop"
                style="
                    display: flex;
                    height: 200px;
                    background-image: url({{ $photo->thumbnail() }});
                "
            >
                @if ($photo->private)
                    <p class="card-text ellipsis align-self-end">
                        <i
                            class="fas fa-eye-slash ms-2 mb-1 text-info"
                            data-bs-toggle="tooltip"
                            data-bs-placement="bottom"
                            title="This photo is only visible to members."
                        ></i>
                    </p>
                @endif
            </label>
        </div>
    </div>
</div>
