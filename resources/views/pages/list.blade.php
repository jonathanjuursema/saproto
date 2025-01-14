@extends('website.layouts.redesign.dashboard')

@section('page-title')
    Page Admin
@endsection

@section('container')
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card mb-3">
                <div class="card-header bg-dark text-white">
                    @yield('page-title')
                    <a
                        href="{{ route('page::create') }}"
                        class="badge bg-info float-end"
                    >
                        Create a new page.
                    </a>
                </div>

                <table class="table table-sm table-hover">
                    <thead>
                        <tr class="bg-dark text-white">
                            <td>Title</td>
                            <td>URL</td>
                            <td>Visibility</td>
                            <td>Controls</td>
                        </tr>
                    </thead>

                    @foreach ($pages as $page)
                        <tr>
                            <td>{{ $page->title }}</td>
                            <td>
                                <a
                                    href="{{ route('page::show', $page->slug) }}"
                                >
                                    {{ route('page::show', $page->slug) }}
                                </a>
                            </td>
                            <td>
                                @if ($page->is_member_only)
                                    <i class="fas fa-lock"></i>
                                @endif
                            </td>
                            <td>
                                <a
                                    href="{{ route('page::edit', ['id' => $page->id]) }}"
                                >
                                    <i class="fas fa-edit me-2"></i>
                                </a>

                                @include(
                                    'components.modals.confirm-modal',
                                    [
                                        'action' => route('page::delete', ['id' => $page->id]),
                                        'text' => '<i class="fas fa-trash text-danger"></i>',
                                        'title' => 'Confirm Delete',
                                        'message' => 'Are you sure you want to delete this page?',
                                        'confirm' => 'Delete',
                                    ]
                                )
                            </td>
                        </tr>
                    @endforeach
                </table>

                <div class="card-footer pb-0">
                    {!! $pages->links() !!}
                </div>
            </div>
        </div>
    </div>
@endsection
