<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="description" content="">
    <meta name="author" content="">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>@yield('title', 'Quicktansi')</title>

    <!-- Custom fonts for this template-->
    <link href="{{ asset('admin/vendor/fontawesome-free/css/all.min.css') }}" rel="stylesheet" type="text/css">
    <link href="https://fonts.googleapis.com/css?family=Nunito:200,200i,300,300i,400,400i,600,600i,700,700i,800,800i,900,900i" rel="stylesheet">

    <!-- Custom styles for this template-->
    <link href="{{ asset('admin/css/sb-admin-2.min.css') }}" rel="stylesheet">

    <!-- DataTables Bootstrap 5 CSS -->
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.4/css/dataTables.bootstrap5.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/responsive/2.5.0/css/responsive.bootstrap5.min.css">
    
    <!-- Select2 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <link href="https://cdn.jsdelivr.net/npm/select2-bootstrap-5-theme@1.3.0/dist/css/select2-bootstrap-5-theme.min.css" rel="stylesheet" />

    <!-- Custom CSS for App -->
    <link href="{{ asset('css/kuitansi.css') }}" rel="stylesheet">
    <link href="{{ asset('css/dashboard-custom.css') }}" rel="stylesheet">

    <!-- Preview CSS (only load on preview pages) -->
    @if(request()->routeIs('kuitansi.preview'))
        <link href="{{ asset('css/kuitansi-preview.css') }}" rel="stylesheet">
    @endif

    @stack('styles')
</head>

<body id="page-top">

    <!-- Page Wrapper -->
    <div id="wrapper">

        <!-- Sidebar -->
        @include('components.sidebar')

        <!-- Content Wrapper -->
        <div id="content-wrapper" class="d-flex flex-column">

            <!-- Main Content -->
            <div id="content">

                <!-- Topbar -->
                @include('components.header')

                <!-- Begin Page Content -->
                <div class="container-fluid">
                    @yield('content')
                </div>
                <!-- /.container-fluid -->

            </div>
            <!-- End of Main Content -->

            <!-- Footer -->
            @include('components.footer')

        </div>
        <!-- End of Content Wrapper -->

    </div>
    <!-- End of Page Wrapper -->

    <!-- Scroll to Top Button-->
    <a class="scroll-to-top rounded" href="#page-top">
        <i class="fas fa-angle-up"></i>
    </a>

    <!-- Logout Modal-->
    <div class="modal fade" id="logoutModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Mau Keluar?</h5>
                    <button class="close" type="button" data-dismiss="modal" aria-label="Tutup">
                        <span aria-hidden="true">×</span>
                    </button>
                </div>
                <div class="modal-body">Klik "Keluar" di bawah jika Anda siap mengakhiri sesi ini.</div>
                <div class="modal-footer">
                    <button class="btn btn-secondary" type="button" data-dismiss="modal">Batal</button>
                    <form action="{{ route('logout') }}" method="POST" style="display:inline;">
                        @csrf
                        <button type="submit" class="btn btn-primary">Keluar</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Dark Mode Toggle Switch -->
    <div class="dark-mode-switch position-fixed" style="bottom: 20px; left: 20px; z-index: 1050;">
        <div class="custom-control custom-switch">
            <input type="checkbox" class="custom-control-input" id="darkModeToggle">
            <label class="custom-control-label" for="darkModeToggle">
                <i class="fas fa-moon"></i>
            </label>
        </div>
    </div>
    
    <!-- Bootstrap core JavaScript-->
    <script src="{{ asset('admin/vendor/jquery/jquery.min.js') }}"></script>
    <script src="{{ asset('admin/vendor/bootstrap/js/bootstrap.bundle.min.js') }}"></script>

    <!-- Core plugin JavaScript-->
    <script src="{{ asset('admin/vendor/jquery-easing/jquery.easing.min.js') }}"></script>

    <!-- Custom scripts for all pages-->
    <script src="{{ asset('admin/js/sb-admin-2.min.js') }}"></script>

    <!-- DataTables JS -->
    <script src="https://cdn.datatables.net/1.13.4/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.4/js/dataTables.bootstrap5.min.js"></script>
    <script src="https://cdn.datatables.net/responsive/2.5.0/js/dataTables.responsive.min.js"></script>
    <script src="https://cdn.datatables.net/responsive/2.5.0/js/responsive.bootstrap5.min.js"></script>
    
    <!-- Select2 JS -->
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    
    <!-- SweetAlert2 JS -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <!-- Custom JS for App -->
    <script src="{{ asset('js/kuitansi.js') }}"></script>

    <!-- Preview JS (only load on preview pages) -->
    @if(request()->routeIs('kuitansi.preview'))
        <script src="{{ asset('js/kuitansi-preview.js') }}"></script>
    @endif

    <script>
        // Auto-collapse sidebar on mobile
        $(document).ready(function() {
            function handleSidebarResponsive() {
                if ($(window).width() < 768) {
                    // On mobile, toggle sidebar to collapsed by default
                    $('body').addClass('sidebar-toggled');
                    $('.sidebar').addClass('toggled');
                } else {
                    // On desktop, expand sidebar
                    $('body').removeClass('sidebar-toggled');
                    $('.sidebar').removeClass('toggled');
                }
            }
            
            // Run on page load
            handleSidebarResponsive();
            
            // Run on window resize
            $(window).on('resize', function() {
                handleSidebarResponsive();
            });
            
            // Close sidebar when clicking outside on mobile
            if ($(window).width() < 768) {
                $(document).on('click', function(e) {
                    if (!$(e.target).closest('.sidebar, #sidebarToggle, #sidebarToggleTop').length) {
                        if (!$('.sidebar').hasClass('toggled')) {
                            $('body').addClass('sidebar-toggled');
                            $('.sidebar').addClass('toggled');
                        }
                    }
                });
            }
        });
        
        // SweetAlert for success/error messages
        @if(session('success'))
            Swal.fire({
                icon: 'success',
                title: 'Berhasil!',
                text: @json(session('success')),
                timer: 3000,
                showConfirmButton: false
            });
        @endif

        @if($errors->any())
            Swal.fire({
                icon: 'error',
                title: 'Error!',
                text: @json($errors->first())
            });
        @endif
    </script>

    @stack('scripts')
</body>

</html>