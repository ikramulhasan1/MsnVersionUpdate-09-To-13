@extends('admin.layouts.master')
@section('title', $title)
@section('content')

<!-- Start Content-->
<div class="container-fluid">
    
    <!-- start page title -->
    <!-- Include page breadcrumb -->
    @include('admin.inc.breadcrumb')  
    <!-- end page title --> 

    <div class="row">
        <div class="col-xl-3 col-lg-6">
            <div class="card widget-flat">
                <div class="card-body p-0">
                    <div class="p-3 pb-0">
                        <div class="float-right">
                            <span class="icon text-primary widget-icon"><i class="fas fa-newspaper"></i></span>
                        </div>
                        <h5 class="text-muted font-weight-normal mt-0">{{ __('dashboard.total_blogs') }}</h5>
                        <h3 class="mt-2">{{ $articles }}</h3>
                    </div>
                </div> <!-- end card-body-->
            </div> <!-- end card-->
        </div> <!-- end col-->

        <div class="col-xl-3 col-lg-6">
            <div class="card widget-flat">
                <div class="card-body p-0">
                    <div class="p-3 pb-0">
                        <div class="float-right">
                            <span class="icon text-danger widget-icon"><i class="far fa-images"></i></span>
                        </div>
                        <h5 class="text-muted font-weight-normal mt-0">{{ __('dashboard.total_portfolios') }}</h5>
                        <h3 class="mt-2">{{ $portfolios }}</h3>
                    </div>
                </div> <!-- end card-body-->
            </div> <!-- end card-->
        </div> <!-- end col-->

        <div class="col-xl-3 col-lg-6">
            <div class="card widget-flat">
                <div class="card-body p-0">
                    <div class="p-3 pb-0">
                        <div class="float-right">
                            <span class="icon text-primary widget-icon"><i class="fas fa-tools"></i></span>
                        </div>
                        <h5 class="text-muted font-weight-normal mt-0">{{ __('dashboard.total_services') }}</h5>
                        <h3 class="mt-2">{{ $services }}</h3>
                    </div>
                </div> <!-- end card-body-->
            </div> <!-- end card-->
        </div> <!-- end col-->

        <div class="col-xl-3 col-lg-6">
            <div class="card widget-flat">
                <div class="card-body p-0">
                    <div class="p-3 pb-0">
                        <div class="float-right">
                            <span class="icon text-danger widget-icon"><i class="fas fa-question-circle"></i></span>
                        </div>
                        <h5 class="text-muted font-weight-normal mt-0">{{ __('dashboard.total_faqs') }}</h5>
                        <h3 class="mt-2">{{ $faqs }}</h3>
                    </div>
                </div> <!-- end card-body-->
            </div> <!-- end card-->
        </div> <!-- end col-->

        <div class="col-xl-3 col-lg-6">
            <div class="card widget-flat">
                <div class="card-body p-0">
                    <div class="p-3 pb-0">
                        <div class="float-right">
                            <span class="icon text-primary widget-icon"><i class="fas fa-users"></i></span>
                        </div>
                        <h5 class="text-muted font-weight-normal mt-0">{{ __('dashboard.total_members') }}</h5>
                        <h3 class="mt-2">{{ $members }}</h3>
                    </div>
                </div> <!-- end card-body-->
            </div> <!-- end card-->
        </div> <!-- end col-->

        <div class="col-xl-3 col-lg-6">
            <div class="card widget-flat">
                <div class="card-body p-0">
                    <div class="p-3 pb-0">
                        <div class="float-right">
                            <span class="icon text-danger widget-icon"><i class="fas fa-mug-hot"></i></span>
                        </div>
                        <h5 class="text-muted font-weight-normal mt-0">{{ __('dashboard.total_partners') }}</h5>
                        <h3 class="mt-2">{{ $clients }}</h3>
                    </div>
                </div> <!-- end card-body-->
            </div> <!-- end card-->
        </div> <!-- end col-->

        <div class="col-xl-3 col-lg-6">
            <div class="card widget-flat">
                <div class="card-body p-0">
                    <div class="p-3 pb-0">
                        <div class="float-right">
                            <span class="icon text-primary widget-icon"><i class="fas fa-envelope-open-text"></i></span>
                        </div>
                        <h5 class="text-muted font-weight-normal mt-0">{{ __('dashboard.total_emails') }}</h5>
                        <h3 class="mt-2">{{ $contacts }}</h3>
                    </div>
                </div> <!-- end card-body-->
            </div> <!-- end card-->
        </div> <!-- end col-->

        <div class="col-xl-3 col-lg-6">
            <div class="card widget-flat">
                <div class="card-body p-0">
                    <div class="p-3 pb-0">
                        <div class="float-right">
                            <span class="icon text-danger widget-icon"><i class="fas fa-mail-bulk"></i></span>
                        </div>
                        <h5 class="text-muted font-weight-normal mt-0">{{ __('dashboard.total_subscribers') }}</h5>
                        <h3 class="mt-2">{{ $subscribers }}</h3>
                    </div>
                </div> <!-- end card-body-->
            </div> <!-- end card-->
        </div> <!-- end col-->
    </div>
    <!-- end row -->

</div> <!-- container -->
<!-- End Content-->

@endsection