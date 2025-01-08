@extends('website.layouts.redesign.dashboard')

@section('page-title')
    @if($new)
        Create new page
    @else
        Edit page {{ $item->title }}
    @endif
@endsection

@section('container')

    <div class="row justify-content-center">

        <div class="col-md-6">

            <form method="post"
                  action="@if($new) {{ route("page::store") }} @else {{ route("page::update", ['id' => $item->id]) }} @endif"
                  enctype="multipart/form-data">

                <div class="card mb-3">

                    <div class="card-header bg-dark text-white">
                        @yield('page-title')
                    </div>

                    <div class="card-body">

                        @csrf

                        <div class="form-group">
                            <label for="title">Title:</label>
                            <input type="text" class="form-control" id="title" name="title"
                                   placeholder="About Proto" value="{{ $item->title ?? '' }}" required>
                        </div>

                        <div class="form-group">
                            <label for="slug">URL:</label>
                            <div class="input-group mb-3">
                                <div class="input-group-prepend">
                                    <span class="input-group-text">{{ route('page::show', '') }}/</span>
                                </div>
                                <input type="text" class="form-control" name="slug" placeholder="about-proto"
                                       value="{{ $item->slug ?? '' }}" required>
                            </div>
                        </div>

                        @include('components.forms.checkbox', [
                            'name' => 'is_member_only',
                            'checked' => $item?->is_member_only,
                            'label' => 'Member only'
                        ])

                        @include('components.forms.checkbox', [
                            'name' => 'show_attachments',
                            'checked' => $item?->show_attachments,
                            'label' => 'Show attachments next to page'
                        ])

                        <div class="form-group">
                            <label for="content">Content</label>
                            @include('components.forms.markdownfield', [
                                'name' => 'content',
                                'placeholder' => 'Text goes here.',
                                'value' => $item ? $item->content : null
                            ])
                        </div>

                    </div>

                    <div class="card-footer">

                        <button type="submit" class="btn btn-success float-end">
                            Submit
                        </button>

                        <a href="{{ route("page::list") }}" class="btn btn-default">Cancel</a>

                    </div>

                </div>

            </form>

        </div>

        @if(!$new)

            <div class="col-md-3">

                <form method="post" action="{{ route("page::image", ["id" => $item->id]) }}"
                      enctype="multipart/form-data">

                    @csrf

                    <div class="card mb-3">

                        @if($item->featuredImage)

                            <img src="{!! $item->featuredImage->generateImagePath(700,null) !!}" width="100%;"
                                 class="card-img-top">

                        @endif

                        <div class="card-header bg-dark text-white">
                            Featured image
                        </div>

                        <div class="card-body">

                            <div class="custom-file">
                                <input id="image" type="file" class="form-control" name="image">
                                <label class="form-label" for="image">Upload featured image</label>
                            </div>

                        </div>

                        <div class="card-footer">

                            <button type="submit" class="btn btn-success float-end btn-block">
                                Replace featured image
                            </button>

                        </div>

                    </div>

                </form>

                <form method="post" action="{{ route("page::file::create", ["id" => $item->id]) }}"
                      enctype="multipart/form-data">

                    @csrf

                    <div class="card mb-3">

                        <div class="card-header bg-dark text-white mb-1">
                            Attachments
                        </div>

                        @if ($item->files->count() > 0)

                            <table class="table table-hover table-sm">

                                <thead>

                                <tr class="bg-dark text-white">

                                    <td>Filename</td>
                                    <td>Controls</td>

                                </tr>

                                </thead>

                                @foreach($item->files as $file)

                                    <tr>

                                        <td class="ps-3 ellipsis">
                                            <a href="{{ $file->generatePath() }}" target="_blank">
                                                {{ $file->original_filename }}
                                            </a>
                                        </td>
                                        <td>
                                            @if(substr($file->mime, 0, 5) == 'image')
                                                <a class="pageEdit_insertImage" href="#"
                                                   rel="{{ $file->generateImagePath(1000, null) }}">
                                                    <i class="fas fa-image me-2 fa-fw"></i>
                                                </a>
                                            @else
                                                <a class="pageEdit_insertLink" href="#" role="button"
                                                   rel="{{ $file->generatePath() }}">
                                                    <i class="fas fa-link me-2 fa-fw"></i>
                                                </a>
                                            @endif
                                            <a href="{{ route('page::file::delete', ['id' => $item->id, 'file_id' => $file->id]) }}">
                                                <i class="fas fa-trash text-danger"></i>
                                            </a>
                                        </td>

                                    </tr>

                                @endforeach

                            </table>

                        @endif

                        <div class="card-body">

                            <div class="custom-file">
                                <input id="files" type="file" class="form-control" name="files[]" multiple>
                                <label class="form-label" for="files">Upload a file</label>
                            </div>

                        </div>

                        <div class="card-footer">

                            <button type="submit" class="btn btn-success btn-block">
                                Upload file
                            </button>

                        </div>

                    </div>

                </form>

            </div>

        @endif

    </div>

@endsection

@push('javascript')

    <script type="text/javascript" nonce="{{ csp_nonce() }}">
        // Borrowed from https://stackoverflow.com/questions/23733455/inserting-a-new-text-at-given-cursor-position
        function insertLineAtCursor(data) {
            const cm = document.getElementsByClassName('.CodeMirror')[0].CodeMirror;
            const doc = cm.getDoc();
            const cursor = doc.getCursor(); // gets the line number in the cursor position
            const line = doc.getLine(cursor.line); // get the line contents
            const pos = { // create a new object to avoid mutation of the original selection
                line: cursor.line, ch: line.length - 1, // set the character position to the end of the line
            };
            doc.replaceRange('\n' + data + '\n', pos); // adds a new line
        }

        const insertLinks = document.querySelectorAll('.pageEdit_insertLink, .pageEdit_insertImage');
        insertLinks.forEach(el => {
            el.addEventListener('click', e => {
                e.preventDefault();
                const linkUrl = e.target.getAttribute('rel');
                insertLineAtCursor(`[Link text](${linkUrl})`);
            });
        });
    </script>

@endpush
