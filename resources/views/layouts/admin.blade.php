<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="{{ asset('css/app.css') }}">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/5.3.0/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/2.1.7/css/dataTables.bootstrap5.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/css/select2.min.css"/>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet">


    <!-- <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/admin-lte@3.2/dist/css/adminlte.min.css"> -->
</head>
<body>
    <div class="container-fluid">
        <div class="row">
            @include('partials.navbar')
            <nav class="col-md-2 d-flex flex-column flex-shrink-0 p-3 bg-light ">
                <div class="sidebar-sticky">
                    <ul class="nav nav-pills flex-column mb-auto">

                        @if(hasPermission('Dashboard'))
                            <li class="nav-item">
                                <a class="nav-link d-flex align-items-center text-black {{ request()->routeIs('admin.dashboard*') ? 'active' : '' }}" 
                                href="{{ route('admin.dashboard') }}">
                                    <img src="{{ asset('assets/dashboard.svg') }}" alt="Dashboard" style="width: 20px; height: 20px; margin-right: 5px;">
                                    Dashboard
                                </a>
                            </li>
                        @endif

                        @if(hasPermission('Categories'))
                            <li class="nav-item">
                                <a class="nav-link d-flex align-items-center text-black {{ request()->routeIs('categories*') ? 'active' : '' }}" 
                                href="{{ route('categories.index') }}">
                                    <img src="{{ asset('assets/category.svg') }}" alt="Categories" style="width: 20px; height: 20px; margin-right: 5px;">
                                    {{__('sidebar.categories')}}
                                </a>
                            </li>
                        @endif

                        @if(hasPermission('Product'))
                            <li class="nav-item">
                                <a class="nav-link d-flex align-items-center text-black {{ request()->routeIs('products*') ? 'active' : '' }}" 
                                href="{{ route('products.index') }}">
                                    <img src="{{ asset('assets/product.svg') }}" alt="Products" style="width: 20px; height: 20px; margin-right: 5px;">
                                    {{__('sidebar.products')}}
                                </a>
                            </li>
                        @endif

                        @if(hasPermission('Users'))
                            <li class="nav-item">
                                <a class="nav-link d-flex align-items-center text-black {{ request()->routeIs('users*') ? 'active' : '' }}" 
                                href="{{ route('users.index') }}">
                                    <img src="{{ asset('assets/user.svg') }}" alt="Users" style="width: 20px; height: 20px; margin-right: 5px;">
                                    {{__('sidebar.users')}}
                                </a>
                            </li>
                        @endif

                        @if(hasPermission('Admins'))
                            <li class="nav-item">
                                <a class="nav-link d-flex align-items-center text-black {{ request()->routeIs('admins*') ? 'active' : '' }}" 
                                href="{{ route('admins.index') }}">
                                    <img src="{{ asset('assets/admin.svg') }}" alt="Admins" style="width: 19px; height: 19px; margin-right: 6px;">
                                    {{__('sidebar.admins')}}
                                </a>
                            </li>
                        @endif

                        @if(hasPermission('Orders'))
                            <li class="nav-item">
                                <a class="nav-link d-flex align-items-center text-black {{ request()->routeIs('orders*') ? 'active' : '' }}" 
                                href="{{ route('orders.index') }}">
                                    <img src="{{ asset('assets/order.svg') }}" alt="Orders" style="width: 20px; height: 20px; margin-right: 5px;">
                                    {{__('sidebar.orders')}}
                                </a>
                            </li>
                        @endif

                        @if(hasPermission('Setting'))
                            <li class="nav-item">
                                <a class="nav-link d-flex align-items-center text-black {{ request()->routeIs('settings*') ? 'active' : '' }}" 
                                href="{{ route('settings.index') }}">
                                    <img src="{{ asset('assets/gear.svg') }}" alt="Settings" style="width: 20px; height: 20px; margin-right: 5px;">
                                    {{__('sidebar.settings')}}
                                </a>
                            </li>
                        @endif

                        @if(hasPermission('Role'))
                            <li class="nav-item">
                                <a class="nav-link d-flex align-items-center text-black {{ request()->routeIs('roles*') ? 'active' : '' }}" 
                                href="{{ route('roles.index') }}">
                                    <i class="fa-solid fa-user-shield" style="width: 20px; margin-right: 5px;"></i>
                                    {{ __('sidebar.roles_permissions') }}
                                </a>
                            </li>
                        @endif

                        <li class="nav-item">
                            <a class="nav-link d-flex align-items-center text-black" href="#"
                            onclick="event.preventDefault(); if(confirm('Apakah anda yakin ingin logout?')) { document.getElementById('logout-form').submit(); }">
                                <img src="{{ asset('assets/logout.svg') }}" alt="Logout" style="width: 20px; height: 20px; margin-right: 5px;">
                                {{__('sidebar.logout')}}
                            </a>
                        </li>
                    </ul>
                </div>
            </nav>
            <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
                @csrf
            </form>
            <main role="main" class="col-md-9 ml-sm-auto col-lg-10 px-4">
                @yield('content')
            </main>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.7.1.js" integrity="sha256-eKhayi8LEQwp4NKxN+CfCh+3qOVUtJn3QNZ0TciWLP4=" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.0.7/dist/umd/popper.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/5.3.0/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.datatables.net/2.1.7/js/dataTables.js"></script>
    <script src="https://cdn.datatables.net/2.1.7/js/dataTables.bootstrap5.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/js/select2.min.js"></script>
    <!-- <script src="https://cdn.jsdelivr.net/npm/admin-lte@3.2/dist/js/adminlte.min.js"></script> -->
    
    <script>
        var locale = "{{ App::getLocale() }}";
        var languageUrl = locale === 'id' 
        ? "{{asset('assets/indonesia.json')}}" 
        : "{{asset('assets/english.json')}}" ;

        document.getElementById('notification-btn').addEventListener('click', function () {
            let dropdown = document.getElementById('notifications-dropdown');
            if (dropdown.classList.contains('hidden')) {
                dropdown.classList.remove('hidden');
            } else {
                dropdown.classList.add('hidden');
            }
        });

        </script>
        @stack('scripts')
</body>
</html>
