@extends('website.layouts.redesign.dashboard')

@section('page-title')
    Short URL Service
@endsection

@section('container')
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card mb-3">
                <div class="card-header bg-dark text-white mb-1">
                    @yield('page-title')
                    <a
                        class="badge bg-info float-end"
                        href="{{ route('short_urls.create') }}"
                    >
                        Create Short URL
                    </a>
                </div>

                <div class="table-responsive">
                    <table class="table table-hover table-sm">
                        <thead>
                            <tr class="bg-dark text-white">
                                <td></td>
                                <td>Description</td>
                                <td>Url</td>
                                <td>Clicks</td>
                                <td class="text-center">Target</td>
                                <td>QR Code</td>
                            </tr>
                        </thead>

                        @foreach ($urls as $url)
                            <tr>
                                <td class="ps-2">
                                    @include(
                                        'components.modals.confirm-modal',
                                        [
                                            'action' => route('short_urls.destroy', ['short_url' => $url]),
                                            'method' => 'DELETE',
                                            'classes' => 'fas fa-trash text-danger',
                                            'text' => '',
                                            'confirm' => 'Delete',
                                            'title' => 'Confirm deleting the url ' . $url->url,
                                            'message' => 'Are you sure you want to delete ' . $url->url . '?',
                                        ]
                                    )
                                    <a
                                        href="{{ route('short_urls.edit', ['short_url' => $url]) }}"
                                        class="fa fa-pencil-alt text-success"
                                    ></a>
                                </td>
                                <td
                                    style="
                                        overflow-wrap: break-word;
                                        max-width: 200px;
                                    "
                                >
                                    {{ $url->description }}
                                </td>
                                <td>
                                    <span class="text-muted">
                                        saproto.nl/go
                                    </span>
                                    <strong>/{{ $url->url }}</strong>
                                </td>
                                <td>
                                    {{ $url->clicks }}
                                </td>
                                <td class="text-center">
                                    <button
                                        data-bs-toggle="popover"
                                        data-bs-placement="right"
                                        data-bs-trigger="focus"
                                        data-bs-content="{{ $url->target }}"
                                        class="btn badge bg-info"
                                    >
                                        <i class="fas fa-link text-white"></i>
                                    </button>
                                </td>

                                <td>
                                    <a
                                        href="#"
                                        id="url-{{ $url->id }}"
                                        class="qr-button"
                                        data-url-id="{{ $url->id }}"
                                        data-bs-toggle="modal"
                                        data-bs-target="#qr-modal"
                                    >
                                        Open QR
                                    </a>
                                </td>
                            </tr>
                        @endforeach
                    </table>
                </div>

                <div class="card-footer pb-0">
                    {{ $urls->links() }}
                </div>
            </div>
        </div>
    </div>
@endsection

@push('javascript')
    <script type="text/javascript" nonce="{{ csp_nonce() }}">
        document.querySelectorAll('.qr-button').forEach((el) =>
            el.addEventListener('click', (e) => {
                const modal = document.querySelector(
                    el.getAttribute('data-bs-target')
                )
                modal.querySelector('#qr-modal-url').src =
                    '{{ route('short_urls.qr_code', '') }}/' +
                    el.getAttribute('data-url-id')
                console.log(document.getElementById('qr-modal-url').src)
            })
        )

        document
            .querySelector('#qr-modal-copy')
            .addEventListener('click', (e) => {
                const image = document.getElementById('qr-modal-url')
                const canvas = document.createElement('canvas')
                const margin = 10 //the margin of the QR code in the image in percentage
                const scale = 10
                canvas.width = image.width * scale
                canvas.height = image.height * scale
                const ctx = canvas.getContext('2d')
                ctx.fillStyle = 'white'
                ctx.fillRect(0, 0, canvas.width, canvas.height)
                ctx.drawImage(
                    image,
                    (image.width * (margin / 100) * scale) / 2,
                    (image.height * (margin / 100) * scale) / 2,
                    (1 - margin / 100) * image.width * scale,
                    (1 - margin / 100) * image.height * scale
                )
                canvas.toBlob((blob) => {
                    navigator.clipboard.write([
                        new ClipboardItem({ 'image/png': blob }),
                    ])
                }, 'image/png')
            })
    </script>
@endpush

@once
    @push('modals')
        <div class="modal fade" id="qr-modal" tabindex="-1" role="dialog">
            <div class="modal-dialog model-sm" role="document">
                <form>
                    @csrf
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title">Short Url QR Code</h5>
                            <button
                                type="button"
                                class="btn-close"
                                data-bs-dismiss="modal"
                                aria-label="Close"
                            ></button>
                        </div>
                        <div class="modal-body">
                            <img
                                id="qr-modal-url"
                                alt="QR code"
                                class="bg-white p-2"
                                src=""
                                style="
                                    margin-left: auto;
                                    margin-right: auto;
                                    display: block;
                                "
                                width="400px"
                                height="400px"
                            />
                        </div>
                        <div
                            class="modal-footer d-flex justify-content-between"
                        >
                            <i
                                class="fas fa-info-circle fa-fw"
                                aria-hidden="true"
                            ></i>
                            <span>Right click QR to save as svg</span>
                            <button
                                type="button"
                                id="qr-modal-copy"
                                class="btn btn-primary"
                            >
                                Copy as png with background
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    @endpush
@endonce
