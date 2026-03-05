<!-- Sidebar -->
<ul class="navbar-nav bg-gradient-primary sidebar sidebar-dark accordion" id="accordionSidebar">

    <!-- Sidebar - Brand -->
    <a class="sidebar-brand d-flex align-items-center justify-content-center" href="{{ url('/') }}">
        <div class="sidebar-brand-icon rotate-n-15">
            <i class="fas fa-laugh-wink"></i>
        </div>
        <div class="sidebar-brand-text mx-3">Quicktansi</div>
    </a>

    <!-- Divider -->
    <hr class="sidebar-divider my-0">

    <!-- Nav Item - Dashboard -->
    <li class="nav-item {{ request()->routeIs('dashboard') ? 'active' : '' }}">
        <a class="nav-link" href="{{ route('dashboard') }}">
            <i class="fas fa-fw fa-tachometer-alt"></i>
            <span>Dashboard</span></a>
    </li>

    <!-- Divider -->
    <hr class="sidebar-divider">

    <!-- Heading - APLIKASI -->
    <div class="sidebar-heading">
        APLIKASI
    </div>

    <!-- Nav Item - Kuitansi -->
    <li class="nav-item {{ request()->routeIs('kuitansi.*') ? 'active' : '' }}">
        <a class="nav-link" href="{{ route('kuitansi.index') }}">
            <i class="fas fa-fw fa-receipt"></i>
            <span>Kuitansi</span></a>
    </li>

    <!-- Divider -->
    <hr class="sidebar-divider">

    <!-- Heading - DATA MASTER -->
    <div class="sidebar-heading">
        DATA MASTER
    </div>

    <!-- Nav Item - Rekanan -->
    <li class="nav-item {{ request()->routeIs('rekanan.*') ? 'active' : '' }}">
        <a class="nav-link" href="{{ route('rekanan.index') }}">
            <i class="fas fa-fw fa-handshake"></i>
            <span>Rekanan</span></a>
    </li>

    <!-- Nav Item - Staff -->
    <li class="nav-item {{ request()->routeIs('staff.*') ? 'active' : '' }}">
        <a class="nav-link" href="{{ route('staff.index') }}">
            <i class="fas fa-fw fa-user-tie"></i>
            <span>Staff</span></a>
    </li>

    <!-- Divider -->
    <hr class="sidebar-divider">

    <!-- Heading - PENGATURAN -->
    <div class="sidebar-heading">
        PENGATURAN
    </div>

    <!-- Nav Item - User Management -->
    <li class="nav-item {{ request()->routeIs('user.*') ? 'active' : '' }}">
        <a class="nav-link" href="{{ route('user.index') }}">
            <i class="fas fa-fw fa-users-cog"></i>
            <span>Manajemen User</span></a>
    </li>

    <!-- Divider -->
    <hr class="sidebar-divider d-none d-md-block">

    <!-- Sidebar Toggler (Sidebar) -->
    <div class="text-center d-none d-md-inline">
        <button class="rounded-circle border-0" id="sidebarToggle"></button>
    </div>

</ul>
<!-- End of Sidebar -->