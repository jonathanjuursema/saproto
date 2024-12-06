@extends('website.layouts.redesign.dashboard')

@section('page-title')
    Job offer Administration
@endsection

@section('container')

    <div class="row justify-content-center">

        <div class="col-md-6">

            <div class="card mb-3">

                <div class="card-header bg-dark text-white mb-1">
                    @yield('page-title')
                    <a href="{{ route('joboffers::create') }}" class="badge bg-info float-end">
                        Create a new job offer.
                    </a>
                </div>

                <table class="table table-sm table-hover">

                    <thead>

                    <tr class="bg-dark text-white">

                        <td>Company</td>
                        <td>Title</td>
                        <td class="text-end">Control</td>

                    </tr>

                    </thead>

                    @foreach($joboffers as $joboffer)

                        <tr>

                            <td>{{ $joboffer->company->name }}</td>
                            <td>{{ $joboffer->title}}</td>


                            <td class="text-end">
                                <a href="{{ route('joboffers::edit', ['id' => $joboffer->id]) }}">
                                    <i class="fas fa-edit me-2 fa-fw"></i>
                                </a>
                                <a href="{{ route('joboffers::delete', ['id' => $joboffer->id]) }}">
                                    <i class="fas fa-trash text-danger fa-fw"></i>
                                </a>
                            </td>

                        </tr>

                    @endforeach

                </table>

            </div>

        </div>

    </div>

@endsection
