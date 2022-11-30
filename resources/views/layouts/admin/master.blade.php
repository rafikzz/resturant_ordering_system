<!DOCTYPE html>
<!--
This is a starter template page. Use this page to start your new project from
scratch. This page gets rid of all links and provides the needed markup only.
-->
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Laravel') }}</title>

    <!-- Google Font: Source Sans Pro -->
    <link rel="stylesheet" href="{{ asset('css/fonts.googleapis.css') }}">
    <!-- Font Awesome Icons -->
    <link rel="stylesheet" href="{{ asset('admin-lte/plugins/fontawesome-free/css/all.min.css') }}">
    <!-- Theme style -->
    <link rel="stylesheet" href="{{ asset('admin-lte/dist/css/adminlte.min.css') }}">
    {{-- Select 2 --}}
    <link rel="stylesheet" href="{{ asset('admin-lte/plugins/select2/css/select2.min.css') }}">
    {{-- DateRangePicker --}}
    <link rel="stylesheet" href="{{ asset('admin-lte/plugins/daterangepicker/daterangepicker.css') }}">

    {{-- Datatables --}}
    <link href="{{ asset('css/jquery.dataTables.min.css') }}" rel="stylesheet">
    {{-- <link href="{{ asset('css/jquery.dataTable-bootstrap.min.css') }}" rel="stylesheet"> --}}

    {{-- ColorPicker --}}
    <link rel="stylesheet"
        href="{{ asset('admin-lte/plugins/bootstrap-colorpicker/css/bootstrap-colorpicker.min.css') }}">
    {{-- Owl Carousel --}}
    <link href="{{ asset('css/owl.carousel.css') }}" rel="stylesheet">
    <link href="{{ asset('css/owl.theme.default.min.css') }}" rel="stylesheet">


    <style>
        [class*="sidebar-dark-"] {
            background-color: #000;
        }


        .select2-container--default .select2-selection--multiple .select2-selection__choice {
            background-color: #3e4e4e;
            border-radius: 4px;
            cursor: default;
            float: left;
            margin-right: 5px;
            margin-top: 5px;
            padding: 0 5px;
        }
    </style>
    @yield('css')
    {{-- @if (Session::has('download.in.the.next.request'))
         <meta http-equiv="refresh" content="5;url={{ Session::get('download.in.the.next.request') }}">
      @endif --}}
</head>

<body class="hold-transition sidebar-mini">
    <div class="wrapper">

        <!-- Navbar -->
        <nav class="main-header navbar navbar-expand navbar-white navbar-light">
            @include('layouts.admin._topbar')
        </nav>
        <!-- /.navbar -->

        <!-- Main Sidebar Container -->
        <aside class="main-sidebar sidebar-dark-primary elevation-4">

            <!-- Sidebar -->
            <div class="sidebar">
                <!-- Sidebar user panel (optional) -->
                <div class="user-panel mt-3 pb-3 mb-3 d-flex">
                    <div class="image">
                    </div>
                    <div class="info">
                        <a href="{{ route('dashboard') }}" class="d-block">{{ config('app.name', 'Laravel') }}</a>
                    </div>
                </div>

                <!-- SidebarSearch Form -->
                <div class="form-inline">
                    <div class="input-group" data-widget="sidebar-search">
                        <input class="form-control form-control-sidebar" type="search" placeholder="Search"
                            aria-label="Search">
                        <div class="input-group-append">
                            <button class="btn btn-sidebar">
                                <i class="fas fa-search fa-fw"></i>
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Sidebar Menu -->
                <nav class="mt-2 text-sm">
                    <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu"
                        data-accordion="false">
                        @include('layouts.admin._sidebar-menu')
                    </ul>
                </nav>
                <!-- /.sidebar-menu -->
            </div>
            <!-- /.sidebar -->
        </aside>

        <!-- Content Wrapper. Contains page content -->
        <div class="content-wrapper">
            <!-- Content Header (Page header) -->
            <div class="content-header">
                <div class="container-fluid">
                    <div class="row mb-2">
                        <div class="col-sm-6">
                            @isset($title)
                                <h1 class="m-0">
                                    {{ $title }}
                                </h1>
                            @endisset

                        </div><!-- /.col -->


                        @include('layouts.admin._breadcrumb')
                    </div><!-- /.row -->
                </div><!-- /.container-fluid -->
            </div>

            <!-- /.content-header -->

            <!-- Main content -->
            <div class="content">
                <div class="container-fluid">
                    @if ($message = Session::get('success'))
                        <div class="alert alert-success alert-dismissible">
                            <a class="close" data-dismiss="alert" aria-hidden="true">x</a>
                            <p>{{ $message }}</p>
                        </div>
                    @endif
                    @if ($message = Session::get('error'))
                        <div class="alert alert-danger alert-dismissible">
                            <a class="close" data-dismiss="alert" aria-hidden="true">x</a>
                            <p>{{ $message }}</p>
                        </div>
                    @endif
                    @yield('content')
                </div>
            </div>
            <!-- /.content -->
        </div>
        <!-- /.content-wrapper -->



        <!-- Main Footer -->
        <footer class="main-footer">
            <!-- To the right -->
            {{-- <div class="float-right d-none d-sm-inline">
                Anything you want
            </div>
            <!-- Default to the left -->
            <strong>Copyright &copy; 2014-2021 <a href="https://adminlte.io">AdminLTE.io</a>.</strong> All rights
            reserved. --}}
        </footer>
    </div>
    <!-- ./wrapper -->

    <!-- REQUIRED SCRIPTS -->

    <!-- jQuery -->
    <script src="{{ asset('admin-lte/plugins/jquery/jquery.min.js') }}"></script>
    <!-- Bootstrap 4 -->
    <script src="{{ asset('admin-lte/plugins/bootstrap/js/bootstrap.bundle.min.js') }}"></script>
    <!-- AdminLTE App -->
    <script src="{{ asset('admin-lte/dist/js/adminlte.min.js') }}"></script>
    {{-- Select 2 --}}
    <script src="{{ asset('admin-lte/plugins/select2/js/select2.min.js') }}"></script>
    {{-- datatalbes --}}
    <script src="{{ asset('js/jquery.dataTables.min.js') }}"></script>
    {{-- <script src="{{ asset('js/jQuery.dataTable-bootstrap-4.js') }}"></script> --}}


    {{-- Jquery validation --}}
    <script src="{{ asset('js/jquery-validation-1.19.5/dist/jquery.validate.min.js') }}"></script>
    {{-- Moment Js --}}
    <script src="{{ asset('js/moment.min.js') }}"></script>

    {{-- DateRange Picker --}}
    <script src="{{ asset('admin-lte/plugins/daterangepicker/daterangepicker.js') }}"></script>

    {{-- jQuery Ui --}}
    <script src="{{ asset('js/jQuery-ui.js') }}"></script>
    {{-- Bs custom file Input --}}
    <script src="{{ asset('admin-lte/plugins/bs-custom-file-input/bs-custom-file-input.min.js') }}"></script>
    {{-- Owl Carousel --}}
    <script src="{{ asset('js/owl.carousel.js') }}"></script>
    {{-- Ck Editor --}}
    <script src="{{ asset('js/ckeditor/ckeditor.js') }}"></script>
    {{-- BsColorPicker --}}
    <script src="{{ asset('admin-lte/plugins/bootstrap-colorpicker/js/bootstrap-colorpicker.min.js') }}"></script>
    {{-- Sweet Alert --}}
    <script src="{{ asset('js/sweetAlert.js') }}"></script>

    {{-- Chart Js --}}
    <script src="{{ asset('js/chartJs.js') }}"></script>

    {{-- Print This Js v1.15 --}}
    <script src="{{ asset('js/printThis.js') }}"></script>
    {{-- <script>
        @if ($message = Session::get('success'))
            Swal.fire({
                icon: 'success',
                title: '{{ $message }}',
                showConfirmButton: false,
                timer: 1500
            });
        @endif
        @if ($message = Session::get('error'))
            Swal.fire([
                icon: 'error',
                title:' {{ $message }}',
                showConfirmButton: false,
                timer: 1500
            ])
        @endif
    </script> --}}
    <script>
        $(document).ready(function() {
            $('.select2').select2();
        });
    </script>


    @yield('js')

</body>

</html>
