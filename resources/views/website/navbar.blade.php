<header>
    <nav class="navbar navbar-expand-xl navbar-dark fixed-top bg-primary">
        <div class="container-fluid">
            <a class="navbar-brand" href="{{ route('homepage') }}">
                @if(config('app.env') != 'production')
                    <i class="fas fa-hammer me-2"></i>
                    <span class="text-uppercase">{{ config('app.env') }}</span> |
                @endif
                S.A. Proto
            </a>

            <button class="navbar-toggler outline-none" type="button" data-bs-toggle="collapse" data-bs-target="#navbar"
                    aria-controls="navbar" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>

            <div class="collapse navbar-collapse" id="navbar">
                <ul class="navbar-nav me-auto">
                    @foreach($menuItems as $menuItem)
                        @if(!$menuItem->is_member_only || (Auth::check() && Auth::user()->is_member))
                            @if($menuItem->children->count() > 0)
                                <li class="nav-item dropdown">
                                    <a href="{{ $menuItem->getUrl()  }}" class="nav-link dropdown-toggle"
                                       data-bs-toggle="dropdown" role="button" aria-haspopup="true"
                                       aria-expanded="false">
                                        {{ $menuItem->menuname }}
                                    </a>

                                    <ul class="dropdown-menu dropdown">
                                        @foreach($menuItem->children->sortBy('order') as $childItem)
                                            @if(!$childItem->is_member_only || (Auth::check() && Auth::user()->is_member))
                                                <a class="dropdown-item" href="{{ $childItem->getUrl()  }}">
                                                    {{ $childItem->menuname }}
                                                </a>
                                            @endif
                                        @endforeach
                                    </ul>
                                </li>
                            @else
                                <li class="nav-item">
                                    <a class="nav-link" href="{{ $menuItem->getUrl() }}" role="button"
                                       aria-haspopup="false"
                                       aria-expanded="false">{{ $menuItem->menuname }}</a>
                                </li>
                            @endif
                        @endif
                    @endforeach

                    @auth

                        @canany(['omnomcom', 'tipcie', 'drafters'])
                            <li class="nav-item dropdown">
                                <a href="#" class="nav-link dropdown-toggle" data-bs-toggle="dropdown" role="button"
                                   aria-haspopup="true" aria-expanded="false">
                                    OmNomCom
                                </a>

                                <ul class="dropdown-menu">
                                    @foreach(config('omnomcom.stores') as $name => $store)
                                        @if(in_array(Request::ip(), $store->addresses) || Auth::user()->hasAnyPermission($store->roles))
                                            <a class="dropdown-item"
                                               href="{{ route('omnomcom::store::show', ['store'=>$name]) }}">
                                                Open store: {{ $store->name }}
                                            </a>
                                        @endif
                                    @endforeach

                                    @can('omnomcom')
                                        <li role="separator" class="dropdown-divider"></li>
                                        <a class="dropdown-item" href="{{ route("omnomcom::orders::adminlist") }}">Orders</a>
                                        <a class="dropdown-item"
                                           href="{{ route("omnomcom::products::list") }}">Products</a>
                                        <a class="dropdown-item" href="{{ route("omnomcom::categories::list") }}">Categories</a>
                                        <a class="dropdown-item" href="{{ route("omnomcom::generateorder") }}">Generate
                                            Supplier Order</a>
                                        <a class="dropdown-item" href="{{ route("omnomcom::products::statistics") }}">Sales
                                            statistics</a>
                                    @endcan

                                    @can('tipcie')
                                        <li role="separator" class="dropdown-divider"></li>
                                        <a class="dropdown-item" href="{{ route("dinnerform::add") }}">Dinnerforms</a>
                                        <a class="dropdown-item" href="{{ route("omnomcom::tipcie::orderhistory") }}">TIPCie
                                            Order Overview</a>
                                        <a class="dropdown-item" href="{{ route("wallstreet::admin") }}">Wallstreet
                                            Drinks</a>
                                    @endcan

                                    @cannot('board')
                                        @canany(['tipcie', 'omnomcom'])
                                            <li role="separator" class="dropdown-divider"></li>
                                            <a class="dropdown-item" href="{{ route("passwordstore::index") }}">Password
                                                Store</a>
                                        @endcanany
                                    @endcannot
                                </ul>
                            </li>
                        @endcanany

                        @canany(['board', 'finadmin', 'alfred'])
                            <li id="admin-nav-item" class="nav-item dropdown">
                                <a href="#" class="nav-link dropdown-toggle" data-bs-toggle="dropdown" role="button"
                                   aria-haspopup="true" aria-expanded="false">Admin <span class="caret"></span></a>

                                <ul class="dropdown-menu">
                                    @can('board')
                                        <a class="dropdown-item" href="{{ route("user::admin::list") }}">Users</a>
                                        <a class="dropdown-item" href="{{ route("tickets::list") }}">Tickets</a>
                                        <a class="dropdown-item" href="{{ route("short_url::index") }}">Short URLs</a>
                                        <a class="dropdown-item" href="{{ route("queries::index") }}">Queries</a>

                                        <li role="separator" class="dropdown-divider"></li>
                                        <a class="dropdown-item" href="{{ route("tempadmin::index") }}">Temp ProTube Admin</a>
                                        <a class="dropdown-item" href="{{ config('protube.remote_url') }}">ProTube Admin</a>

                                        <li role="separator" class="dropdown-divider"></li>
                                        <a class="dropdown-item" href="{{ route("committee::add") }}">Add Committee</a>
                                        <a class="dropdown-item" href="{{ route("event::add") }}">Add Event</a>
                                        <a class="dropdown-item" href="{{ route("event::category::admin") }}">Event Categories</a>
                                        <a class="dropdown-item" href="{{ route("feedback::category::admin") }}">Feedback Categories</a>

                                        <li role="separator" class="dropdown-divider"></li>
                                        <a class="dropdown-item"
                                           href="{{ route("narrowcasting::list") }}">Narrowcasting</a>
                                        <a class="dropdown-item" href="{{ route("companies::admin") }}">Companies</a>
                                        <a class="dropdown-item" href="{{ route("joboffers::admin") }}">Job offers</a>
                                        <a class="dropdown-item"
                                           href="{{ route("leaderboards::admin") }}">Leaderboards</a>
                                    @endcan

                                    @if(Auth::user()->hasAllPermissions(['board', 'finadmin']))
                                        <li role="separator" class="dropdown-divider"></li>
                                    @endif

                                    @can('finadmin')
                                        <a class="dropdown-item"
                                           href="{{ route("omnomcom::accounts::list") }}">Accounts</a>
                                        <a class="dropdown-item"
                                           href="{{ route("event::financial::list") }}">Activities</a>
                                        <a class="dropdown-item" href="{{ route("omnomcom::withdrawal::list") }}">Withdrawals</a>
                                        <a class="dropdown-item" href="{{ route("omnomcom::unwithdrawable") }}">Unwithdrawable</a>
                                        <a class="dropdown-item" href="{{ route("omnomcom::mollie::list") }}">Mollie
                                            Payments</a>
                                        <a class="dropdown-item" href="{{ route("omnomcom::payments::statistics") }}">Cash
                                            & Card Payments</a>
                                    @endcan

                                    @canany(['alfred', 'board'])
                                        <li role="separator" class="dropdown-divider"></li>
                                        <a class="dropdown-item" href="{{ route("dmx::index") }}">Fixtures</a>
                                        <a class="dropdown-item" href="{{ route("dmx::override::index") }}">Override</a>
                                    @endcanany

                                    @canany(['alfred', 'sysadmin'])
                                        <li role="separator" class="dropdown-divider"></li>
                                        <a class="dropdown-item" href="{{ route("minisites::isalfredthere::admin") }}">Is
                                            Alfred There?</a>
                                    @endcanany
                                </ul>
                            </li>
                        @endcanany

                        @can('board')
                            <li class="nav-item dropdown">
                                <a href="#" class="nav-link dropdown-toggle" data-bs-toggle="dropdown" role="button"
                                   aria-haspopup="true"
                                   aria-expanded="false">Site <span class="caret"></span></a>

                                <ul class="dropdown-menu">
                                    <a class="dropdown-item" href="{{ route("menu::list") }}">Menu</a>
                                    <a class="dropdown-item" href="{{ route("video::admin::index") }}">Videos</a>
                                    <a class="dropdown-item" href="{{ route("page::list") }}">Pages</a>
                                    <a class="dropdown-item" href="{{ route("news::admin") }}">News</a>
                                    <a class="dropdown-item" href="{{ route("email::admin") }}">Email</a>
                                    <a class="dropdown-item" href="{{ route("achievement::list") }}">Achievements</a>
                                    <a class="dropdown-item" href="{{ route("leaderboards::admin") }}">Leaderboards</a>
                                    <a class="dropdown-item" href="{{ route("welcomeMessages::list") }}">Welcome
                                        Messages</a>
                                    <a class="dropdown-item" href="{{ route("news::add", ['is_weekly'=>true]) }}">Weekly Update</a>

                                    <li role="separator" class="dropdown-divider"></li>
                                    <a class="dropdown-item" href="{{ route("headerimage::index") }}">Header Images</a>
                                    <a class="dropdown-item" href="{{ route("photo::admin::index") }}">Photo Admin</a>

                                    @can('sysadmin')
                                        <li role="separator" class="dropdown-divider"></li>
                                        <a class="dropdown-item" href="{{ route("alias::index") }}">Aliases</a>
                                        <a class="dropdown-item"
                                           href="{{ route("announcement::index") }}">Announcements</a>
                                        <a class="dropdown-item" href="{{ route("authorization::overview") }}">Authorization</a>
                                        <li role="separator" class="dropdown-divider"></li>
                                        <a class="dropdown-item" href="{{ route("codex::index") }}">Codices</a>
                                    @endcan

                                    <li role="separator" class="dropdown-divider"></li>
                                    <a class="dropdown-item" href="{{ route("passwordstore::index") }}">Password
                                        Store</a>
                                </ul>
                            </li>
                        @endcanany

                        @cannot('board')
                            <li class="nav-item">
                                <a class="nav-link" href="{{ config('protube.remote_url') }}" target="_blank" role="button">
                                    ProTube 
                                    @if(Auth::user()->hasPermissionTo('protube') || Auth::user()->isTempadmin())
                                        Admin
                                    @endif
                                </a>
                            </li>
                            
                            @if(Auth::user()->hasAllPermissions(['protography', 'header-image']))
                                <li class="nav-item dropdown">
                                    <a href="#" class="nav-link dropdown-toggle" data-bs-toggle="dropdown" role="button"
                                       aria-haspopup="true" aria-expanded="false">Site <span class="caret"></span></a>
                                    <ul class="dropdown-menu">
                                        <a class="dropdown-item" href="{{ route("headerimage::index") }}">Header
                                            Images</a>
                                        <a class="dropdown-item" href="{{ route("photo::admin::index") }}">Photo
                                            Admin</a>
                                    </ul>
                                </li>
                            @endif

                            @can('protography')
                                <li class="nav-item">
                                    <a class="nav-link" href="{{ route("photo::admin::index") }}" role="button"
                                       aria-haspopup="false" aria-expanded="false">Photo Admin</a>
                                </li>
                            @endcan

                            @can('registermembers')
                                <li class="nav-item">
                                    <a class="nav-link" href="{{route('user::registrationhelper::list')}}" role="button"
                                       aria-haspopup="false" aria-expanded="false">Registration Helper</a>
                                </li>
                            @endcan

                            @can('senate')
                                <li class="nav-item">
                                    <a class="nav-link" href="{{route('codex::index')}}" role="button"
                                       aria-haspopup="false" aria-expanded="false">Codices</a>
                                </li>
                            @endcan

                        @endcan

                        @if(\App\Models\Leaderboard::isAdminAny(Auth::user()))
                            <li class="nav-item">
                                <a class="nav-link" href="{{ route("leaderboards::admin") }}" role="button"
                                   aria-haspopup="false" aria-expanded="false">Leaderboards Admin</a>
                            </li>
                        @endif
                    @endauth
                </ul>

                <form method="post" action="{{ route('search') }}" class="form-inline mt-2 mt-md-0 me-2 float-end">
                    {{ csrf_field() }}
                    <div class="input-group">
                        <input class="form-control bg-white text-black" placeholder="Search" type="search" name="query"
                               style="max-width: 125px;">
                        <button type="submit" class="input-group-text btn btn-info"><i class="fas fa-search"></i>
                        </button>
                    </div>
                </form>

                @if(Auth::check())
                    <form class="form-inline mt-2 mt-md-0">
                        <ul class="navbar-nav me-auto">
                            <li class="nav-item dropdown">
                                <a href="#" class="dropdown-toggle nav-link active" data-bs-toggle="dropdown"
                                   role="button" aria-haspopup="true" aria-expanded="false">
                                    {{ Auth::user()->calling_name }}
                                    <img id="profile-picture" class="rounded-circle ms-2" alt="your profile picture"
                                         width="100px" height=""100px src="{{ Auth::user()->generateSmallPhotoPath() }}"/>
                                </a>

                                <div class="dropdown-menu dropdown-menu-end mt-2">
                                    <a class="dropdown-item" href="{{ route('user::dashboard') }}">Dashboard</a>
                                    @if(Auth::user()->is_member)
                                        <a class="dropdown-item" href="{{ route('user::profile') }}">My Profile</a>
                                        <a class="dropdown-item" href="{{ route('photo::liked::list') }}">My Liked Photos</a>
                                    @else
                                        <a class="dropdown-item" href="{{ route('becomeamember') }}">Become a
                                            member!</a>
                                    @endif

                                    <a href="#" data-bs-toggle="modal" data-bs-target="#discord-modal"
                                       class="dropdown-item">
                                        Discord
                                        <span class="badge bg-secondary" style="transform: translateY(-1px)">
                                            <i class="fas fa-user me-1"></i> <span id="discord__online">...</span>
                                        </span>
                                    </a>

                                    <a class="dropdown-item" href="{{ route('protube::dashboard') }}">ProTube
                                        Dashboard</a>
                                    <a class="dropdown-item" href="{{ route('omnomcom::orders::list') }}">Purchase
                                        History</a>

                                    @if (Session::has('impersonator'))
                                        <a class="dropdown-item" href="{{ route('user::quitimpersonating') }}">Quit
                                            Impersonation</a>
                                    @else
                                        <a class="dropdown-item" href="{{ route('login::logout') }}">Logout</a>
                                    @endif
                                </div>
                            </li>
                        </ul>
                    </form>
                @else
                    <form class="form-inline mt-2 mt-md-0">
                        <a class="btn btn-outline-light me-2" href="{{ route('login::register') }}">
                            <i class="fas fa-user-plus me-2"></i> Register
                        </a>
                        <a class="btn btn-light" href="{{ route('login::show') }}">
                            <i class="fas fa-id-card fa-fw me-2"></i> Log-in
                        </a>
                    </form>
                @endif
            </div>
        </div>
    </nav>
</header>