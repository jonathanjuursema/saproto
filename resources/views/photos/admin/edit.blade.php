@extends('website.layouts.redesign.generic')

@section('page-title')
    Edit {{ $album->name }} ({{ date('M j, Y', $album->date_taken) }})
@endsection

@section('container')

    @if($album->event)
        <a class="btn btn-info btn-block mb-3"
           href="{{ route('event::show', ['id'=>$album->event->getPublicId()]) }}">
            This album is linked to the event {{ $album->event->title }}, click here to go to the event.
        </a>
    @endisset

    <div class="row">
        <div class="col-lg-3">
            @if($album->published)
                @can('publishalbums')
                    <a class="btn btn-warning text-white btn-block mb-3"
                       href="{{ route('photo::admin::unpublish', ['id'=>$album->id]) }}">
                        This album is published so editing is limited, click here to unpublish the album.
                    </a>
                @else
                    <span class="btn btn-warning text-white btn-block mb-3 cursor-default">
                        This album is published so editing is limited, ask a Protography admin to unpublish it if you wish to make changed.
                    </span>
                @endcan
            @else
                @can('publishalbums')
                    <a class="btn btn-danger text-white btn-block mb-3"
                       href="{{ route('photo::admin::publish', ['id'=>$album->id]) }}">
                        This album is not yet published, click here to publish the album.
                    </a>
                @else
                    <span class="btn btn-warning text-white btn-block mb-3 cursor-default">
                        This album is not yet published, ask a Protography admin to publish it.
                    </span>
                @endcan
            @endif

            <a class="btn btn-info text-white btn-block mb-3"
               href="{{ route('photo::album::list', ['id' => $album->id]) }}">
                Preview album
            </a>

            <div class="card mb-3">

                @if(Auth::user()->can('publishalbums') || (Auth::user()->can('protography') && !$album->published))
                    <div class="card-header bg-dark text-white text-center">
                        Edit album
                    </div>

                    <form method="post">
                        {{ csrf_field() }}
                        <div class="card-body">
                            <div class="form-group">
                                <label for="album">Album name:</label>
                                <input required type="text" id="album" name="album" class="form-control"
                                       value="{{ $album->name }}">
                            </div>
                            @include('components.forms.datetimepicker', [
                                'name' => 'date',
                                'label' => 'Album date:',
                                'placeholder' => date($album->date_taken),
                                'format' => 'date'
                            ])
                            @include('components.forms.checkbox', [
                                'name' => 'private',
                                'checked' => $album->private,
                                'label' => 'Private album'
                            ])
                        </div>

                        <div class="card-footer">
                            <input type="submit" class="btn btn-success btn-block mb-1" value="Save">
                            <button type="button" class="btn btn-danger btn-block" data-bs-toggle="modal"
                                    data-bs-target="#delete-modal">
                                Delete Album
                            </button>
                        </div>

                    </form>
                @else
                    <div class="card-header bg-dark text-white text-center">
                        Edit album
                    </div>

                    <div class="card-body">
                        <b>Album name:</b> {{ $album->name }}<br>
                        <b>Album date:</b> {{ date('d-m-Y', $album->date_taken) }}<br>
                        <b>Private album:</b> <i class="fa fa-{{ $album->private ? "check" : "times"}}"></i>
                    </div>
                @endif
            </div>

            <div class="modal fade" id="delete-modal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel"
                 aria-hidden="true">
                <div class="modal-dialog" role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="exampleModalLabel">Delete Album</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                            This will delete the album and all the photos inside.<br>
                            Are you sure you want to delete the album?
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                            <a class="btn btn-danger"
                               href="{{ route('photo::admin::delete', ['id' => $album->id]) }}">
                                Delete Album
                            </a>
                        </div>
                    </div>
                </div>
            </div>


            <div class="card mb-3">

                <div class="card-header bg-dark text-white text-center">
                    Thumbnail
                </div>

                @if($album->thumb())
                    <div class="card-body"
                         style="height: 300px; background: url({{ $album->thumb() }}) no-repeat center; background-size: cover;"></div>
                @else
                    <div class="card-body d-flex opacity-25" style="height: 300px;">
                        <div class="text-center m-auto">
                            <i class="fa fa-image fa-5x"></i>
                            <p>No thumbnail set</p>
                        </div>
                    </div>
                @endisset

            </div>
        </div>

        <div class="col-lg-9">
            <div class="card mb-3">

                <div class="card-header bg-dark text-white text-center">
                    Add photos
                </div>
                @if(!$album->published)
                    <div class="card-body">
                        <div id="error-bar" class="alert alert-danger d-none" role="alert">
                            <p>The following files failed to upload:</p>
                            <ul></ul>
                        </div>
                        <div id="upload-view" class="row position-relative"></div>
                        <div id="droparea" class="d-flex opacity-25 border border-2 border-light rounded-3"
                             style="height: 200px">
                            <div id="droparea-content" class="text-center m-auto pointer-events-none">
                                <i class="fa fa-images fa-5x mt-2"></i>
                                <p>
                                    <span>Drop photos to upload</span>
                                    <span id="droparea-loader" class="spinner-border spinner-border-sm ms-1 d-none"
                                          role="status"></span>
                                </p>
                            </div>
                        </div>

                    </div>
                @else
                    <div class="card-footer text-center">
                        Uploading is disabled for published albums, unpublish the album to upload extra photos.
                    </div>
                @endif
            </div>


            <div class="card mb-3">
                <form method="POST" action="{{ route('photo::admin::action', ['id' => $album->id]) }}">
                    {{ csrf_field() }}

                    <div class="card-header bg-dark text-white text-center">
                        {{ $album->name }} ({{ date('M j, Y', $album->date_taken) }})
                    </div>

                    <div class="card-body">
                        @if(!$album->published || Auth::user()->can('publishalbums'))
                            <div class="row">
                                <div class="col-12 mb-4">
                                    <div class="btn-group" role="group" aria-label="Toolbar">
                                        @php
                                            $attr = $album->published ?
                                            'type=button data-bs-toggle=modal data-bs-target=#published-modal' :
                                            'type=submit'
                                        @endphp
                                        <button {{ $attr }} name="action" value="remove" class="btn btn-danger">
                                            <i class="fa fa-trash"></i> Remove
                                        </button>
                                        <button {{ $attr }} name="action" value="thumbnail" class="btn btn-success">
                                            <i class="fa fa-image"></i> Set thumbnail
                                        </button>
                                        <button {{ $attr }} name="action" value="private" class="btn btn-warning">
                                            <i class="fa fa-eye"></i> Toggle private
                                        </button>

                                    </div>

                                    <div class="modal fade" id="published-modal" tabindex="-1" role="dialog"
                                         aria-hidden="true">
                                        <div class="modal-dialog" role="document">
                                            <div class="modal-content">
                                                <div class="modal-header">
                                                    <h5 class="modal-title">Perform action</h5>
                                                    <button type="button" class="btn-close" data-bs-dismiss="modal"
                                                            aria-label="Close"></button>
                                                </div>
                                                <div class="modal-body">
                                                    This album has already been published. Are you sure you want perform
                                                    this action?
                                                </div>
                                                <div class="modal-footer">
                                                    <button type="button" class="btn btn-secondary"
                                                            data-bs-dismiss="modal">Close
                                                    </button>
                                                    <span id="confirm-button"></span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endif

                        <div id="photo-view" data-name="photos" class="row shift-select">

                            @foreach($album->items as $photo)

                                @include('photos.includes.selectablephoto', ['photo' => $photo])

                            @endforeach

                        </div>

                    </div>
                </form>

            </div>
        </div>
    </div>

@endsection

@push('javascript')

    <script async type="text/javascript" nonce="{{ csp_nonce() }}">
        window.addEventListener('load', _ => {
            let fileSizeLimit = '{{ $fileSizeLimit }}B';
            let fileId = 1;
            let uploadRunning = false;
            let dropArea = document.getElementById('droparea');

            document.getElementById('published-modal').addEventListener('show.bs.modal', e => {
                const footer = document.querySelector('#published-modal .modal-footer');
                const btn = e.relatedTarget.cloneNode(true);
                btn.type = 'submit';
                footer.replaceChild(btn, footer.lastChild);
            });

            if (dropArea && window.File && window.FileReader && window.FileList && window.Blob) {
                dropArea.addEventListener('dragenter', e => {
                    e.stopPropagation();
                    e.preventDefault();
                    dropArea.classList.remove('opacity-25');
                    e.dataTransfer.dropEffect = 'move';
                });
                dropArea.addEventListener('dragleave', e => {
                    e.stopPropagation();
                    e.preventDefault();
                    dropArea.classList.add('opacity-25');
                });

                dropArea.addEventListener('dragover', e => {
                    e.stopPropagation();
                    e.preventDefault();
                });
                window.addEventListener('drop', dropFiles);
            }

            function dropFiles(e) {
                console.log(e);
                e.stopPropagation();
                e.preventDefault();
                dropArea.classList.add('opacity-25');

                let files = e.dataTransfer.files;
                if (files.length) {
                    let fileQueue = [];
                    for (const file of files) {
                        if (['image/png', 'image/jpg', 'image/jpeg'].includes(file.type)) {
                            let fr = new FileReader();
                            fr.onload = async _ => {
                                file.id = fileId++;
                                fileQueue.push(file);
                                await uploadFiles(fileQueue);
                            };
                            fr.readAsDataURL(file);
                        }
                    }
                }
            }

            async function uploadFiles(fileQueue) {
                while (fileQueue.length) {
                    let file = fileQueue.shift();
                    let formData = new FormData();
                    formData.append('file', file);
                    toggleRunning();
                    await post('{{ route('photo::admin::upload', ['id' => $album->id], false) }}', formData, { parse: false })
                        .then(response => {
                            response.text().then(text => {
                                document.getElementById('photo-view').innerHTML += text;
                                document.getElementById('error-bar').classList.add('d-none');
                                document.querySelector('#error-bar ul').innerHTML = '';
                                toggleRunning();
                            });
                        })
                        .catch(err => {
                            let errText;
                            switch (err.status) {
                                case 413:
                                    errText = `Uploaded photo was bigger than limit of ${fileSizeLimit}.`;
                                    break;
                                default:
                                    errText = `Error ${err.status}: ${err.statusText}`;
                                    break;
                            }
                            console.error(errText, err);
                            uploadError(file, errText);
                            toggleRunning();
                        });
                }
            }

            function toggleRunning() {
                uploadRunning = !uploadRunning;
                const loader = document.getElementById('droparea-loader');
                loader.classList.toggle('d-none');
            }

            function uploadError(file, err) {
                document.getElementById('error-bar').classList.remove('d-none');
                document.querySelector('#error-bar ul').innerHTML += `<li> ${file.name} <small><i>${err}</i></small> </li>`;
            }
        });
    </script>

@endpush
