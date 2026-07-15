<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        @include('admin.layouts.common.header_script')

        <style type="text/css">
            .card-header {
                background: #343b4a;
                color: #fff;
                padding-bottom: 10px;
                text-align: center;
                font-weight: 500;
            }
        </style>
    </head>

    <body class="authentication-bg">

        <div class="account-pages mt-5 mb-5">
            
            <!-- Start Content-->
            @yield('content')
            <!-- End Content-->

        </div>
        <!-- END wrapper -->


        @include('admin.layouts.common.footer_script')
    </body>
</html>