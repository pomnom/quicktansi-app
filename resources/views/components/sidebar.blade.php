<!-- Sidebar -->
<ul class="navbar-nav bg-gradient-primary sidebar sidebar-dark accordion" id="accordionSidebar">

    <!-- Sidebar - Brand -->
    <a class="sidebar-brand d-flex align-items-center justify-content-center" href="{{ url('/') }}">
        <div class="sidebar-brand-logo">
            <img src="{{ asset('images/Logo-quicktansi.png') }}" alt="Quicktansi Logo" class="logo-responsive">
        </div>
        <div class="sidebar-brand-text mx-2">Quicktansi</div>
    </a>

    <!-- Divider -->
    <hr class="sidebar-divider my-0">

    <!-- Nav Item - Dashboard -->
    <li class="nav-item {{ request()->routeIs('dashboard') ? 'active' : '' }}">
        <a class="nav-link" href="/">
            <i class="fas fa-fw fa-tachometer-alt"></i>
            <span>Dashboard</span>
        </a>
    </li>

    <!-- Divider -->
    <hr class="sidebar-divider">

    <!-- Nav Item - Rekanan Collapse Menu -->
    <li class="nav-item {{ request()->routeIs('rekanan.*') || request()->routeIs('staff.*') ? 'active' : '' }}">
        <a class="nav-link collapsed" href="#" data-toggle="collapse" data-target="#collapseRekanan" aria-expanded="true" aria-controls="collapseRekanan">
            <i class="fas fa-fw fa-users"></i>
            <span>Rekanan</span>
        </a>
        <div id="collapseRekanan" class="collapse {{ request()->routeIs('rekanan.*') || request()->routeIs('staff.*') ? 'show' : '' }}" aria-labelledby="headingRekanan" data-parent="#accordionSidebar">
            <div class="bg-white py-2 collapse-inner rounded">
                <h6 class="collapse-header">Data Rekanan:</h6>
                <a class="collapse-item {{ request()->routeIs('rekanan.*') ? 'active' : '' }}" href="{{ route('rekanan.index') }}">
                    <i class="fas fa-handshake"></i> Rekanan
                </a>
                <a class="collapse-item {{ request()->routeIs('staff.*') ? 'active' : '' }}" href="{{ route('staff.index') }}">
                    <i class="fas fa-user-tie"></i> Staff
                </a>
            </div>
        </div>
    </li>

    <!-- Nav Item - Kuitansi -->
    <li class="nav-item {{ request()->routeIs('kuitansi.*') ? 'active' : '' }}">
        <a class="nav-link" href="{{ route('kuitansi.index') }}">
            <i class="fas fa-fw fa-receipt"></i>
            <span>Kuitansi</span>
        </a>
    </li>

    <!-- Divider -->
    <hr class="sidebar-divider d-none d-md-block">

    <!-- Sidebar Toggler (Sidebar) -->
    <div class="text-center d-none d-md-inline">
        <button class="rounded-circle border-0" id="sidebarToggle"></button>
    </div>

</ul>
<!-- End of Sidebar -->