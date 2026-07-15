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
            @if(isset($row->quote_id))
            <a href="{{ route('admin.get-quote.show', [$row->quote_id]) }}" class="btn btn-primary">{{ __('dashboard.quote') }}</a>
            @endif
        </div>
    </div>

    <div class="row mt-3 mb-3">
        <div class="col-12">
            <!-- Logo & title -->
            <div class="clearfix">
                <div class="float-right">
                    <h4 class="m-0 d-print-none">{{ __('dashboard.invoice') }} #{{ $row->id }}</h4>
                </div>
            </div>

            <div class="row">
                <div class="col-sm-6">
                    <div class="pull-left mt-3">
                        <p><b>{{ __('dashboard.hello') }}, {{ $row->name }}</b></p>
                    </div>

                </div><!-- end col -->
                <div class="col-sm-4 offset-sm-2">
                    <div class="mt-3 float-right">
                        <p class="m-b-10"><strong>{{ __('dashboard.invoice_date') }} : </strong> <span class="float-right"> {{ date('d-M-y', strtotime($row->invoice_date)) }}</span></p>
                        @if(isset($row->due_date))
                        <p class="m-b-10"><strong>{{ __('dashboard.due_date') }} : </strong> <span class="float-right"> {{ date('d-M-y', strtotime($row->due_date)) }}</span></p>
                        @endif
                        <p class="m-b-10"><strong>{{ __('dashboard.invoice_type') }} : </strong> <span class="float-right">
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
                        </span></p>
                        <p class="m-b-10"><strong>{{ __('dashboard.invoice_status') }} : </strong> <span class="float-right">
                            @if( $row->status == 1 )
                            <span class="badge badge-primary badge-pill">{{ __('dashboard.pending') }}</span>
                            @elseif( $row->status == 2 )
                            <span class="badge badge-success badge-pill">{{ __('dashboard.paid') }}</span>
                            @elseif( $row->status == 0 )
                            <span class="badge badge-danger badge-pill">{{ __('dashboard.canceled') }}</span>
                            @endif
                        </span></p>
                        @if(isset($row->quote_id))
                        <p class="m-b-10"><strong>{{ __('dashboard.quote_no') }} : </strong> <span class="float-right">#{{ $row->quote_id }} </span></p>
                        @endif
                    </div>
                </div><!-- end col -->
            </div>
            <!-- end row -->

            <div class="row mt-3">
                <div class="col-sm-6">
                    <h6>{{ __('dashboard.billing_address') }}</h6>
                    <address class="line-h-24">
                        <abbr title="{{ __('dashboard.name') }}">{{ __('dashboard.name') }}:</abbr> {{ $row->name }}<br>
                        <abbr title="{{ __('dashboard.email') }}">{{ __('dashboard.email') }}:</abbr> {{ $row->email }}<br>
                        @if(isset($row->phone))
                        <abbr title="{{ __('dashboard.phone') }}">{{ __('dashboard.phone') }}:</abbr> {{ $row->phone }}<br>
                        @endif
                        @if(isset($row->company))
                        <abbr title="{{ __('dashboard.company') }}">{{ __('dashboard.company') }}:</abbr> {{ $row->company }}<br>
                        @endif
                        <abbr title="{{ __('dashboard.address') }}">{{ __('dashboard.address') }}:</abbr> {{ $row->address }}<br>
                        @if(isset($row->city))
                        <abbr title="{{ __('dashboard.city') }}">{{ __('dashboard.city') }}:</abbr> {{ $row->city }}<br>
                        @endif
                        @if(isset($row->reference))
                        <abbr title="{{ __('dashboard.reference') }}">{{ __('dashboard.reference') }}:</abbr> {{ $row->reference }}<br>
                        @endif
                    </address>
                </div> <!-- end col -->
            </div> 
            <!-- end row -->

            <div class="row">
                <div class="col-12">
                    @if(count($row->services) > 0)
                    <span>{{ __('dashboard.services') }}: </span>
                    @foreach($row->services as $service)
                        <span class="badge badge-primary">{{ $service->title }}</span> 
                    @endforeach
                    <br/><br/>
                    @endif
                    <div>
                    <p>{{ __('dashboard.note') }}: </p>
                      {!! $row->message !!}
                    </div>
                </div> <!-- end col -->
            </div>
            <!-- end row -->

            <div class="row">
                <div class="col-sm-6">
                    <div class="clearfix pt-5">
                    </div>
                </div> <!-- end col -->
                <div class="col-sm-6">
                    <div class="float-right">
                        @if(isset($row->service_charge))
                        <p><b>{{ __('dashboard.service_bill') }}:</b> <span class="float-right">{{ $row->service_charge }} {{ __('common.currency') }}</span></p>
                        @endif
                        @if(isset($row->tax))
                        <p><b>{{ __('dashboard.tax_charge') }}:</b> <span class="float-right">{{ $row->tax }} {{ __('common.currency') }}</span></p>
                        @endif
                        @if(isset($row->shipping))
                        <p><b>{{ __('dashboard.shipping_charge') }}:</b> <span class="float-right">{{ $row->shipping }} {{ __('common.currency') }}</span></p>
                        @endif
                        <p><b>{{ __('dashboard.total') }}:</b> <span class="float-right">{{ $row->total_amount }} {{ __('common.currency') }}</span></p>
                        @if(isset($row->discount_amount))
                        <p><b>{{ __('dashboard.discount') }}:</b> <span class="float-right">{{ $row->discount_amount }} {{ __('common.currency') }}</span></p>
                        @endif
                        <h3><b>{{ __('dashboard.payable') }}:</b> {{ $row->invoice_amount }} {{ __('common.currency') }}</h3>
                    </div>
                    <div class="clearfix"></div>
                </div> <!-- end col -->
            </div>
            <!-- end row -->

            <div class="mt-4 mb-1">
                <div class="text-right d-print-none">
                    <button type="button" class="btn btn-primary btn-sm" data-toggle="modal" data-target="#editModal-{{ $row->id }}">
                        <i class="far fa-edit"></i>
                    </button>
                    <!-- Include Edit modal -->
                    @include($view.'.edit')

                    <button type="button" class="btn btn-danger btn-sm" data-toggle="modal" data-target="#deleteModal-{{ $row->id }}">
                        <i class="fas fa-trash-alt"></i>
                    </button>
                    <!-- Include Delete modal -->
                    @include('admin.inc.delete')

                    @if(is_file('uploads/invoice/'.$row->attach))
                    <a href="{{ asset('uploads/invoice/'.$row->attach) }}" class="btn btn-sm btn-warning waves-effect waves-light" download>{{ __('dashboard.attach') }}</a>
                    @endif

                    <a href="javascript:window.print()" class="btn btn-sm btn-info waves-effect waves-light"><i class="mdi mdi-printer mr-1"></i> {{ __('dashboard.print') }}</a>
                </div>
            </div>
        </div> <!-- end col -->
    </div>
    <!-- end row --> 

    
</div> <!-- container -->
<!-- End Content-->

@endsection