<div class="card mb-3">
    <div class="card-header d-inline-flex justify-content-between">
        Songs
        @if (! isset($edit))
            <div>
                <a
                    href="{{ route('codexSong.create') }}"
                    class="btn btn-info badge"
                >
                    New Song
                </a>

                <a
                    href="{{ route('codexSongCategory.create') }}"
                    class="btn btn-info badge"
                >
                    New Song type
                </a>
            </div>
        @endif
    </div>
    <div class="card-body">
        @foreach ($songTypes as $songCategory)
            <div class="card border">
                <div class="card-header border-bottom-0">
                    <span class="w-100 d-inline-flex justify-content-between">
                        <span
                            class="cursor-pointer"
                            data-bs-toggle="collapse"
                            data-bs-target="#collapse-song{{ $songCategory->id }}"
                        >
                            <b>
                                <i class="fas fa-sm fa-fw fa-caret-down"></i>
                                {{ $songCategory->name }}
                                ({{ $songCategory->songs_count }})
                            </b>
                        </span>
                        @if (! isset($edit))
                            <div>
                                <a
                                    href="{{ route('codexSongCategory.edit', ['codexSongCategory' => $songCategory]) }}"
                                    class="btn btn-info badge m-1"
                                >
                                    Edit
                                </a>
                                @include(
                                    'components.modals.confirm-modal',
                                    [
                                        'method' => 'DELETE',
                                        'action' => route('codexSongCategory.destroy', [
                                            'codexSongCategory' => $songCategory,
                                        ]),
                                        'classes' => 'btn btn-danger badge',
                                        'text' => 'Delete',
                                        'message' => "Are you sure you want to delete the category $songCategory->name?<br> This will also delete <b>all songs</b> in this category!",
                                    ]
                                )
                            </div>
                        @endif
                    </span>

                    <div
                        id="collapse-song{{ $songCategory->id }}"
                        class="collapse"
                    >
                        <div class="card-body cursor-default">
                            @foreach ($songCategory->songs as $song)
                                <span
                                    class="w-100 d-inline-flex justify-content-between"
                                >
                                    @if (isset($edit) && $edit)
                                        <div class="form-check">
                                            @include(
                                                'components.forms.checkbox',
                                                [
                                                    'input_class_name' => '',
                                                    'name' => 'songids[]',
                                                    'checked' => in_array($song->id, $mySongs ?? []),
                                                    'value' => $song->id,
                                                    'label' => 'Include',
                                                ]
                                            )
                                        </div>
                                    @endif

                                    {{ $song->title }}
                                    @if (! isset($edit))
                                        <div>
                                            <a
                                                href="{{ route('codexSong.edit', ['codexSong' => $song]) }}"
                                                class="btn btn-info badge m-1"
                                            >
                                                Edit
                                            </a>
                                            @include(
                                                'components.modals.confirm-modal',
                                                [
                                                    'method' => 'DELETE',
                                                    'action' => route('codexSong.destroy', ['codexSong' => $song]),
                                                    'classes' => 'btn btn-danger badge m-1',
                                                    'text' => 'Delete',
                                                    'message' => "Are you sure you want to delete $song->title?",
                                                ]
                                            )
                                        </div>
                                    @endif
                                </span>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>
        @endforeach
    </div>
</div>
