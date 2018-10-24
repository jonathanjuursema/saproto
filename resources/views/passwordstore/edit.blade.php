@extends('website.layouts.redesign.dashboard')

@section('head')
    @parent
    <meta http-equiv="refresh" content="{{ Session::get('passwordstore-verify') - time() }}">
@endsection

@section('page-title')
    {{ ($password ? 'Edit' : 'Add') }} {{ ($type == 'password' ? 'Password' : 'Secure Note') }}
@endsection

@section('container')

    <div class="row justify-content-center">

        <div class="col-md-4">

            <form method="post"
                  action="{{ $password ? route('passwordstore::edit', ['id'=>$password->id]) : route('passwordstore::add') }}">

                {!! csrf_field() !!}

                <div class="card">

                    <div class="card-header bg-dark text-white">@yield('page-title')</div>

                    <div class="card-body">

                        <label>Resource description:</label>
                        <input class="form-control mb-3" type="text" name="description"
                               value="{{ $password->description or '' }}">

                        <label>Authorized users:</label>
                        <select name="permission_id" class="form-control mb-3" required>
                            @foreach(Permission::all() as $permission)
                                @if(Auth::user()->can($permission->name))
                                    <option value="{{ $permission->id }}" {{ ($password && $permission->id == $password->permission_id ? 'selected' : '') }}>
                                        {{ $permission->display_name }}
                                    </option>
                                @endif
                            @endforeach
                        </select>

                        @if($type == 'password')

                            <input type="hidden" name="type" value="password">

                            <label>Username:</label>
                            <input class="form-control mb-3" type="text" name="username"
                                   value="{{ $password ? Crypt::decrypt($password->username) : '' }}">

                            <label>Password:</label>
                            <input class="form-control mb-3" type="password" name="password"
                                   value="{{ $password ? Crypt::decrypt($password->password) : '' }}">

                            <label>Website URI:</label>
                            <input class="form-control" type="text" name="url" value="{{ $password->url or '' }}">

                        @else

                            <input type="hidden" name="type" value="note">

                            <textarea class="form-control mb-3" name="note" rows="10"
                                      placeholder="The content for this note.">{{ $password ? Crypt::decrypt($password->note) : '' }}</textarea>

                        @endif

                    </div>

                    <div class="card-footer">

                        <input type="submit" value="Save" class="btn btn-success float-right">

                        <a href="{{ route('passwordstore::index') }}" class="btn btn-default">
                            Cancel
                        </a>

                    </div>

                </div>

            </form>

        </div>

    </div>

@endsection