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
                    <a class="badge bg-info float-end" href="{{ route('headerimages.create') }}">
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
                                    @include('components.modals.confirm-modal', [
                                            'action' => route('headerimages.destroy', ['headerimage' => $image]),
                                            'method'=>'DELETE',
                                            'confirm' => 'Delete the headerimage',
                                            'classes' => 'fa fa-trash text-danger',
                                            'text' => '',
                                            'message' => "Are you sure you want to delete the headerimage?",
                                        ])
                                </td>
                                <td>
                                    <strong>{{ $image->title }}</strong><br>
                                    <em>{!! $image->user ? $image->user->name : 'None' !!}</em>
                                </td>
                                <td>
                                    <img src="{{ $image->image->generateImagePath(1500, 400) }}"
                                         class="rounded float-end" height="100px">

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
