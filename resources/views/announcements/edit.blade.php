@extends('website.layouts.redesign.dashboard')

@section('page-title')
    {{ ($announcement == null ? "Add announcement" : "Edit announcement.") }}
@endsection

@section('container')

    <div class="row justify-content-center">

        <div class="col-md-4">

            <form method="post"
                  action="{{ ($announcement == null ? route("announcement::store") : route("announcement::update", ['id' => $announcement->id])) }}"
                  enctype="multipart/form-data">

                <div class="card mb-3">

                    <div class="card-header bg-dark text-white">
                        @yield('page-title')
                    </div>

                    <div class="card-body">

                        @csrf

                        <div class="form-group">
                            <label for="name">Description:</label>
                            <input type="text" class="form-control" id="description" name="description"
                                   placeholder="Internal description." value="{{ $announcement?->description }}"
                                   required>
                        </div>

                        <div class="form-group">
                            <label for="editor">Announcement:</label>
                            @include('components.forms.markdownfield', [
                                'name' => 'content',
                                'placeholder' => "Awesome announcement goes here.",
                                'value' => $announcement == null ? null : $announcement->content
                            ])
                        </div>

                        @include('components.forms.datetimepicker', [
                            'name' => 'display_from',
                            'label' => 'Start:',
                            'placeholder' => $announcement ? strtotime($announcement->display_from) : strtotime(Carbon::now())
                        ])

                        @include('components.forms.datetimepicker', [
                            'name' => 'display_till',
                            'label' => 'End:',
                            'placeholder' => $announcement ? strtotime($announcement->display_till) : strtotime(Carbon::now()->endOfDay()   )
                        ])

                        <hr>

                        <p>
                            <strong>Announcement settings:</strong>
                        </p>

                        <div class="row">
                            <div class="col-md-4">
                                @include('components.forms.checkbox',[
                                    'name' => 'show_only_homepage',
                                    'checked' => $announcement?->show_only_homepage,
                                    'label' => 'Homepage only'
                                ])
                            </div>
                            <div class="col-md-4">
                                @include('components.forms.checkbox', [
                                    'name' => 'show_as_popup',
                                    'checked' => $announcement?->show_as_popup,
                                    'label' => 'Pop-up'
                                ])
                            </div>
                            <div class="col-md-4">
                                @include('components.forms.checkbox', [
                                    'name' => 'is_dismissable',
                                    'checked' => $announcement?->is_dismissable,
                                    'label' => 'Dismissable'
                                ])
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="show-style">Announcement style:</label>
                            <select id="show-style" name="show_style" class="form-control">
                                <option value="0" @selected(old('show_style', $announcement?->show_style == 0))>
                                    Primary (Green)
                                </option>
                                <option value="1" @selected(old('show_style', $announcement?->show_style == 1))>
                                    Info (Blue)
                                </option>
                                <option value="2" @selected(old('show_style', $announcement?->show_style == 2))>
                                    Warning (Yellow)
                                </option>
                                <option value="3" @selected(old('show_style', $announcement?->show_style == 3))>
                                    Danger (Red)
                                </option>
                            </select>
                        </div>

                        <p>
                            <strong>Users that can see this announcement:</strong>
                        </p>

                        <div class="row">
                            <div class="col-md-4">
                                @include('components.forms.checkbox', [
                                    'name' => 'show_guests',
                                    'checked' => $announcement?->show_guests,
                                    'label' => 'Guests'
                                ])
                            </div>
                            <div class="col-md-4">
                                @include('components.forms.checkbox', [
                                    'name' => 'show_users',
                                    'checked' => $announcement?->show_users,
                                    'label' => 'Users'
                                ])
                            </div>
                            <div class="col-md-4">
                                @include('components.forms.checkbox', [
                                    'name' => 'show_members',
                                    'checked' => $announcement?->show_members,
                                    'label' => 'Members'
                                ])
                            </div>
                        </div>

                        <p>
                            <strong>Further restrict this announcement:</strong>
                        </p>

                        <div class="row">
                            <div class="col-md-4">
                                @include('components.forms.checkbox', [
                                    'name' => 'show_only_new',
                                    'checked' => $announcement?->show_only_new,
                                    'label' => 'New users'
                                ])
                            </div>
                            <div class="col-md-4">
                                @include('components.forms.checkbox', [
                                    'name' => 'show_only_firstyear',
                                    'checked' => $announcement?->show_only_firstyear,
                                    'label' => 'First years'
                                ])
                            </div>
                            <div class="col-md-4">
                                @include('components.forms.checkbox', [
                                    'name' => 'show_only_active',
                                    'checked' => $announcement?->show_only_active,
                                    'label' => 'Active members'
                                ])
                            </div>
                        </div>

                    </div>

                    <div class="card-footer">

                        <button type="submit" class="btn btn-success float-end ms-4">
                            Submit
                        </button>

                        <a href="{{ route("announcement::index") }}" class="btn btn-default pull-end">Back</a>

                    </div>

                </div>

            </form>

        </div>

    </div>

@endsection
