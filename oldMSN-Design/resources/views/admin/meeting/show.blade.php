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
        <div class="col-12">
            <a href="{{ route($route.'.index') }}" class="btn btn-info">{{ __('dashboard.back') }}</a>
        </div>
    </div>

    <div class="row">
        <div class="col-12 col-lg-8">
            <div class="card">
                <div class="card-header">
                    <h4 class="header-title">{{ $title }} #{{ $row->id }}</h4>
                </div>
                <div class="card-body">

                    <!-- Details View Start -->
                    <div class="table-responsive">
                    <table class="table table-striped table-hover">
                        <tr>
                            <td>{{ __('dashboard.name') }}</td>
                            <td>: {{ $row->name }}</td>
                        </tr>
                        <tr>
                            <td>{{ __('dashboard.email') }}</td>
                            <td>: {{ $row->email }}</td>
                        </tr>
                       
                        <tr>
                            <td>{{ __('dashboard.phone') }}</td>
                            <td>: {{ $row->phone }}</td>
                        </tr>
                        <tr>
                            <td>{{ __('dashboard.date') }}</td>
                            <td>: {{ $row->date }}</td>
                        </tr>
                        <tr>
                            <td>{{ __('dashboard.meeting_time') }}</td>
                            <td>: {{ $row->meeting_time }}</td>
                        </tr>
                        <tr>
                            <td>{{ __('dashboard.location') }}</td>
                            <td>: {{ $row->location }}</td>
                        </tr>
                        
                        @if(isset($row->city))
                        <tr>
                            <td>{{ __('dashboard.city') }}</td>
                            <td>: {{ $row->city }}</td>
                        </tr>
                        @endif
                        @if(isset($row->ip))
                        <tr>
                            <td>{{ __('dashboard.ip') }}</td>
                            <td>: {{ $row->ip }}</td>
                        </tr>
                        @endif
                        @if(isset($row->latitude))
                        <tr>
                            <td>{{ __('dashboard.latitude') }}</td>
                            <td>: {{ $row->latitude }}</td>
                        </tr>
                        @endif
                        @if(isset($row->longitude))
                        <tr>
                            <td>{{ __('dashboard.longitude') }}</td>
                            <td>: {{ $row->longitude }}</td>
                        </tr>
                        @endif
                        @if(isset($row->distance_time))
                        <tr>
                            <td>{{ __('dashboard.distance_time') }}</td>
                            <td>: {{ $row->distance_time }}</td>
                        </tr>
                        @endif
                        @if(isset($row->distance_km))
                        <tr>
                            <td>{{ __('dashboard.distance_km') }}</td>
                            <td>: {{ $row->distance_km }}</td>
                        </tr>
                        @endif
                    </table>
                    </div>

                 
                </div>
            </div>
        </div><!-- end col-->
        
    </div>
    <!-- end row-->

   
    
</div> <!-- container -->
<!-- End Content-->

@endsection