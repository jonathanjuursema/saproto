<form class="form-horizontal" method="post" action="{{ route("user::changemail", ['id'=>$user->id]) }}">

    {!! csrf_field() !!}

    <div class="card mb-3">

        <div class="card-header bg-dark text-white">
            Your e-mail address
        </div>

        <div class="card-body">

            <p>
                Here you can change your e-mail address. You'll receive an e-mail on both the old and new address for
                confirmation. You need your password to change your e-mail address.
                <br><i>Note: For practical reasons you cannot set your e-mail address to an ".utwente.nl" account.</i>
            </p>

            <table class="table table-borderless table-sm mb-0">

                <tbody>

                <tr>
                    <th>E-mail</th>
                    <td>
                        <input type="email" class="form-control form-control-sm" id="email" name="email"
                               value="{{ $user->email }}" required>
                    </td>
                </tr>

                <tr>
                    <th>Current password</th>
                    <td>
                        <input type="password" class="form-control form-control-sm" id="password" name="password"
                               required>
                    </td>
                </tr>

                </tbody>

            </table>

        </div>

        <div class="card-footer">

            <button type="submit" class="btn btn-outline-info btn-block">
                Change your e-mail
            </button>

        </div>

    </div>

</form>

<form class="form-horizontal" method="post" action="{{ route("user::dashboard::show") }}">

    {!! csrf_field() !!}

    <div class="card mb-3">

        <div class="card-header bg-dark text-white">
            Your personal details
        </div>

        <div class="card-body">

            <table class="table table-borderless table-sm mb-0">

                <tbody>

                <tr>
                    <th>Name</th>
                    <td>{{ $user->name }} ({{{ $user->calling_name }}})</td>
                </tr>

                @if($user->did_study_create || $user->did_study_itech)
                    <tr>
                        <th>Studies attended</th>
                        <td>
                        <span
                            class="badge rounded-pill bg-{{ $user->did_study_create ? 'primary' : 'dark' }} text-white"
                            data-bs-toggle="tooltip" data-bs-placement="bottom"
                            title="Is this incorrect? Let the board know.">
                            <i class="fas fa-{{ $user->did_study_create ? 'check-' : null }}square fa-fw"></i>
                            Creative Technology
                        </span>
                            <span
                                class="badge rounded-pill bg-{{ $user->did_study_itech ? 'primary' : 'dark' }} text-white"
                                data-bs-toggle="tooltip" data-bs-placement="bottom"
                                title="Is this incorrect? Let the board know.">
                            <i class="fas fa-{{ $user->did_study_itech ? 'check-' : null }}square fa-fw"></i>
                            Interaction Technology
                        </span>
                        </td>
                    </tr>
                @endif

                @if($user->birthdate)
                    <tr>
                        <th>Birthdate</th>
                        <td>
                            {{ date('F j, Y', strtotime($user->birthdate)) }}
                            @if($user->is_member)
                                @include('components.forms.checkbox', [
                                    'name' => 'show_birthday',
                                    'checked' =>  $user->show_birthday,
                                    'label' => 'Show to members'
                                ])
                            @endif
                        </td>
                    </tr>
                @endif

                <tr>
                    <th>University account</th>
                    <td>
                        @if ($user->edu_username)
                            {{ $user->utwente_username ?? $user->edu_username }}
                            <a class="badge rounded-pill bg-danger float-end"
                               href="{{ route('user::edu::delete') }}">
                                <i class="fas fa-unlink fa-fw"></i>
                            </a>
                        @else
                            Not linked
                            <a class="badge rounded-pill bg-primary float-end" href="{{ route('user::edu::create') }}">
                                <i class="fas fa-user-plus fa-fw"></i>
                            </a>
                        @endif
                    </td>
                </tr>

                <tr>
                    <th>
                        Address
                    </th>
                    <td>

                        @if($user->address)
                            {{ $user->address->street }} {{ $user->address->number }}
                            @if($user->is_member)
                                <a class="badge rounded-pill bg-primary float-end"
                                   href="{{ route('user::address::edit') }}">
                                    <i class="far fa-edit fa-fw"></i>
                                </a>
                            @else
                                <a class="badge rounded-pill bg-primary float-end ms-2"
                                   href="{{ route('user::address::edit') }}">
                                    <i class="far fa-edit fa-fw"></i>
                                </a>
                                <a class="badge rounded-pill bg-danger float-end"
                                   href="{{ route('user::address::delete') }}">
                                    <i class="fas fa-eraser fa-fw"></i>
                                </a>
                            @endif
                            <br>
                            {{ $user->address->zipcode }} {{ $user->address->city }} ({{ $user->address->country }})
                            <br>
                            <p class="text-muted">
                                @if($user->address_visible)
                                    <i class="fas fa-user-friends fa-fw me-2"></i> Visible to members
                                @else
                                    <i class="fas fa-user-lock fa-fw me-2"></i> Visible to the board
                                @endif
                                <a class="badge rounded-pill bg-primary float-end"
                                   href="{{ route('user::address::togglehidden') }}">
                                    @if($user->address_visible)
                                        Hide from members.
                                    @else
                                        Make visible to members.
                                    @endif
                                </a>
                            </p>
                        @else
                            <a href="{{ route('user::address::create') }}">
                                Let us know your address
                            </a>
                        @endif

                    </td>
                </tr>

                @if($user->phone)
                    <tr>
                        <th>
                            <label for="phone">Phone</label>
                        </th>
                        <td>
                            <input type="tel" class="mb-1 form-control form-control-sm" id="phone" name="phone"
                                   value="{{ $user->phone }}">
                            @include('components.forms.checkbox', [
                                'name' => 'phone_visible',
                                'checked' =>  $user->phone_visible,
                                'label' => 'Show to members'
                            ])
                            @include('components.forms.checkbox', [
                                'name' => 'receive_sms',
                                'checked' =>  $user->receive_sms,
                                'label' => 'Receive messages'
                            ])
                        </td>
                    </tr>
                @endif

                <tr>
                    <th>
                        <label for="website">Homepage</label>
                    </th>
                    <td>
                        <input type="text" class="form-control form-control-sm" id="website" name="website"
                               value="{{ $user->website }}">
                    </td>
                </tr>

                <tr>
                    <th>OmNomCom</th>
                    <td>
                        @if($user->is_member)
                            @include('components.forms.checkbox', [
                                'name' => 'show_omnomcom_total',
                                'checked' =>  $user->show_omnomcom_total,
                                'label' => 'After checkout, show how much I\'ve spent today.'
                            ])
                            <small class="form-text text-muted mb-2 d-block">
                                This feature was requested by members who want to be aware of how much they spend.
                            </small>

                            @include('components.forms.checkbox', [
                                'name' => 'show_omnomcom_calories',
                                'checked' =>  $user->show_omnomcom_calories,
                                'label' => 'After checkout, show how many calories I\'ve bought today.'
                            ])
                            <small class="form-text text-muted mb-2 d-block">
                                This feature was requested by members who want to be aware of how much calories they
                                eat.
                            </small>

                            @include('components.forms.checkbox', [
                                'name' => 'disable_omnomcom',
                                'checked' =>  $user->disable_omnomcom,
                                'label' => 'Don\'t let me use the OmNomCom.'
                            ])
                            <small class="d-block text-warning mb-1"><i class="fas fa-warning me-1"></i> Only the board
                                can allow you access to the OmNomCom again.</small>
                            <small class="d-block mb-1"><i class="fas fa-info-circle me-1"></i> You can still sign-up
                                for
                                activities, and the board can manually buy something for you if you need that.
                            </small>
                            <small class="form-text text-muted mb-2 d-block">
                                This feature was requested by members who wanted some help controlling their
                                personal spendings.
                            </small>
                        @endif

                        @include('components.forms.checkbox', [
                            'name' => 'keep_omnomcom_history',
                            'checked' =>  $user->keep_omnomcom_history,
                            'label' => 'Keep my personal orderline history.'
                        ])
                        <small class="form-text text-muted mb-2 d-block">
                            We are required to keep financial information for 7 years.
                            If you disable this setting, your purchases will be anonymised after this time.
                        </small>
                    </td>
                </tr>

                <tr>
                    <th>Website</th>
                    <td>

                        <label class="form-check-label" for="theme">
                            Choose a theme
                        </label>
                        <select class="form-control" id="theme" name="theme">
                            @foreach(Config::array('proto.themes') as $i => $name)
                                <option value="{{ $i }}" @selected($user->theme == $i)>{{ ucwords($name) }}</option>
                            @endforeach
                        </select>
                        <small class="form-text text-muted">
                            This feature was requested by pretty much everyone.
                        </small>

                        @if(Carbon::now()->month===Carbon::DECEMBER)
                            <br>
                            <a href="{{route('december::toggle')}}">
                                <button type="button"
                                        class="btn btn-warning">{{Cookie::get('disable-december')==='disabled'?'enable':'disable'}}
                                    december theme
                                </button>
                            </a>
                        @endif

                    </td>
                </tr>

                @if($user->is_member)

                    <tr>
                        <th>Privacy</th>
                        <td>
                            @include('components.forms.checkbox', [
                                'name' => 'show_achievements',
                                'checked' =>  $user->show_achievements,
                                'label' => 'Show my achievements on my profile.'
                            ])
                            <small class="form-text text-muted d-block mb-2">
                                Achievements you obtain may reveal some personal details.<br>
                                Only members can see your achievements.
                            </small>

                            @include('components.forms.checkbox', [
                                'name' => 'profile_in_almanac',
                                'checked' =>  $user->profile_in_almanac,
                                'label' => 'Use my profile picture in the Lustrum Almanac.'
                            ])
                            <small class="form-text text-muted d-block">
                                With this you allow for the use of your profile picture in the Lustrum Alamanac
                                if one will be published during your Proto membership.
                            </small>
                        </td>
                    </tr>

                @endif

                </tbody>
            </table>

        </div>

        <div class="card-footer">

            <button type="submit" class="btn btn-outline-info btn-block">
                Save settings
            </button>

            @if($user->completed_profile && !$user->member)
                <a href="{{ route('user::memberprofile::showclear') }}" class="btn btn-outline-danger btn-block mt-3">
                    Clear information required only for members
                </a>
            @endif

            @if(!$user->completed_profile)
                <a href="{{ route('user::memberprofile::show') }}" class="btn btn-outline-info btn-block mt-3">
                    Complete profile for membership
                </a>
            @endif

        </div>

    </div>

</form>
