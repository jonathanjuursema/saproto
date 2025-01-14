@if (Session::has('flash_message'))
    <div class="modal fade" id="flash-modal" tabindex="-1" role="dialog">
        <div
            class="modal-dialog modal-dialog-centered modal-lg"
            role="document"
        >
            <div class="modal-content bg-dark text-white">
                <div class="modal-body text-center">
                    {!! Session::get('flash_message') !!}
                </div>
            </div>
        </div>
    </div>

    @push('javascript')
        <script type="text/javascript" nonce="{{ csp_nonce() }}">
            window.addEventListener('load', (_) => {
                modals['flash-modal'].show()
            })
        </script>
    @endpush
@endif
