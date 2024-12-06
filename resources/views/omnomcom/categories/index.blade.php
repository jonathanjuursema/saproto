@extends('website.layouts.redesign.dashboard')

@section('page-title')
    OmNomCom Product Category Administration
@endsection

@section('container')

    <div class="row justify-content-center">

        <div class="col-lg-6">

            <div class="card mb-3">

                <div class="card-header bg-dark text-white">
                    @yield('page-title')
                    <a href="{{ route('omnomcom::categories::create') }}" class="float-end badge bg-success">
                        Add new category
                    </a>
                </div>

                @if (count($categories) > 0)

                    <div class="table-responsive">
                        <table class="table table-borderless table-hover">

                            @foreach($categories as $category)

                                <tr>

                                    <td class="text-end">{{ $category->id }}</td>
                                    <td>
                                        {{ $category->name }}
                                    </td>
                                    <td>
                                        {{ $category->products_count }} products
                                    </td>
                                    <td>
                                        <a href="{{ route('omnomcom::categories::show', ['id' => $category->id]) }}">
                                            <i class="fas fa-edit me-2"></i>
                                        </a>
                                        @include('components.modals.confirm-modal', [
                                            'action' => route('omnomcom::categories::delete', ['id' => $category->id]),
                                            'text' => '<i class="fas fa-trash text-danger"></i>',
                                            'title' => 'Confirm Delete',
                                            'message' => "Are you sure you want to delete category $category->name",
                                            'confirm' => 'Delete',
                                        ])
                                    </td>

                                </tr>

                            @endforeach

                        </table>
                    </div>

                @endif

            </div>

        </div>

    </div>

@endsection
