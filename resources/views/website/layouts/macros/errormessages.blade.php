@if (isset($errors) && count($errors->all()) > 0)

    <div class="modal fade" id="flashModal" tabindex="-1" role="dialog" aria-labelledby="flashModalLabel">
        <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
            <div class="modal-content bg-danger text-white">
                <div class="modal-body text-center">
                    <h5 class="text-center">Whoops</h5>
                    @foreach($errors->all() as $e)
                        <div>
                        <i class="fas fa-exclamation-triangle mr-2"></i> {{ $e }}
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>

    <script type="text/javascript">
        $('#flashModal').modal('show');
    </script>

@endif