<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8" />
    <title> @yield('title') | {{ env('APP_NAME', 'Laravel') }}</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta content="Premium Multipurpose Admin & Dashboard Template" name="description" />
    <meta content="Themesbrand" name="author" />
    <!-- App favicon -->
    <link rel="shortcut icon" href="{{ URL::asset('assets/images/favicon.ico') }}">
    @include('layouts.head-css')
</head>

@section('body')

    <body data-sidebar="dark">
    @show
    <!-- Begin page -->
    <div id="layout-wrapper">
        @include('layouts.topbar')
        @include('layouts.sidebar')
        <!-- ============================================================== -->
        <!-- Start right Content here -->
        <!-- ============================================================== -->
        <div class="main-content">
            <div class="page-content">
                <div class="container-fluid">
                    @yield('content')
                </div>
                <!-- container-fluid -->
            </div>
            <!-- End Page-content -->
            @include('layouts.footer')
        </div>
        <!-- end main content-->
    </div>
    <!-- END layout-wrapper -->

    <!-- Right Sidebar -->
    @include('layouts.right-sidebar')
    <!-- /Right-bar -->

    <!-- JAVASCRIPT -->
    @include('layouts.vendor-scripts')
    <script>
        function confirmDelete(id, location_id, url, table_name) {
            Swal.fire({
                title: 'Are you sure?',
                text: "You won't be able to revert this!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Yes, delete it!',
                cancelButtonText: 'No, cancel'
            }).then((result) => {
                if (result.isConfirmed) {
                    // Send DELETE request via AJAX
                    $.ajax({
                        url: url,
                        type: 'DELETE',
                        data: {
                            _token: '{{ csrf_token() }}', // CSRF token
                            id: id,
                            location_id: location_id // Pass user_id in the request payload
                        },
                        success: function(response) {
                            if (response.code === 200) {
                                // Success: Handle the successful delete response
                                Swal.fire(
                                    'Deleted!',
                                    'The order has been deleted.',
                                    'success'
                                ).then(() => {
                                    // $('#orderRow_' + id).remove();
                                    $('#' + table_name).DataTable().ajax.reload(null, false);
                                });
                            } else {
                                // Error handling if response code isn't 400
                                Swal.fire(
                                    'Error!',
                                    'There was an issue deleting the order.',
                                    'error'
                                );
                            }
                        },
                        error: function(xhr, status, error) {
                            // Handle AJAX error
                            Swal.fire(
                                'Error!',
                                'There was an issue with the server. Please try again later.',
                                'error'
                            );
                        }
                    });
                } else if (result.dismiss === Swal.DismissReason.cancel) {
                    // Handle cancel action
                    Swal.fire(
                        'Cancelled',
                        'The order has not been deleted.',
                        'info'
                    );
                }
            });
        }
    </script>
    <script>
        localStorage.setItem('spin_enabled', true);
    </script>
</body>

</html>
