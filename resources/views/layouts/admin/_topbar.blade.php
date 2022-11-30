 <!-- Left navbar links -->
 <ul class="navbar-nav">
     <li class="nav-item">
         <a class="nav-link" data-widget="pushmenu" href="#" role="button"><i class="fas fa-bars"></i></a>
     </li>
     <li class="nav-item d-none d-sm-inline-block">
         <a href="{{ url('/dashboard') }}" class="nav-link">Home</a>
     </li>

 </ul>

 <!-- Right navbar links -->
 <ul class="navbar-nav ml-auto">
     <!-- Navbar Search -->
     {{-- <li class="nav-item">
         <a class="nav-link" data-widget="navbar-search" href="#" role="button">
             <i class="fas fa-search"></i>
         </a>
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
     </li> --}}



     <li class="nav-item dropdown ml-3">
         <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" data-toggle="dropdown"
             aria-haspopup="true" aria-expanded="false">
             {{ ucwords(auth()->user()->name) }}
         </a>
         <div class="dropdown-menu" aria-labelledby="navbarDropdown">
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
