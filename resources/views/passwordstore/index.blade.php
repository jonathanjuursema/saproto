@extends('website.layouts.redesign.dashboard')

@push('head')
    <meta http-equiv="refresh" content="{{ Session::get('passwordstore-verify') - time() }}">
@endpush

@section('page-title')
    Password Store
@endsection

@section('container')

    <div class="row justify-content-center">

        <div class="col-12 col-sm-2 mb-3">

            <a href="{{ route('passwordstore::create', ['type' => 'password']) }}"
               class="btn btn-success btn-block mb-3">
                Add Password
            </a>
            <a href="{{ route('passwordstore::create', ['type' => 'note']) }}" class="btn btn-success btn-block">
                Add Secure Note
            </a>

        </div>

        <div class="col-12 col-sm-8">

            <div class="card mb-3">

                <div class="card-header bg-dark text-white mb-1">
                    Password store
                </div>


                @if (count($passwords) > 0)

                    <div class="table-responsive">
                        <table class="table table-hover table-sm">

                            <thead>

                            <tr class="bg-dark text-white">

                                <td></td>
                                <td>Description</td>
                                <td>Access</td>
                                <td class="text-center">URL</td>
                                <td class="text-center">User</td>
                                <td class="text-center">Pass</td>
                                <td class="text-center">Comment</td>
                                <td>Age</td>
                                <td></td>
                                <td></td>

                            </tr>

                            </thead>

                                <?php $i = 0; ?>

                            @foreach($passwords as $password)

                                    <?php ++$i; ?>

                                @if($password->canAccess(Auth::user()))

                                    <tr>

                                        <td class="text-end">
                                            @if($password->username == null)
                                                <i class="fas fa-sticky-note" aria-hidden="true"></i>
                                            @else
                                                <i class="fas fa-key" aria-hidden="true"></i>
                                            @endif
                                        </td>

                                        <td>{{ $password->description }}</td>

                                        <td>{{ $password->permission->display_name }}</td>

                                        <td class="text-center">
                                            @if($password->url)
                                                <a href="{{ $password->url }}" target="_blank">
                                                    <i class="fas fa-globe-africa" aria-hidden="true"></i>
                                                </a>
                                            @endif
                                        </td>

                                        <td class="text-center">
                                            @if($password->username != null)
                                                <a id="{{ $password->description }}-username"
                                                   class="passwordmanager__copy"
                                                   data-copy="{{ Crypt::decrypt($password->username) }}"
                                                   data-bs-toggle="tooltip" data-bs-trigger="manual" title="Copied!">
                                                    <i class="fas fa-user me-1"></i>
                                                </a>
                                            @endif
                                        </td>

                                        <td class="text-center">
                                            @if($password->password != null)
                                                <a id="{{ $password->description }}-password"
                                                   class="passwordmanager__copy"
                                                   data-copy="{{ Crypt::decrypt($password->password) }}"
                                                   data-bs-toggle="tooltip" data-bs-trigger="manual" title="Copied!">
                                                    <i class="fas fa-key me-1"></i>
                                                </a>
                                            @endif
                                        </td>

                                        <td class="text-center">
                                            @if($password->note)
                                                <a class="passwordmanager__shownote" data-bs-toggle="modal"
                                                   data-bs-target="#passwordmodal-{{ $password->id }}">
                                                    <i class="fas fa-sticky-note"></i>
                                                </a>
                                            @endif
                                        </td>

                                        <td class="{{ ($password->age() > 12 ? 'text-danger' : 'text-primary') }}">
                                            {{ $password->age() }} months
                                        </td>

                                        <td>
                                            <a href="{{ route("passwordstore::edit", ['id' => $password->id]) }}">
                                                <i class="fas fa-edit me-2"></i>
                                            </a>
                                            <a href="{{ route("passwordstore::delete", ['id' => $password->id]) }}">
                                                <i class="fas fa-trash text-danger"></i>
                                            </a>
                                        </td>

                                    </tr>

                                @endif

                            @endforeach

                        </table>
                    </div>

                @else

                    <div class="card-body">
                        <p class="card-text text-centerØ">
                            There is nothing for you to see.
                        </p>
                    </div>

                @endif

            </div>

        </div>

    </div>

    @foreach($passwords as $password)

        @if($password->note != null)

            <div class="modal fade" id="passwordmodal-{{ $password->id }}" tabindex="-1" role="dialog"
                 aria-labelledby="passwordmodal-label-{{ $password->id }}">
                <div class="modal-dialog modal-lg" role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title">{{ $password->description }}</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                            <textarea class="form-control" rows="15" readonly>
                                {{ Crypt::decrypt($password->note) }}
                            </textarea>
                        </div>
                    </div>
                </div>
            </div>

        @endif

    @endforeach

@endsection

@push('javascript')

    <script type="text/javascript" nonce="{{ csp_nonce() }}">
        document.querySelectorAll('.passwordmanager__copy').forEach(el => {
            const copy = el.getAttribute('data-copy');
            el.addEventListener('click', _ => {
                navigator.clipboard.writeText(copy);
                let tooltip = tooltips[el.id];
                tooltip.show();
                setTimeout(_ => {
                    tooltip.hide();
                }, 1000);
            });
        });
    </script>

@endpush
