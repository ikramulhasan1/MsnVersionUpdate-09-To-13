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
@dd( $meeting->id)
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

                    <hr/>
                    <p><span class="text-highlight">{{ __('dashboard.services') }}: </span></p>
                    {{-- @foreach($row->services as $service)
                        <span class="badge badge-primary badge-pill">{{ $service->title }}</span>
                    @endforeach --}}
                    <hr/>

                    @if(isset($row->message))
                    <p><span class="text-highlight">{{ __('dashboard.note') }}: </span> {!! strip_tags($row->message, '<p><a><b><i><u><strong><br><ul><ol><li><del><ins><sup><sub><pre>') !!}</p>
                    <hr/>
                    @endif
                </div>
            </div>
        </div><!-- end col-->
        <div class="col-12 col-lg-4">
            <div class="card">
                <div class="card-header">
                    <h4 class="header-title">{{ __('dashboard.sidebar') }}</h4>
                </div>
                <div class="card-body">
                    <p><span class="text-highlight">{{ __('dashboard.total_amount') }}: </span>
                        @if(isset($row->amount))
                        {{ $row->amount }} {{ __('common.currency') }}
                        @else
                        <span class="badge badge-warning badge-pill">{{ __('dashboard.no_value') }}</span>
                        @endif
                    </p>

                    <hr/>
                    <p><span class="text-highlight">{{ __('dashboard.status') }}:</span> 
                    @if( $row->status == 1 )
                    <span class="badge badge-primary badge-pill">{{ __('dashboard.pending') }}</span>
                    @elseif( $row->status == 2 )
                    <span class="badge badge-info badge-pill">{{ __('dashboard.estimated') }}</span>
                    @elseif( $row->status == 3 )
                    <span class="badge badge-success badge-pill">{{ __('dashboard.approved') }}</span>
                    @elseif( $row->status == 0 )
                    <span class="badge badge-danger badge-pill">{{ __('dashboard.rejected') }}</span>
                    @endif
                    </p>

                    <hr/>
                    <p><span class="text-highlight">{{ __('dashboard.prefer_contact') }} </span> 
                    @if( $row->prefer_contact == 1 )
                    <span>{{ __('dashboard.phone') }}: <a href="tel:{{ $row->phone }}" target="_blank">{{ $row->phone }}</a></span>
                    @elseif( $row->prefer_contact == 2 )
                    <span>{{ __('dashboard.email') }}: <a href="mailto:{{ $row->email }}" target="_blank">{{ $row->email }}</a></span>
                    @endif
                    </p>

                    <hr/>
                    {{ __('dashboard.send_mail') }} : <br/>

                    @php
                        $template_estimated = \App\Models\EmailTemplate::template('quote-estimated');
                    @endphp
                    {{-- @if(isset($template_estimated))
                    <a href="{{ route($route.'.invoice', ['id' => $row->id, 'action' => 'estimated']) }}" class="btn btn-info btn-sm mb-1">
                        {{ __('dashboard.estimate') }}
                    </a>
                    @endif --}}

                   

                    
                </div>
            </div>
        </div><!-- end col-->
    </div>
    <!-- end row-->

   
    
</div> <!-- container -->
<!-- End Content-->

@endsection