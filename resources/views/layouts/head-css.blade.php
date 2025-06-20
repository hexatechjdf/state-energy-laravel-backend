@yield('css')

<!-- Bootstrap Css -->
<link href="{{ URL::asset('/assets/css/bootstrap.min.css') }}" id="bootstrap-style" rel="stylesheet" type="text/css" />
<!-- Icons Css -->
<link href="{{ URL::asset('/assets/css/icons.min.css') }}" rel="stylesheet" type="text/css" />
<!-- App Css-->
<link href="{{ URL::asset('/assets/css/app.min.css') }}" id="app-style" rel="stylesheet" type="text/css" />
<link href="{{ URL::asset('/assets/libs/toastr/toastr.min.css') }}" rel="stylesheet" type="text/css" />
<link href="{{ URL::asset('/assets/libs/sweetalert2/sweetalert2.min.css') }}" rel="stylesheet" type="text/css" />
<style>
    .pagination.pagination-bordered li {
        display: -webkit-box;
        display: -ms-flexbox;
        display: flex;
    }

    .topnav .navbar-nav .dropdown-item.active,
    .topnav .navbar-nav .dropdown-item:hover,
    .topnav .navbar-nav .nav-item .nav-link.active {
        color: #002b5c;
    }

    .page-item.active .page-link {
        background-color: #002b5c;
        border-color: #002b5c
    }

    .text-primary {
        color: #002b5c !important;

    }

    .btn-primary {
        background-color: #002B5C !important;
        border-color: #002b5c !important;

    }
</style>
