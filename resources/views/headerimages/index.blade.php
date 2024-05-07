@extends('website.layouts.redesign.dashboard')

@section('page-title')
    Header Images
@endsection

@section('container')

    <div class="row justify-content-center">

        <div class="col-md-8">

            <div class="card mb-3">

                <div class="card-header bg-dark text-white mb-1">
                    @yield('page-title')
                    <a class="badge bg-info float-end" href="{{ route('headerimage::add') }}">
                        Add header image.</a>
                </div>

                <div class="table-responsive">
                <table class="table table-hover table-sm">

                    <thead>

                    <tr class="bg-dark text-white">
                        <td></td>
                        <td>Title</td>
                        <td></td>
                    </tr>

                    </thead>

                    @foreach($images as $image)

                        <tr>

                            <td>
                                <a href="{{ route('headerimage::delete', ['id' => $image->id]) }}"
                                   class="fa fa-trash-alt text-danger"></a>
                            </td>
                            <td>
                                <strong>{{ $image->title }}</strong><br>
                                <em>{!! $image->user ? $image->user->name : 'None' !!}</em>
                            </td>
                            <td>
                                <img src="{{ $image->image->generateUrl(1500, 400) }}" class="rounded float-end" height="100px">

                            </td>

                        </tr>

                    @endforeach

                </table>
                </div>

                <div class="card-footer pb-0">
                    {{ $images->links() }}
                </div>

            </div>

        </div>

    </div>

@endsection