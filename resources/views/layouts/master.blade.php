<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta content="width=device-width, initial-scale=1, maximum-scale=1, shrink-to-fit=no" name="viewport">
  <title>@yield('title') &mdash; Pharmaceutical</title>

  <!-- General CSS Files -->
  <link rel="stylesheet" href="{{ asset('modules/bootstrap/css/bootstrap.min.css') }}">
  <link rel="stylesheet" href="{{ asset('modules/fontawesome/css/all.min.css') }}">

  <!-- CSS Libraries -->
  <link rel="stylesheet" href="{{ asset('modules/bootstrap-daterangepicker/daterangepicker.css') }}">
  <link rel="stylesheet" href="{{ asset('modules/datatables/datatables.min.css') }}">
  <link rel="stylesheet" href="{{ asset('modules/izitoast/css/iziToast.min.css') }}">
  <link rel="stylesheet" href="{{ asset('modules/select2/dist/css/select2.min.css') }}">

  <!-- Template CSS -->
  <link rel="stylesheet" href="{{ asset('css/style.css') }}">
  <link rel="stylesheet" href="{{ asset('css/components.css') }}">

  @stack('styles')
<!-- Start GA -->
<script async src="https://www.googletagmanager.com/gtag/js?id=UA-94034622-3"></script>
<script>
  window.dataLayer = window.dataLayer || [];
  function gtag(){dataLayer.push(arguments);}
  gtag('js', new Date());

  gtag('config', 'UA-94034622-3');
</script>
<!-- /END GA --></head>

<body>
  <div id="app">
    <div class="main-wrapper main-wrapper-1">
      <div class="navbar-bg"></div>
      <nav class="navbar navbar-expand-lg main-navbar">
        <ul class="navbar-nav mr-auto">
          <li><a href="#" data-toggle="sidebar" class="nav-link nav-link-lg"><i class="fas fa-bars"></i></a></li>
          <li><a href="#" data-toggle="search" class="nav-link nav-link-lg d-sm-none"><i class="fas fa-search"></i></a></li>
        </ul>
        <ul class="navbar-nav navbar-right">
          <li class="dropdown"><a href="#" data-toggle="dropdown" class="nav-link dropdown-toggle nav-link-lg nav-link-user">
            <img alt="image" src="{{ asset('img/avatar/avatar-1.png') }}" class="rounded-circle mr-1">
            <div class="d-sm-none d-lg-inline-block">Hi, {{ Auth::user()->name }}</div></a>
            <div class="dropdown-menu dropdown-menu-right">
              <a href="{{ route('logout') }}" class="dropdown-item has-icon text-danger" onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                <i class="fas fa-sign-out-alt"></i> Logout
              </a>
              <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
                  @csrf
              </form>
            </div>
          </li>
        </ul>
      </nav>
      <div class="main-sidebar sidebar-style-2">
        <aside id="sidebar-wrapper">
          <div class="sidebar-brand">
            <a href="{{ action('HomeController@index') }}" style="font-weight: bold;font-size: 18px;color:#30b067">
              {{--<img src="{{ asset('img/logo.png') }}" alt="logo" style="height: calc(100% - 10px);">--}}
                Pharmaceutical
            </a>
          </div>
          <div class="sidebar-brand sidebar-brand-sm">
            <a href="{{ action('HomeController@index') }}" style="font-weight: bold;font-size: 18px;color:#30b067">P</a>
          </div>
          <ul class="sidebar-menu">
            <li class="menu-header">Menu</li>
            <li><a class="nav-link" href="{{ action('HomeController@index') }}"><i class="fas fa-tachometer-alt"></i> <span>Dashboard</span></a></li>

            @role('admin')
            {{--
            <li class="dropdown">
              <a href="#" class="nav-link has-dropdown"><i class="fas fa-layer-group"></i><span>Categories</span></a>
              <ul class="dropdown-menu">
                <li><a class="nav-link" href="{{ action('CategoryController@index') }}">List Categories</a></li>
                <li><a class="nav-link" href="{{ action('CategoryController@create') }}">Add Category</a></li>
              </ul>
            </li>
            --}}
            <li><a class="nav-link" href="{{ action('ImportController@index') }}"><i class="fas fa-history"></i> <span>Import History</span></a></li>
            <li class="dropdown">
              <a href="#" class="nav-link has-dropdown"><i class="fas fa-layer-group"></i><span>Divisions</span></a>
              <ul class="dropdown-menu">
                <li><a class="nav-link" href="{{ action('DivisionController@index') }}">List Divisions</a></li>
                <li><a class="nav-link" href="{{ action('DivisionController@create') }}">Add Division</a></li>
              </ul>
            </li>
            <li class="dropdown">
              <a href="#" class="nav-link has-dropdown"><i class="fas fa-clone"></i><span>Verticals</span></a>
              <ul class="dropdown-menu">
                <li><a class="nav-link" href="{{ action('VerticalController@index') }}">List Verticals</a></li>
                <li><a class="nav-link" href="{{ action('VerticalController@create') }}">Add Vertical</a></li>
              </ul>
            </li>
            <li class="dropdown">
              <a href="#" class="nav-link has-dropdown"><i class="fas fa-cubes"></i><span>Brands</span></a>
              <ul class="dropdown-menu">
                <li><a class="nav-link" href="{{ action('BrandController@index') }}">List Brands</a></li>
                <li><a class="nav-link" href="{{ action('BrandController@create') }}">Add Brand</a></li>
              </ul>
            </li>
            <li class="dropdown">
              <a href="#" class="nav-link has-dropdown"><i class="fas fa-balance-scale"></i><span>Units</span></a>
              <ul class="dropdown-menu">
                <li><a class="nav-link" href="{{ action('UnitController@index') }}">List Units</a></li>
                <li><a class="nav-link" href="{{ action('UnitController@create') }}">Add Unit</a></li>
              </ul>
            </li>
            <li class="dropdown">
              <a href="#" class="nav-link has-dropdown"><i class="fas fa-tags"></i><span>Products</span></a>
              <ul class="dropdown-menu">
                <li><a class="nav-link" href="{{ action('ProductController@index') }}">List Products</a></li>
                <li><a class="nav-link" href="{{ action('ProductController@create') }}">Add Product</a></li>
              </ul>
            </li>
            <li class="dropdown">
              <a href="#" class="nav-link has-dropdown"><i class="fas fa-percentage"></i><span>Schemes</span></a>
              <ul class="dropdown-menu">
                <li><a class="nav-link" href="{{ action('SchemeController@index') }}">List Schemes</a></li>
                <li><a class="nav-link" href="{{ action('SchemeController@create') }}">Add Scheme</a></li>
              </ul>
            </li>
            {{--
            <li class="dropdown">
              <a href="#" class="nav-link has-dropdown"><i class="fas fa-map-marker-alt"></i><span>Locations</span></a>
              <ul class="dropdown-menu">
                <li><a class="nav-link" href="{{ action('LocationController@index') }}">List Locations</a></li>
                <li><a class="nav-link" href="{{ action('LocationController@create') }}">Add Location</a></li>
              </ul>
            </li>
            --}}
            <li class="dropdown">
              <a href="#" class="nav-link has-dropdown"><i class="fas fa-store-alt"></i><span>Customers</span></a>
              <ul class="dropdown-menu">
                <li><a class="nav-link" href="{{ action('CustomerController@index') }}">List Customers</a></li>
                <li><a class="nav-link" href="{{ action('CustomerController@create') }}">Add Customer</a></li>
              </ul>
            </li>
            <li class="dropdown">
              <a href="#" class="nav-link has-dropdown"><i class="fas fa-grip-vertical"></i><span>Customer Types</span></a>
              <ul class="dropdown-menu">
                <li><a class="nav-link" href="{{ action('CustomerTypeController@index') }}">List Customer Types</a></li>
                <li><a class="nav-link" href="{{ action('CustomerTypeController@create') }}">Add Customer Type</a></li>
              </ul>
            </li>
              <li class="dropdown">
                  <a href="#" class="nav-link has-dropdown"><i class="fas fa-rainbow"></i><span>Product Types</span></a>
                  <ul class="dropdown-menu">
                      <li><a class="nav-link" href="{{ action('ProductTypeController@index') }}">List Product Types</a></li>
                      <li><a class="nav-link" href="{{ action('ProductTypeController@create') }}">Add Product Type</a></li>
                  </ul>
              </li>
            <li class="dropdown">
              <a href="#" class="nav-link has-dropdown"><i class="fas fa-store-alt"></i><span>Customer Classes</span></a>
              <ul class="dropdown-menu">
                <li><a class="nav-link" href="{{ action('CustomerClassController@index') }}">List Customer Classes</a></li>
                <li><a class="nav-link" href="{{ action('CustomerClassController@create') }}">Add Customer Class</a></li>
              </ul>
            </li>
            <li><a class="nav-link" href="{{ action('OrderController@index') }}"><i class="fas fa-list"></i> <span>Orders</span></a></li>
            <li class="dropdown">
              <a href="#" class="nav-link has-dropdown"><i class="fas fa-building"></i><span>Distributors</span></a>
              <ul class="dropdown-menu">
                <li><a class="nav-link" href="{{ action('DistributorController@index') }}">List Distributors</a></li>
                <li><a class="nav-link" href="{{ action('DistributorController@create') }}">Add Distributor</a></li>
              </ul>
            </li>
            <li class="dropdown">
              <a href="#" class="nav-link has-dropdown"><i class="fas fa-route"></i><span>Beats</span></a>
              <ul class="dropdown-menu">
                <li><a class="nav-link" href="{{ action('RouteController@index') }}">List Beats</a></li>
                <li><a class="nav-link" href="{{ action('RouteController@create') }}">Add Beat</a></li>
              </ul>
            </li>
            {{--
            <li class="dropdown">
              <a href="#" class="nav-link has-dropdown"><i class="fas fa-users"></i><span>Users</span></a>
              <ul class="dropdown-menu">
                <li><a class="nav-link" href="{{ action('UserController@index') }}">List Users</a></li>
                <li><a class="nav-link" href="{{ action('UserController@create') }}">Add User</a></li>
              </ul>
            </li>
            --}}
            <li class="dropdown">
              <a href="#" class="nav-link has-dropdown"><i class="fas fa-users"></i><span>DSMs</span></a>
              <ul class="dropdown-menu">
                <li><a class="nav-link" href="{{ action('DsmController@index') }}">List DSMs</a></li>
                <li><a class="nav-link" href="{{ action('DsmController@create') }}">Add DSM</a></li>
              </ul>
            </li>
            <li class="dropdown">
              <a href="#" class="nav-link has-dropdown"><i class="fas fa-receipt"></i><span>Sales Officers</span></a>
              <ul class="dropdown-menu">
                <li><a class="nav-link" href="{{ action('SalesOfficerController@index') }}">List Sales Officers</a></li>
                <li><a class="nav-link" href="{{ action('SalesOfficerController@create') }}">Add Sales Officer</a></li>
              </ul>
            </li>
            <li class="dropdown">
              <a href="#" class="nav-link has-dropdown"><i class="fas fa-user-friends"></i><span>Admins</span></a>
              <ul class="dropdown-menu">
                <li><a class="nav-link" href="{{ action('AdminController@index') }}">List Admins</a></li>
                <li><a class="nav-link" href="{{ action('AdminController@create') }}">Add Admin</a></li>
              </ul>
            </li>
            @endrole

            @role('sales-officer')
            <li class="dropdown">
              <a href="#" class="nav-link has-dropdown"><i class="fas fa-route"></i><span>Beats</span></a>
              <ul class="dropdown-menu">
                <li><a class="nav-link" href="{{ action('RouteController@index') }}">List Beats</a></li>
                <li><a class="nav-link" href="{{ action('RouteController@create') }}">Add Beat</a></li>
              </ul>
            </li>
            <li class="dropdown">
              <a href="#" class="nav-link has-dropdown"><i class="fas fa-route"></i><span>My Beats</span></a>
              <ul class="dropdown-menu">
                <li><a class="nav-link" href="{{ action('MyRouteController@index') }}">List My Beats</a></li>
                <li><a class="nav-link" href="{{ action('MyRouteController@create') }}">Add My Beat</a></li>
              </ul>
            </li>
            <li class="dropdown">
              <a href="#" class="nav-link has-dropdown"><i class="fas fa-store-alt"></i><span>Customers</span></a>
              <ul class="dropdown-menu">
                <li><a class="nav-link" href="{{ action('CustomerController@index') }}">List Customers</a></li>
                <li><a class="nav-link" href="{{ action('CustomerController@index', [ 'mine' => 1 ]) }}">List My Customers</a></li>
                <li><a class="nav-link" href="{{ action('CustomerController@create') }}">Add Customer</a></li>
              </ul>
            </li>
            <li class="dropdown">
              <a href="#" class="nav-link has-dropdown"><i class="fas fa-users"></i><span>DSMs</span></a>
              <ul class="dropdown-menu">
                <li><a class="nav-link" href="{{ action('DsmController@index') }}">List DSMs</a></li>
                <li><a class="nav-link" href="{{ action('DsmController@index', [ 'mine' => 1 ]) }}">List My DSMs</a></li>
                <li><a class="nav-link" href="{{ action('DsmController@create') }}">Add DSM</a></li>
              </ul>
            </li>
            <li><a class="nav-link" href="{{ action('OrderController@index') }}">
                    <i class="fas fa-list"></i> <span>Orders</span></a>
            </li>
            @endrole
          </ul>
        </aside>
      </div>

      <!-- Main Content -->
      <div class="main-content">
        <section class="section">
          <div class="section-header">
            <h1>@yield('title')</h1>
          </div>

          <div class="section-body">
            @yield('content')
          </div>
        </section>
      </div>
      <footer class="main-footer">
        <div class="footer-left">
          Copyright &copy; 2019<!--  <div class="bullet"></div> Developed By <a href="http://iclicksms.com" target="_blank">I.CLICK</a> -->
        </div>
        <div class="footer-right">
          
        </div>
      </footer>
    </div>
  </div>

  <!-- General JS Scripts -->
  <script src="{{ asset('modules/jquery.min.js') }}"></script>
  <script src="{{ asset('modules/popper.js') }}"></script>
  <script src="{{ asset('modules/tooltip.js') }}"></script>
  <script src="{{ asset('modules/bootstrap/js/bootstrap.min.js') }}"></script>
  <script src="{{ asset('modules/nicescroll/jquery.nicescroll.min.js') }}"></script>
  <script src="{{ asset('modules/moment.min.js') }}"></script>
  <script src="{{ asset('js/stisla.js') }}"></script>
  
  <!-- JS Libraies -->
  <script src="{{ asset('modules/bootstrap-daterangepicker/daterangepicker.js') }}"></script>
  <script src="{{ asset('modules/chart.min.js') }}"></script>
  <script src="{{ asset('modules/datatables/datatables.min.js') }}"></script>
  <script src="{{ asset('modules/izitoast/js/iziToast.min.js') }}"></script>
  <script src="{{ asset('modules/select2/dist/js/select2.full.min.js') }}"></script>

  <!-- Page Specific JS File -->
  
  <!-- Template JS File -->
  <script src="{{ asset('js/scripts.js') }}"></script>
  <script src="{{ asset('js/custom.js') }}"></script>

  <script type="text/javascript">
    $(window).on('load', function() {
      @if(Session::has('success'))
      iziToast.success({
        message: '{{ Session::get('success') }}',
        position: 'topRight'
      });
      @endif

      @if(Session::has('error'))
      iziToast.error({
        message: '{{ Session::get('error') }}',
        position: 'topRight'
      });
      @endif
    });
  </script>

  @stack('scripts')
</body>
</html>
