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
                        @if(isset($row->phone))
                        <tr>
                            <td>{{ __('dashboard.phone') }}</td>
                            <td>: {{ $row->phone }}</td>
                        </tr>
                        @endif
                        @if(isset($row->company))
                        <tr>
                            <td>{{ __('dashboard.company') }}</td>
                            <td>: {{ $row->company }}</td>
                        </tr>
                        @endif
                        <tr>
                            <td>{{ __('dashboard.address') }}</td>
                            <td>: {{ $row->address }}</td>
                        </tr>
                        <tr>
                            <td>{{ __('dashboard.city') }}</td>
                            <td>: {{ $row->city }}</td>
                        </tr>
                        @if(is_file('uploads/quote/'.$row->file_path))
                        <tr>
                            <td>{{ __('dashboard.quote_files') }}</td>
                            <td>: <a href="{{ asset('uploads/quote/'.$row->file_path) }}" target="_blank" download><span class="btn btn-sm btn-primary">{{ __('dashboard.download') }}</span></a></td>
                        </tr>
                        @endif
                    </table>
                    </div>

                    <hr/>
                    <p><span class="text-highlight">{{ __('dashboard.services') }}: </span></p>
                    @foreach($row->services as $service)
                        <span class="badge badge-primary badge-pill">{{ $service->title }}</span>
                    @endforeach
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
                    @if(isset($template_estimated))
                    <a href="{{ route($route.'.invoice', ['id' => $row->id, 'action' => 'estimated']) }}" class="btn btn-info btn-sm mb-1">
                        {{ __('dashboard.estimate') }}
                    </a>
                    @endif

                    @php
                        $template_approved = \App\Models\EmailTemplate::template('quote-approved');
                    @endphp
                    @if(isset($template_approved))
                    <button type="button" class="btn btn-success btn-sm mb-1" data-toggle="modal" data-target="#approveModal-{{ $row->id }}">
                        {{ __('dashboard.approve') }}
                    </button>
                    <!-- Include Approve modal -->
                    @include($view.'.approve')
                    @endif

                    @php
                        $template_rejected = \App\Models\EmailTemplate::template('quote-rejected');
                    @endphp
                    @if(isset($template_rejected))
                    <button type="button" class="btn btn-danger btn-sm mb-1" data-toggle="modal" data-target="#rejectModal-{{ $row->id }}">
                        {{ __('dashboard.reject') }}
                    </button>
                    <!-- Include Reject modal -->
                    @include($view.'.reject')
                    @endif
                </div>
            </div>
        </div><!-- end col-->
    </div>
    <!-- end row-->

    @if($row->status != 0)
    <div class="row">
        <div class="col-12 col-lg-12">
            @php
                $template_invoice = \App\Models\EmailTemplate::template('invoice-send');
            @endphp
            @if(isset($template_invoice))
            <a href="{{ route($route.'.invoice', ['id' => $row->id, 'action' => 'invoice']) }}" class="btn btn-primary btn-sm">
                {{ __('dashboard.create_invoice') }}
            </a>
            @endif
        </div>
    </div>
    @endif
    <div class="row">
        <div class="col-12 col-lg-12">
            <div class="card">
                <div class="card-header">
                    <h4 class="header-title">{{ __('dashboard.invoice') }} {{ __('dashboard.list') }}</h4>
                </div>
                <div class="card-body">
                    <!-- Data Table Start -->
                  <div class="table-responsive">
                    <table class="table table-striped table-hover nowrap">
                        <thead>
                            <tr>
                                <th>{{ __('dashboard.sl') }}</th>
                                <th>{{ __('dashboard.invoice_no') }}</th>
                                <th>{{ __('dashboard.name') }}</th>
                                <th>{{ __('dashboard.email') }}</th>
                                <th>{{ __('dashboard.invoice_date') }}</th>
                                <th>{{ __('dashboard.invoice_type') }}</th>
                                <th>{{ __('dashboard.status') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                          @foreach( $row->invoices as $key => $row )
                            <tr>
                                <td>{{ $key + 1 }}</td>
                                <td><a href="{{ route('admin.invoice.show', [$row->id]) }}">#{{ $row->id }}</a></td>
                                <td>{{ $row->name }}</td>
                                <td>{{ $row->email }}</td>
                                <td>{{ date('h:i:s A | d-M-y', strtotime($row->created_at)) }}</td>
                                <td>
                                    @if( $row->invoice_type == 0 )
                                        {{ __('dashboard.estimate') }}
                                    @elseif( $row->invoice_type == 1 )
                                        {{ __('dashboard.advance') }}
                                    @elseif( $row->invoice_type == 2 )
                                        {{ __('dashboard.interval') }}
                                    @elseif( $row->invoice_type == 3 )
                                        {{ __('dashboard.milestone') }}
                                    @elseif( $row->invoice_type == 4 )
                                        {{ __('dashboard.final') }}
                                    @elseif( $row->invoice_type == 5 )
                                        {{ __('dashboard.full') }}
                                    @endif
                                </td>
                                <td>
                                    @if( $row->status == 1 )
                                    <span class="badge badge-primary badge-pill">{{ __('dashboard.pending') }}</span>
                                    @elseif( $row->status == 2 )
                                    <span class="badge badge-success badge-pill">{{ __('dashboard.paid') }}</span>
                                    @elseif( $row->status == 0 )
                                    <span class="badge badge-danger badge-pill">{{ __('dashboard.canceled') }}</span>
                                    @endif
                                </td>
                            </tr>
                          @endforeach
                        </tbody>
                    </table>
                  </div>
                  <!-- Data Table End -->
                </div>
            </div>
        </div><!-- end col-->
    </div>
    <!-- end row-->

    
</div> <!-- container -->
<!-- End Content-->

@endsection