<header>

    <nav class="navbar navbar-expand-xl navbar-dark fixed-top bg-primary">

        <a class="navbar-brand" href="{{ route('homepage') }}">
            @if(config('app.env') != 'production') <i class="fas fa-hammer mr-2"></i> @endif
            S.A. Proto
            @if(config('app.env') != 'production') | <span class="text-uppercase">{{ config('app.env') }}</span> @endif
        </a>
        <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbar" aria-controls="navbar"
                aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>

        <div class="collapse navbar-collapse" id="navbar">

            <ul class="navbar-nav mr-auto">

                @foreach($menuItems as $menuItem)

                    @if(!$menuItem->is_member_only || (Auth::check() && Auth::user()->member()))

                        @if($menuItem->children->count() > 0)

                            <li class="nav-item dropdown">
                                <a href="{{ $menuItem->getUrl()  }}" class="nav-link dropdown-toggle"
                                   data-toggle="dropdown"
                                   role="button" aria-haspopup="true"
                                   aria-expanded="false">{{ $menuItem->menuname }}</a>
                                <ul class="dropdown-menu">

                                    @foreach($menuItem->children->sortBy('order') as $childItem)
                                        @if(!$childItem->is_member_only || (Auth::check() && Auth::user()->member()))
                                            <a class="dropdown-item" href="{{ $childItem->getUrl()  }}">
                                                {{ $childItem->menuname }}
                                            </a>
                                        @endif
                                    @endforeach

                                </ul>
                            </li>

                        @else

                            <li class="nav-item">
                                <a class="nav-link" href="{{ $menuItem->getUrl() }}" role="button" aria-haspopup="false"
                                   aria-expanded="false">{{ $menuItem->menuname }}</a>
                            </li>

                        @endif

                    @endif

                @endforeach

                @if (Auth::check() && Auth::user()->can(["omnomcom","tipcie"]))
                    <li class="nav-item dropdown">
                        <a href="#" class="nav-link dropdown-toggle" data-toggle="dropdown" role="button"
                           aria-haspopup="true"
                           aria-expanded="false">OmNomCom</a>
                        <ul class="dropdown-menu">

                            @foreach(config('omnomcom.stores') as $name => $store)
                                @if(in_array(Request::ip(), $store->addresses) || Auth::user()->can($store->roles))
                                    <a class="dropdown-item"
                                       href="{{ route('omnomcom::store::show', ['store'=>$name]) }}">
                                        Open store: {{ $store->name }}
                                    </a>
                                @endif
                            @endforeach

                            @if (Auth::check() && Auth::user()->can("omnomcom"))
                                <li role="separator" class="dropdown-divider"></li>
                                <a class="dropdown-item" href="{{ route("omnomcom::orders::adminlist") }}">Orders</a>
                                <a class="dropdown-item" href="{{ route("omnomcom::products::list") }}">Products</a>
                                <a class="dropdown-item" href="{{ route("omnomcom::categories::list") }}">Categories</a>
                                <a class="dropdown-item" href="{{ route("omnomcom::generateorder") }}">
                                    Generate Supplier Order
                                </a>
                                <a class="dropdown-item" href="{{ route("omnomcom::products::statistics") }}">
                                    Sales statistics
                                </a>
                            @endif

                            <li role="separator" class="dropdown-divider"></li>

                            <a class="dropdown-item" href="{{ route("omnomcom::tipcie::orderhistory") }}">
                                TIPCie Order Overview
                            </a>
                            <a class="dropdown-item" href="{{ route("passwordstore::index") }}">Password Store</a>
                        </ul>
                    </li>
                @endif

                @if (Auth::check() && (Auth::user()->can(["board","finadmin","alfred"])))
                    <li class="nav-item dropdown">
                        <a href="#" class="nav-link dropdown-toggle" data-toggle="dropdown" role="button"
                           aria-haspopup="true"
                           aria-expanded="false">Admin <span class="caret"></span></a>
                        <ul class="dropdown-menu">

                            @if (Auth::user()->can("board"))

                                <a class="dropdown-item" href="{{ route("user::admin::list") }}">Users</a>
                                <a class="dropdown-item" href="{{ route("tickets::list") }}">Tickets</a>
                                <a class="dropdown-item" href="{{ route("protube::admin") }}">ProTube Admin</a>
                                <a class="dropdown-item" href="{{ route("tempadmin::index") }}">Temp Admin Admin</a>

                                <li role="separator" class="dropdown-divider"></li>

                                <a class="dropdown-item" href="{{ route("committee::add") }}">Add Committee</a>
                                <a class="dropdown-item" href="{{ route("event::add") }}">Add Event</a>

                                <li role="separator" class="dropdown-divider"></li>

                                <a class="dropdown-item" href="{{ route("narrowcasting::list") }}">Narrowcasting</a>
                                <a class="dropdown-item" href="{{ route("companies::admin") }}">Companies</a>
                                <a class="dropdown-item" href="{{ route("joboffers::admin") }}">Job offers</a>

                                <li role="separator" class="dropdown-divider"></li>

                                <a class="dropdown-item" href="{{ route("newsletter::show") }}">Edit Newsletter</a>

                            @endif

                            @if (Auth::user()->can("board") && Auth::user()->can("finadmin"))

                                <li role="separator" class="dropdown-divider"></li>

                            @endif

                            @if (Auth::user()->can("finadmin"))

                                <a class="dropdown-item" href="{{ route("omnomcom::accounts::list") }}">Accounts</a>
                                <a class="dropdown-item" href="{{ route("event::financial::list") }}">Activities</a>
                                <a class="dropdown-item" href="{{ route("omnomcom::withdrawal::list") }}">
                                    Withdrawals
                                </a>
                                <a class="dropdown-item" href="{{ route("omnomcom::unwithdrawable") }}">
                                    Unwithdrawable
                                </a>
                                <a class="dropdown-item" href="{{ route("omnomcom::mollie::list") }}">
                                    Mollie Payments
                                </a>

                            @endif

                            @if(Auth::user()->can(["alfred","board"]))

                                <li role="separator" class="dropdown-divider"></li>

                                <a class="dropdown-item" href="{{ route("dmx::index") }}">Fixtures</a>
                                <a class="dropdown-item" href="{{ route("dmx::override::index") }}">Override</a>

                            @endif

                        </ul>
                    </li>
                @endif

                @if (Auth::check() && Auth::user()->can("board"))
                    <li class="nav-item dropdown">
                        <a href="#" class="nav-link dropdown-toggle" data-toggle="dropdown" role="button"
                           aria-haspopup="true"
                           aria-expanded="false">Site <span class="caret"></span></a>
                        <ul class="dropdown-menu">

                            <a class="dropdown-item" href="{{ route("menu::list") }}">Menu</a>
                            <a class="dropdown-item" href="{{ route("video::admin::index") }}">Videos</a>
                            <a class="dropdown-item" href="{{ route("page::list") }}">Pages</a>
                            <a class="dropdown-item" href="{{ route("news::admin") }}">News</a>
                            <a class="dropdown-item" href="{{ route("email::admin") }}">Email</a>
                            <a class="dropdown-item" href="{{ route("achievement::list") }}">Achievements</a>
                            <a class="dropdown-item" href="{{ route("welcomeMessages::list") }}">Welcome Messages</a>

                            @if(Auth::user()->can('sysadmin'))
                                <li role="separator" class="dropdown-divider"></li>
                                <a class="dropdown-item" href="{{ route("protube::radio::index") }}">
                                    ProTube Radio Stations
                                </a>
                                <a class="dropdown-item" href="{{ route("protube::display::index") }}">
                                    ProTube Displays
                                </a>
                                <a class="dropdown-item" href="{{ route("protube::soundboard::index") }}">
                                    Soundboard Sounds
                                </a>
                                <a class="dropdown-item" href="{{ route("headerimage::index") }}">
                                    Header Images
                                </a>
                                <a class="dropdown-item" href="{{ route("alias::index") }}">Aliases</a>
                                <a class="dropdown-item" href="{{ route("announcement::index") }}">Announcements</a>
                                <a class="dropdown-item" href="{{ route("authorization::overview") }}">Authorization</a>
                            @endif

                            <li role="separator" class="dropdown-divider"></li>

                            <a class="dropdown-item" href="{{ route("passwordstore::index") }}">Password Store</a>

                        </ul>
                    </li>
                @endif

                @if(Auth::check() && (Auth::user()->isTempadmin() || (Auth::user()->can('protube') && !Auth::user()->can('board'))))
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route("protube::admin") }}" role="button" aria-haspopup="false"
                           aria-expanded="false">ProTube Admin</a>
                    </li>
                @endif

            </ul>

            <form method="post" action="{{ route('search') }}" class="form-inline mt-2 mt-md-0 mr-2 float-right">
                {{ csrf_field() }}
                <div class="input-group">
                    <input type="text" class="form-control"
                           placeholder="Search" type="search" name="query" style="max-width: 125px;">
                    <div class="input-group-append">
                        <button type="submit" class="input-group-text btn btn-info">
                            <i class="fas fa-search"></i>
                        </button>
                    </div>
                </div>
            </form>

            @if (Auth::check())

                <form class="form-inline mt-2 mt-md-0">

                    <ul class="navbar-nav mr-auto">

                        <li class="nav-item dropdown float-right">
                            <a href="#" class="dropdown-toggle nav-link active" data-toggle="dropdown" role="button"
                               aria-haspopup="true"
                               aria-expanded="false">
                                {{ Auth::user()->calling_name }}
                                <img class="rounded-circle ml-2"
                                     src="{{ Auth::user()->generatePhotoPath(100, 100) }}"
                                     style="width: 45px; height: 45px; border: 2px solid white; margin: -14px 0 -11px 0;">
                            </a>
                            <ul class="dropdown-menu dropdown-menu-right">
                                <a class="dropdown-item" href="{{ route('user::dashboard') }}">Dashboard</a>
                                <a class="dropdown-item" href="{{ route('omnomcom::orders::list') }}">
                                    Purchase History
                                </a>

                                @if(Auth::check() && Auth::user()->member)
                                    <a class="dropdown-item" href="{{ route('user::profile') }}">My Profile</a>
                                @else
                                    <a class="dropdown-item" href="{{ route('becomeamember') }}">Become a member!</a>
                                @endif

                                @if (Auth::check() && Auth::user()->member)
                                    <a href="#" data-toggle="modal" data-target="#slack-modal" class="dropdown-item">
                                        Slack
                                        <span class="badge badge-secondary">
                                            <i class="fas fa-circle green"></i> <span id="slack__online">...</span>
                                        </span>
                                    </a>
                                @endif

                                @if (Session::has('impersonator'))
                                    <a class="dropdown-item" href="{{ route('user::quitimpersonating') }}">
                                        Quit Impersonation
                                    </a>
                                @else
                                    <a class="dropdown-item" href="{{ route('login::logout') }}">Logout</a>
                                @endif
                            </ul>
                        </li>

                    </ul>

                </form>

            @else

                <form class="form-inline mt-2 mt-md-0">
                    <a class="btn btn-outline-light" href="{{ route('login::register') }}"
                       style="margin-right: 10px;">
                        <i class="fas fa-user-plus mr-2"></i> Register
                    </a>
                    <a class="btn btn-light" href="{{ route('login::show') }}"><i class="fas fa-id-card fa-fw mr-2"></i>
                        Log-in</a>
                </form>

                </form>

            @endif

        </div>

    </nav>

</header>