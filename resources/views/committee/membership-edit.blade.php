@extends('website.layouts.redesign.dashboard')

@section('page-title')
    Change committee membership: {{ $membership->user->name }} @ The {{ $membership->committee->name }}
@endsection

@section('panel-title')

@endsection

@section('container')

    <div class="row justify-content-center">

        <div class="col-md-4 col-sm-8 col-xs-12">

            <form class="form-horizontal"
                  action="{{ route('committee::membership::update', ["id" => $membership->id]) }}"
                  method="post">

                {!! csrf_field() !!}

                <div class="card mb-3">

                    <div class="card-header bg-dark text-white">@yield('page-title')</div>

                    <div class="card-body">

                        @include('committee.include.render-membership', [
                            'membership' => $membership
                        ])

                        <div class="row mb-3">

                            <div class="col-6">
                                <label>Role</label>
                                <input type="text" class="form-control" id="role" name="role"
                                       placeholder="General Member"
                                       value="{{ $membership->role }}">
                            </div>
                            <div class="col-6">
                                <label>Edition</label>
                                <input type="text" class="form-control" id="edition" name="edition"
                                       value="{{ $membership->edition }}">
                            </div>

                        </div>

                        <div class="row">

                            <div class="col-6">
                                @include('components.forms.datetimepicker', [
                                    'name' => 'start',
                                    'label' => 'Since',
                                    'placeholder' => strtotime($membership->created_at)
                                ])
                            </div>
                            <div class="col-6">
                                @include('components.forms.datetimepicker', [
                                    'name' => 'end',
                                    'label' => 'Until',
                                    'placeholder' => ($membership->deleted_at == null ? null : strtotime($membership->deleted_at)),
                                    'not_required' => true
                                ])
                            </div>

                        </div>

                    </div>

                    <div class="card-footer">

                        <button type="submit" class="btn btn-success float-end">
                            Save
                        </button>

                        <a href="{{ route('committee::membership::delete', ["id" => $membership->id]) }}"
                           class="btn btn-danger float-end me-4">
                            Was never a member
                        </a>

                        <a href="javascript:history.go(-1);" class="btn btn-default float-end">
                            Cancel
                        </a>

                    </div>

                </div>

            </form>


        </div>

    </div>

@endsection
