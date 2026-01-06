<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="description" content="">
    <meta name="author" content="">

    <title>@yield('title', 'Quicktansi')</title>

    <!-- Custom fonts for this template-->
    <link href="{{ asset('admin/vendor/fontawesome-free/css/all.min.css') }}" rel="stylesheet" type="text/css">
    <link href="https://fonts.googleapis.com/css?family=Nunito:200,200i,300,300i,400,400i,600,600i,700,700i,800,800i,900,900i" rel="stylesheet">

    <!-- Custom styles for this template-->
    <link href="{{ asset('admin/css/sb-admin-2.min.css') }}" rel="stylesheet">

    <!-- Custom CSS for App -->
    <link href="{{ asset('css/kuitansi.css') }}" rel="stylesheet">

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
                    <h5 class="modal-title" id="exampleModalLabel">Ready to Leave?</h5>
                    <button class="close" type="button" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">×</span>
                    </button>
                </div>
                <div class="modal-body">Select "Logout" below if you are ready to end your current session.</div>
                <div class="modal-footer">
                    <button class="btn btn-secondary" type="button" data-dismiss="modal">Cancel</button>
                    <a class="btn btn-primary" href="/logout">Logout</a>
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
    
    <style>
        .dark-mode-switch {
            background: rgba(255, 255, 255, 0.9);
            padding: 10px 15px;
            border-radius: 30px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            transition: all 0.3s ease;
        }
        
        body.dark-mode .dark-mode-switch {
            background: rgba(52, 58, 64, 0.9);
        }
        
        .dark-mode-switch .custom-control-label {
            cursor: pointer;
            font-size: 16px;
            padding-left: 10px;
            margin-bottom: 0;
        }
        
        .dark-mode-switch .custom-control-label i {
            transition: all 0.3s ease;
        }
        
        .dark-mode-switch .custom-control-input:checked ~ .custom-control-label i {
            color: #ffc107;
        }
        
        .custom-switch .custom-control-label::before {
            background-color: #6c757d;
        }
        
        .custom-switch .custom-control-input:checked ~ .custom-control-label::before {
            background-color: #4e73df;
        }
    </style>

    <!-- Bootstrap core JavaScript-->
    <script src="{{ asset('admin/vendor/jquery/jquery.min.js') }}"></script>
    <script src="{{ asset('admin/vendor/bootstrap/js/bootstrap.bundle.min.js') }}"></script>

    <!-- Core plugin JavaScript-->
    <script src="{{ asset('admin/vendor/jquery-easing/jquery.easing.min.js') }}"></script>

    <!-- Custom scripts for all pages-->
    <script src="{{ asset('admin/js/sb-admin-2.min.js') }}"></script>

    <!-- DataTables JS -->
    <script src="https://cdn.datatables.net/1.13.4/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.4/js/dataTables.bootstrap4.min.js"></script>
    <!-- SweetAlert2 JS -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <!-- Custom JS for App -->
    <script src="{{ asset('js/kuitansi.js') }}"></script>

    <!-- Preview JS (only load on preview pages) -->
    @if(request()->routeIs('kuitansi.preview'))
        <script src="{{ asset('js/kuitansi-preview.js') }}"></script>
    @endif

    <script>
        // SweetAlert for success/error messages
        @if(session('success'))
            Swal.fire({
                icon: 'success',
                title: 'Berhasil!',
                text: '{{ session('success') }}',
                timer: 3000,
                showConfirmButton: false
            });
        @endif

        @if($errors->any())
            Swal.fire({
                icon: 'error',
                title: 'Error!',
                text: '{{ $errors->first() }}'
            });
        @endif
    </script>

    @stack('scripts')
</body>

</html>