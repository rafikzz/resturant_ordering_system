    <div class="container-fluid">
        <a href="{{ route('dashboard') }}" class="navbar-brand">
            {{-- <img src="../../dist/img/AdminLTELogo.png" alt="AdminLTE Logo" class="brand-image img-circle elevation-3"
                style="opacity: .8"> --}}
            <span class="brand-text font-weight-light">{{ config('app.name', 'Laravel') }}</span>

        </a>
        <button class="navbar-toggler order-1" type="button" data-toggle="collapse" data-target="#navbarCollapse"
            aria-controls="navbarCollapse" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse order-3" id="navbarCollapse">

            <ul class="navbar-nav">
                <ul class="navbar-nav">

                    <li class="nav-item d-none d-sm-inline-block">
                        <a href="{{ url('/dashboard') }}" class="nav-link">Home</a>
                    </li>

                </ul>
            </ul>
            @can('order_list')
            <ul class="navbar-nav">
                <ul class="navbar-nav">

                    <li class="nav-item d-none d-sm-inline-block">
                        <a href="{{ route('admin.orders.index') }}"
                        class="nav-link {{ Request::is('admin/orders*') ? 'active' : '' }}">
                        <p>Orders</p>
                    </a>
                    </li>

                </ul>
            </ul>
            @endcan

        </div>


        <ul class="order-1 order-md-3 navbar-nav navbar-no-expand ml-auto">

            <!-- Right navbar links -->
            <li class="nav-item">

                <div class="navbar-search-block">
                    <form class="form-inline">
                        <div class="input-group input-group-sm">
                            <input class="form-control form-control-navbar" type="search" placeholder="Search"
                                aria-label="Search">
                            <div class="input-group-append">
                                <button class="btn btn-navbar" type="submit">
                                    <i class="fas fa-search"></i>
                                </button>
                                <button class="btn btn-navbar" type="button" data-widget="navbar-search">
                                    <i class="fas fa-times"></i>
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </li>



            <li class="nav-item dropdown ml-3">
                <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button"
                    data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                    User
                </a>
                <div class="dropdown-menu" aria-labelledby="navbarDropdown">
                    <a class="dropdown-item disabled">{{ auth()->user()->name }}</a>
                    <div class="dropdown-divider"></div>
                    <a class="dropdown-item" href="{{ route('logout') }}"
                        onclick="event.preventDefault();
                          document.getElementById('logout-form').submit();">
                        {{ __('Logout') }}
                    </a>

                    <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
                        @csrf
                    </form>
                </div>
            </li>

        </ul>
    </div>
