        <meta charset="utf-8" />

        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <meta content="Coderthemes" name="author" />
        <meta http-equiv="X-UA-Compatible" content="IE=edge" />

        @if(isset($setting))
        <!-- App Title -->
        <title>@yield('title') | {{ $setting->title }}</title>

        <!-- App favicon -->
        <link rel="shortcut icon" href="{{ asset('/uploads/setting/'.$setting->favicon_path) }}" type="image/x-icon">
        @endif

        @if(empty($setting))
        <!-- App Title -->
        <title>@yield('title')</title>
        @endif

        <!-- App css -->
        <link href="{{ asset('dashboard/css/bootstrap.min.css') }}" rel="stylesheet" type="text/css" />
        <link href="{{ asset('dashboard/css/icons.min.css') }}" rel="stylesheet" type="text/css" />
        <link href="{{ asset('dashboard/css/summernote-bs4.css') }}" rel="stylesheet" type="text/css" />


        <!-- third party css -->
        <link href="{{ asset('dashboard/css/vendor/dataTables.bootstrap4.css') }}" rel="stylesheet" type="text/css" />
        <link href="{{ asset('dashboard/css/vendor/switchery.min.css') }}" rel="stylesheet" type="text/css" />
        <link href="{{ asset('dashboard/css/vendor/toastr.min.css') }}" rel="stylesheet" type="text/css" />
        <!-- third party css end -->


        <link href="{{ asset('dashboard/css/app.css') }}" rel="stylesheet" type="text/css" />

        <!-- page css -->
        @yield('page_css')