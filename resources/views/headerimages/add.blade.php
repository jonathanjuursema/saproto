@extends('website.layouts.redesign.dashboard')

@section('page-title')
    Upload a new header image
@endsection

@section('container')

    <div class="row justify-content-center">

        <div class="col-md-4">

            <div class="card mb-3">

                <form method="post" action="{{ route("headerimages.store") }}" enctype="multipart/form-data">

                    {!! csrf_field() !!}

                    <div class="card-header bg-dark text-white">
                        @yield('page-title')
                    </div>

                    <div class="card-body">

                        <div class="form-group">
                            <label for="title">Image title:</label>
                            <input type="text" class="form-control" id="title" name="title"
                                   placeholder="Fancy photo" required>
                        </div>

                        <div class="form-group autocomplete">
                            <label for="user">Credits:</label>
                            <input class="form-control user-search" id="user" name="user" />
                        </div>

                        <div class="form-group">
                            <label for="name">Image:</label>
                            <div class="custom-file">
                                <input id="image" type="file" class="form-control" name="image" required>
                                <label for="image" class="form-label">Choose file</label>
                            </div>
                        </div>

                    </div>

                    <div class="card-footer">

                        <button type="submit" class="btn btn-success float-end">
                            Submit
                        </button>

                        <a href="{{ route("headerimages.index") }}" class="btn btn-default">Cancel</a>

                    </div>

                </form>

            </div>

        </div>

    </div>

    </form>

@endsection
