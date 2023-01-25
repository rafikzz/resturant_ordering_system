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

    {{-- Datatables --}}
    <link href="{{ asset('css/jquery.dataTables.min.css') }}" rel="stylesheet">

    {{-- Owl Carousel --}}
    <link rel="stylesheet" href="{{ asset('assets/plugins/owlcarousel/owl.carousel.min.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/plugins/owlcarousel/owl.theme.default.min.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/style.css') }}">
    <link href="{{ asset('css/food-item-card.css') }}" rel="stylesheet">


    <style>
        .is-hidden {
            display: none;
        }

        .btn-pos ul li button.btn {
            color: #888B94;
            font-size: 14px;
            width: 100%;
            padding: 5px px 12px;
            background: #E9ECEF;
            border-radius: 50px;
            border: 1px solid #E9ECEF;
            padding: 5px 12px;
        }
        button .btn-totallabel {
            background: #8377ff;
         }
        .btn-pos ul li button.btn:hover {
            border: 1px solid #EA5455;
            color: #EA5455;
            background: rgba(234, 84, 85, 0.06);
        }

        @media (max-width: 575px) {
            .col {
                padding: 0px 4px;
            }


        }
    </style>
    @yield('css')
    {{-- @if (Session::has('download.in.the.next.request'))
         <meta http-equiv="refresh" content="5;url={{ Session::get('download.in.the.next.request') }}">
      @endif --}}
</head>

<body class="layout-top-nav" style="height:auto">
    <div id="global-loader">
        <div class="whirly-loader"> </div>
    </div>
    <div class="wrapper">

        <!-- Navbar -->
        <nav class="main-header navbar navbar-expand-md navbar-light " style="background-color: #ff9f43;">
            @include('layouts.admin.orders._topbar')
        </nav>
        <!-- /.navbar -->



        <!-- Content Wrapper. Contains page content -->
        <div class="content-wrapper containter">
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

                        </div>


                        @include('layouts.admin._breadcrumb')
                    </div>
                </div>

                <!-- /.content-header -->

                <!-- Main content -->
                <div class="content">
                    <div class="container-fluid">
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
        <script src="{{ asset('assets/js/bootstrap.bundle.min.js') }}"></script>

        <script src="{{ asset('admin-lte/plugins/bootstrap/js/bootstrap.bundle.min.js') }}"></script>
        <!-- AdminLTE App -->
        <script src="{{ asset('admin-lte/dist/js/adminlte.min.js') }}"></script>
        {{-- Select 2 --}}
        <script src="{{ asset('admin-lte/plugins/select2/js/select2.min.js') }}"></script>
        {{-- datatalbes --}}
        <script src="{{ asset('js/jquery.dataTables.min.js') }}"></script>
        {{-- Jquery validation --}}
        <script src="{{ asset('js/jquery-validation-1.19.5/dist/jquery.validate.min.js') }}"></script>

        {{-- Owl Carousel --}}
        <script src="{{ asset('js/owl.carousel.js') }}"></script>


        {{-- Sweet Alert --}}
        <script src="{{ asset('js/sweetAlert.js') }}"></script>


        {{-- Print This Js v1.15 --}}
        <script src="{{ asset('js/printThis.js') }}"></script>

        <script src="{{ asset('assets/js/feather.min.js') }}"></script>

        <script src="{{ asset('assets/js/jquery.slimscroll.min.js') }}"></script>


        <script src="{{ asset('assets/plugins/owlcarousel/owl.carousel.min.js') }}"></script>

        <script src="{{ asset('assets/plugins/sweetalert/sweetalert2.all.min.js') }}"></script>
        <script src="{{ asset('assets/plugins/sweetalert/sweetalerts.min.js') }}"></script>

        <script src="{{ asset('assets/js/script.js') }}"></script>
        <script>
            jQuery.ajaxSetup({
                beforeSend: function() {
                    $('#overlay').show()
                },
                complete: function() {
                    $('#overlay').hide()
                },
                success: function() {
                    $('#overlay').hide()
                }
            });
        </script>
        <script>
            @if (session('success'))

                const Toast = Swal.mixin({
                    toast: true,
                    position: 'top-end',
                    showConfirmButton: false,
                    timer: 3000,
                    timerProgressBar: true,
                    didOpen: (toast) => {
                        toast.addEventListener('mouseenter', Swal.stopTimer)
                        toast.addEventListener('mouseleave', Swal.resumeTimer)
                    }
                });

                Toast.fire({
                    icon: 'success',
                    title: '{{ session('success') }}'
                });
            @endif

            @if (session('error'))

                const Toast = Swal.mixin({
                    toast: true,
                    position: 'top-end',
                    showConfirmButton: false,
                    timer: 3000,
                    timerProgressBar: true,
                    didOpen: (toast) => {
                        toast.addEventListener('mouseenter', Swal.stopTimer)
                        toast.addEventListener('mouseleave', Swal.resumeTimer)
                    }
                });

                Toast.fire({
                    icon: 'error',
                    title: '{{ session('error') }}'
                });
            @endif
        </script>


        @yield('js')

</body>

</html>
