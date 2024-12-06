@extends('website.layouts.redesign.dashboard')

@section('page-title')
    Add Alias
@endsection

@section('container')

    <div class="row justify-content-center">

        <div class="col-md-4">

            <div class="card mb-3">

                <form method="post" action="{{ route("alias::store") }}">

                    <div class="card-header bg-dark text-white">
                        @yield('page-title')
                    </div>

                    <div class="card-body">

                        {!! csrf_field() !!}

                        <div class="input-group mb-3">
                            <input type="text" id="alias" class="form-control" placeholder="awesome-alias" name="alias"
                                   required>
                            <span
                                class="input-group-text">@ {{ Config::string('proto.emaildomain') }}</span>
                        </div>

                        <hr>

                        <label for="destination">Forward to an e-mail address:</label>
                        <div class="form-group">
                            <input type="text" class="form-control" id="destination" name="destination">
                        </div>

                        <div class="form-group autocomplete">
                            <label for="user">Or forward to a member:</label>
                            <input type="text" name="user" id="user" class="user-search form-control">
                        </div>

                    </div>

                    <div class="card-footer">

                        <button type="submit" class="btn btn-success float-end">Submit</button>

                        <a href="{{ route("alias::index") }}" class="btn btn-default">Cancel</a>

                    </div>

                </form>

            </div>

        </div>

    </div>

@endsection

@push('javascript')
    <script type="text/javascript" nonce="{{ csp_nonce() }}">
        document.getElementById('user').addEventListener('change', _ => {
            document.getElementById('destination').value = '';
        });

        document.getElementById('destination').addEventListener('change', _ => {
            document.getElementById('destination').focus();
            document.getElementById('user').value = '';
        });
    </script>

@endpush
