<nav id="nav" class="navbar navbar-default navbar-fixed-top">
    <div class="container-fluid">
        <!-- Brand and toggle get grouped for better mobile display -->
        <div class="navbar-header">
            <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#navbar"
                    aria-expanded="false" aria-controls="navbar">
                <span class="sr-only">Toggle navigation</span>
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
            </button>
            <a class="navbar-brand" href="{{ route('homepage') }}">Study Association Proto</a>
        </div>

        <!-- Collect the nav links, forms, and other content for toggling -->
        <div class="collapse navbar-collapse" id="navbar">
            <ul class="nav navbar-nav navbar-right">
                @foreach($menuItems as $menuItem)

                    @if(!$menuItem->is_member_only || (Auth::check() && Auth::user()->member()))

                        @if($menuItem->children->count() > 0)

                            <li class="dropdown">
                                <a href="{{ $menuItem->getUrl()  }}" class="dropdown-toggle" data-toggle="dropdown"
                                   role="button" aria-haspopup="true"
                                   aria-expanded="false">{{ $menuItem->menuname }} <span class="caret"></span></a>
                                <ul class="dropdown-menu">

                                    @foreach($menuItem->children->sortBy('order') as $childItem)
                                        @if(!$childItem->is_member_only || (Auth::check() && Auth::user()->member()))
                                            <li><a href="{{ $childItem->getUrl()  }}">{{ $childItem->menuname }}</a>
                                            </li>
                                        @endif
                                    @endforeach

                                </ul>
                            </li>

                        @else

                            <li>
                                <a href="{{ $menuItem->getUrl() }}" role="button" aria-haspopup="false"
                                   aria-expanded="false">{{ $menuItem->menuname }}</a>
                            </li>

                        @endif

                    @endif

                @endforeach

                @if (Auth::check() && Auth::user()->can(["omnomcom","pilscie"]))
                    <li class="dropdown">
                        <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true"
                           aria-expanded="false">OmNomCom <span class="caret"></span></a>
                        <ul class="dropdown-menu">
                            <li><a href="{{ route("omnomcom::store::show") }}">Application</a></li>
                            @if (Auth::check() && Auth::user()->can("omnomcom"))
                                <li role="separator" class="divider"></li>
                                <li><a class="navbar-title">Administration:</a></li>
                                <li><a href="{{ route("omnomcom::orders::adminlist") }}">Orders</a></li>
                                <li><a href="{{ route("omnomcom::products::list") }}">Products</a></li>
                                <li><a href="{{ route("omnomcom::categories::list") }}">Categories</a></li>
                                <li><a href="{{ route("omnomcom::generateorder") }}">Generate Supplier Order</a></li>
                            @endif

                            <li role="separator" class="divider"></li>

                            <li><a class="navbar-title">Utilities:</a></li>
                            <li><a href="{{ route("passwordstore::index") }}">Password Store</a></li>
                        </ul>
                    </li>
                @endif

                @if (Auth::check() && Auth::user()->can("board"))
                    <li class="dropdown">
                        <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true"
                           aria-expanded="false">Association Admin <span class="caret"></span></a>
                        <ul class="dropdown-menu">

                            <li><a href="{{ route("user::admin::list") }}">Users</a></li>
                            <li><a href="{{ route("study::list") }}">Studies</a></li>
                            <li><a href="{{ route("tickets::list") }}">Tickets</a></li>
                            <li><a href="{{ route("protube::admin") }}">ProTube Admin</a></li>

                            <li role="separator" class="divider"></li>

                            <li><a href="{{ route("committee::add") }}">Add Committee</a></li>
                            <li><a href="{{ route("event::add") }}">Add Event</a></li>

                            <li role="separator" class="divider"></li>

                            <li><a class="navbar-title">External Affairs:</a></li>
                            <li><a href="{{ route("narrowcasting::list") }}">Narrowcasting</a></li>
                            <li><a href="{{ route("companies::admin") }}">Companies</a></li>
                            <li><a href="{{ route("joboffers::admin") }}">Job offers</a></li>

                            <li role="separator" class="divider"></li>

                            <li><a class="navbar-title">Internal Affairs:</a></li>
                            <li><a href="{{ route("event::innewsletter::show") }}">Edit Newsletter</a></li>

                            @if (Auth::user()->can("finadmin"))
                                <li role="separator" class="divider"></li>
                                <li><a class="navbar-title">Financial:</a></li>
                                <li><a href="{{ route("omnomcom::accounts::list") }}">Accounts</a></li>
                                <li><a href="{{ route("event::financial::list") }}">Activities</a></li>
                                <li><a href="{{ route("omnomcom::withdrawal::list") }}">Withdrawals</a></li>
                                <li><a href="{{ route("omnomcom::unwithdrawable") }}">Unwithdrawable</a></li>
                                <li><a href="{{ route("omnomcom::mollie::list") }}">Mollie Payments</a></li>
                            @endif

                        </ul>
                    </li>
                @endif

                @if (Auth::check() && Auth::user()->can("board"))
                    <li class="dropdown">
                        <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true"
                           aria-expanded="false">Website Admin <span class="caret"></span></a>
                        <ul class="dropdown-menu">

                            <li><a href="{{ route("menu::list") }}">Menu</a></li>
                            <li><a href="{{ route("page::list") }}">Pages</a></li>
                            <li><a href="{{ route("news::admin") }}">News</a></li>
                            <li><a href="{{ route("email::admin") }}">Email</a></li>
                            <li><a href="{{ route("achievement::list") }}">Achievements</a></li>
                            <li><a href="{{ route("welcomeMessages::list") }}">Welcome Messages</a></li>

                            @if(Auth::user()->can('sysadmin'))
                                <li><a href="{{ route("alias::index") }}">Aliases</a></li>
                                <li><a href="{{ route("authorization::overview") }}">Authorization</a></li>
                            @endif

                            <li role="separator" class="divider"></li>

                            <li><a class="navbar-title">Utilities:</a></li>
                            <li><a href="{{ route("passwordstore::index") }}">Password Store</a></li>

                        </ul>
                    </li>
                @endif

                <form method="post" action="{{ route('search') }}" class="navbar-form navbar-right navbar__search">
                    {{ csrf_field() }}
                    <div class="input-group">
                        <input class="navbar__search__input form-control"
                               type="search" name="query" placeholder="Search">
                        <!--<span class="navbar__search__icon input-group-addon">
                            <i class="fa fa-search" aria-hidden="true"></i>
                        </span>-->
                        <span class="input-group-btn">
                            <button type="submit" class="navbar__search__icon" style=""><i class="fa fa-search"
                                                                                               aria-hidden="true"></i></button>
                        </span>
                    </div>
                </form>

                @if (Auth::check() && Auth::user()->member && Cookie::get('hideSlack', 'show') === 'show')
                    <li>
                        <a href="#" data-toggle="modal" data-target="#slack-modal">
                            Slack
                            <span class="badge"><i class="fa fa-circle green" aria-hidden="true"></i> <span id="slack__online">...</span></span>
                        </a>
                    </li>
                @endif

                @if (Auth::check())

                    @if(Auth::user()->isTempadmin() || (Auth::user()->can('protube') && !Auth::user()->can('board')))
                        <li>
                            <a href="{{ route("protube::admin") }}" role="button" aria-haspopup="false"
                               aria-expanded="false">ProTube Admin</a>
                        </li>
                    @endif

                    <li class="dropdown">
                        <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true"
                           aria-expanded="false"><img class="profile__photo profile__photo--small" src="{{ Auth::user()->photo->generateImagePath(64, 64) }}" alt="{{ Auth::user()->name }}"> <span class="caret"></span></a>
                        <ul class="dropdown-menu">
                            <li><a href="{{ route('user::dashboard') }}">Dashboard</a></li>
                            <li><a href="{{ route('omnomcom::orders::list') }}">Purchase History</a></li>

                            @if(Auth::check() && Auth::user()->member)
                                <li><a href="{{ route('user::profile') }}">My Profile</a></li>
                            @else
                                <li><a href="{{ route('becomeamember') }}">Become a member!</a></li>
                            @endif

                            @if (Session::has('impersonator'))
                                <li><a href="{{ route('user::quitimpersonating') }}">Quit Impersonation</a></li>
                            @else
                                <li><a href="{{ route('login::logout') }}">Logout</a></li>
                            @endif
                        </ul>
                    </li>
                @else

                    <li>
                        <a href="{{ route('login::register') }}">New Account</a>
                    </li>

                    <form class="navbar-form navbar-right">
                        <a class="btn btn-success" href="{{ route('login::show') }}">
                            LOG-IN
                        </a>
                    </form>

                @endif

            </ul>
        </div><!-- /.navbar-collapse -->
    </div><!-- /.container-fluid -->
</nav>
