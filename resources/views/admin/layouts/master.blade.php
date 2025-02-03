<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        @include('admin.layouts.common.header_script')
    </head>

    <body>

        <!-- Begin page -->
        <div id="wrapper">

            
            <!-- ========== Left Sidebar Start ========== -->
            <div class="left-side-menu left-side-menu-dark">

                <div class="slimscroll-menu">

                    @if(isset($setting))
                    <!-- LOGO -->
                    <a href="{{ route('admin.dashboard.index') }}" class="logo text-center mb-4">
                        <span class="logo-lg">
                            <img src="{{ asset('/uploads/setting/'.$setting->logo_path) }}" alt="logo" height="40">
                        </span>
                        <span class="logo-sm">
                            <img src="{{ asset('/uploads/setting/'.$setting->logo_path) }}" alt="logo" height="40">
                        </span>
                    </a>
                    @endif

                    @if(Request::is('admin*'))
                    <!--- Sidemenu -->
                    @include('admin.inc.sidebar')
                    <!-- End Sidebar -->
                    @endif

                    <div class="clearfix"></div>

                </div>
                <!-- Sidebar -left -->

            </div>
            <!-- Left Sidebar End -->


            <!-- ============================================================== -->
            <!-- Start Page Content here -->
            <!-- ============================================================== -->

            <div class="content-page">
                <div class="content">

                    <!-- Topbar Start -->
                    <div class="navbar-custom">
                        <ul class="list-unstyled topbar-right-menu float-right mb-0">

                            <!-- Authentication Links -->
                            @guest
                                <li class="nav-item">
                                    <a class="nav-link" href="{{ route('login') }}">{{ __('auth.login') }}</a>
                                </li>
                                @if (Route::has('register'))
                                    <li class="nav-item">
                                        <a class="nav-link" href="{{ route('register') }}">{{ __('auth.register') }}</a>
                                    </li>
                                @endif
                            @else

                            <li class="dropdown notification-list">
                                <a class="nav-link dropdown-toggle nav-user mr-0" data-toggle="dropdown" href="#" role="button" aria-haspopup="false" aria-expanded="false">
                                    <img src="{{ asset('/dashboard/images/users/user.png') }}" alt="user-image" class="rounded-circle">
                                </a>
                                <div class="dropdown-menu dropdown-menu-right dropdown-menu-animated profile-dropdown ">
                                    <!-- item-->
                                    <div class="dropdown-header noti-title">
                                        <h6 class="text-overflow m-0">{{ __('dashboard.welcome') }}
                                            <small class="pro-user-name ml-1">
                                                {{ Auth::user()->name }}
                                            </small>
                                        </h6>
                                    </div>

                                    <!-- item-->
                                    <!-- <a href="javascript:void(0);" class="dropdown-item notify-item">
                                        <i class="fe-user"></i>
                                        <span>My Account</span>
                                    </a> -->

                                    <!-- item-->
                                    <a href="{{ route('admin.setting.index') }}" class="dropdown-item notify-item">
                                        <i class="fe-settings"></i>
                                        <span>{{ trans_choice('dashboard.setting', 2) }}</span>
                                    </a>

                                    <div class="dropdown-divider"></div>

                                    <!-- item-->
                                    <a href="javascript:void(0);" class="dropdown-item" href="{{ route('logout') }}"
                                       onclick="event.preventDefault();
                                                     document.getElementById('logout-form').submit();">
                                        
                                        <i class="fe-log-out"></i>
                                        <span>{{ __('dashboard.logout') }}</span>
                                    </a>

                                    <form id="logout-form" action="{{ route('logout') }}" method="POST">
                                        @csrf
                                    </form>

                                </div>
                            </li>

                            @endguest

                        </ul>
                        <button class="button-menu-mobile open-left disable-btn">
                            <i class="fe-menu"></i>
                        </button>
                        <div class="app-search">
                        </div>
                    </div>
                    <!-- end Topbar -->


                    <!-- Start Content-->
                    @yield('content')
                    <!-- End Content-->
                    


                </div> <!-- content -->

                <!-- Footer Start -->
                <footer class="footer">
                    <div class="container-fluid">
                        <div class="row">
                            <div class="col-md-6">
                                @if(isset($setting))
                                {{ __('dashboard.admin') }} &copy; - {!! strip_tags($setting->footer_text, '<p><a><b><i><u><strong>') !!}
                                @endif
                            </div>
                            <div class="col-md-6">
                                <div class="text-md-right footer-links d-none d-sm-block">
                                    <a href="{{ route('home') }}">{{ __('dashboard.home') }}</a>
                                </div>
                            </div>
                        </div>
                    </div>
                </footer>
                <!-- end Footer -->

            </div>

            <!-- ============================================================== -->
            <!-- End Page content -->
            <!-- ============================================================== -->


        </div>
        <!-- END wrapper -->


        @include('admin.layouts.common.footer_script')
    </body>
</html>