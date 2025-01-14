<form
    method="post"
    action="{{ ! $achievement ? route('achievement::store') : route('achievement::update', ['id' => $achievement->id]) }}"
>
    @csrf

    <div class="card mb-3">
        <div class="card-header bg-dark text-white">
            @yield('page-title')
            @if ($achievement)
                <span class="badge bg-info float-end">
                    Obtained by
                    {{ $achievement->currentOwners(true)->count() }} members
                </span>
            @endif
        </div>

        <div class="card-body">
            <div class="form-group">
                <label for="name">Name:</label>
                <input
                    type="text"
                    class="form-control"
                    id="name"
                    name="name"
                    placeholder="Be Awesome"
                    value="{{ $achievement->name ?? '' }}"
                    required
                />
            </div>

            <div class="form-group">
                <label for="desc">Description:</label>
                <input
                    type="text"
                    class="form-control"
                    id="desc"
                    name="desc"
                    placeholder="Become member of Proto"
                    value="{{ $achievement->desc ?? '' }}"
                    required
                />
            </div>

            <div class="form-group">
                <label for="tier">Tier:</label>
                <select
                    class="form-control {{ $achievement->tier ?? '' }}"
                    name="tier"
                >
                    <option
                        value="COMMON"
                        @selected($achievement?->tier == 'COMMON')
                    >
                        Common
                    </option>
                    <option
                        value="UNCOMMON"
                        @selected($achievement?->tier == 'UNCOMMON')
                    >
                        Uncommon
                    </option>
                    <option
                        value="RARE"
                        @selected($achievement?->tier == 'RARE')
                    >
                        Rare
                    </option>
                    <option
                        value="EPIC"
                        @selected($achievement?->tier == 'EPIC')
                    >
                        Epic
                    </option>
                    <option
                        value="LEGENDARY"
                        @selected($achievement?->tier == 'LEGENDARY')
                    >
                        Legendary
                    </option>
                </select>
            </div>

            @include(
                'components.forms.checkbox',
                [
                    'name' => 'is_archived',
                    'checked' => $achievement?->is_archived,
                    'label' => 'This achievement is archived',
                ]
            )

            @include(
                'components.forms.checkbox',
                [
                    'name' => 'has_page',
                    'checked' => $achievement?->has_page,
                    'label' => 'Can be achieved by visiting url',
                ]
            )

            <div
                id="achieve_page_block"
                class="d-none"
                @if(!$achievement || !$achievement->has_page) @endif
            >
                <div class="form-group">
                    <label for="page_name">Achieve URL</label>
                    <div class="input-group">
                        <div class="input-group-prepend">
                            <span class="input-group-text">
                                saproto.nl/achieve/
                            </span>
                        </div>
                        <input
                            type="text"
                            class="form-control"
                            id="page_name"
                            name="page_name"
                            value="{{ $achievement ? $achievement->page_name ?? str_replace(' ', '-', trim(strtolower($achievement->name))) : null }}"
                        />
                    </div>
                </div>

                <div class="form-group">
                    <label for="content">Content</label>
                    @include(
                        'components.forms.markdownfield',
                        [
                            'name' => 'page_content',
                            'placeholder' => 'Achievement page message.',
                            'value' => $achievement->page_content ?? null,
                        ]
                    )
                </div>
            </div>
        </div>

        <div class="card-footer">
            <button type="submit" class="btn btn-success float-end">
                Submit
            </button>

            <a
                href="{{ route('achievement::index') }}"
                class="btn btn-default"
            >
                Cancel
            </a>
        </div>
    </div>
</form>

@push('javascript')
    <script type="text/javascript" nonce="{{ csp_nonce() }}">
        let pageBlock = document.getElementById('achieve_page_block')
        document.getElementById('has_page').addEventListener('click', (e) => {
            if (e.target.checked) {
                pageBlock.classList.remove('d-none')
                pageBlock.querySelector('input').required = true
            } else {
                pageBlock.classList.add('d-none')
                pageBlock.querySelector('input').required = false
            }
        })
    </script>
@endpush
