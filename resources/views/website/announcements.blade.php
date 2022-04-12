@foreach(Proto\Models\Announcement::all() as $announcement)

    @if($announcement->showForUser(Auth::user()))

        @if ($announcement->show_as_popup)

            <?php
                $announcement->dismissForUser(Auth::user());
            ?>

            <div class="modal fade" id="{{ $announcement->modal_id }}" tabindex="-1" role="dialog">
                <div class="modal-dialog" role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title">Announcement</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                            {!! Markdown::convertToHtml($announcement->content) !!}
                        </div>
                    </div>
                </div>
            </div>

            @push('javascript')

                <script type="text/javascript" nonce="{{ csp_nonce() }}">
                    window.onload = modals['{{ $announcement->modal_id}}'].show()
                </script>

            @endpush

        @else

            <div role="alert"
                 class="alert alert-{{ $announcement->bootstrap_style() }}">

                @if ($announcement->is_dismissable)
                    <span class="float-end">
                       <a href="{{ route('announcement::dismiss', ['id' => $announcement->id]) }}">
                           <i class="fas fa-times-circle" aria-hidden="true"></i>
                       </a>
                    </span>
                @endif

                {!! Markdown::convertToHtml($announcement->content) !!}

            </div>

        @endif

    @endif

@endforeach