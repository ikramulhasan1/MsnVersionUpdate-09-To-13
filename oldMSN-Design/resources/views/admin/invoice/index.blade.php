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
            <a href="{{ route($route.'.create') }}" class="btn btn-primary">{{ __('dashboard.add_new') }}</a>
            
            <a href="{{ route($route.'.index') }}" class="btn btn-info">{{ __('dashboard.refresh') }}</a>
        </div>
    </div>

    <div class="row">
        <div class="col-12">

            <div class="card">
                <div class="card-header">
                    <h4 class="header-title">{{ $title }} {{ __('dashboard.list') }}</h4>
                </div>
                <div class="card-body">

                  <!-- Data Table Start -->
                  <div class="table-responsive">
                    <table id="basic-datatable" class="table table-striped table-hover table-dark nowrap full-width">
                        <thead>
                            <tr>
                                <th>{{ __('dashboard.sl') }}</th>
                                <th>{{ __('dashboard.invoice_no') }}</th>
                                <th>{{ __('dashboard.name') }}</th>
                                <th>{{ __('dashboard.email') }}</th>
                                <th>{{ __('dashboard.invoice_date') }}</th>
                                <th>{{ __('dashboard.invoice_type') }}</th>
                                <th>{{ __('dashboard.status') }}</th>
                                <th>{{ __('dashboard.action') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                          @foreach( $rows as $key => $row )
                            <tr>
                                <td>{{ $key + 1 }}</td>
                                <td><a href="{{ route($route.'.show', [$row->id]) }}">#{{ $row->id }}</a></td>
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
                                <td>
                                    <a href="{{ route($route.'.show', [$row->id]) }}" class="btn btn-success btn-sm">
                                        <i class="fas fa-eye"></i>
                                    </a>

                                    <button type="button" class="btn btn-danger btn-sm" data-toggle="modal" data-target="#deleteModal-{{ $row->id }}">
                                        <i class="fas fa-trash-alt"></i>
                                    </button>
                                    <!-- Include Delete modal -->
                                    @include('admin.inc.delete')
                                </td>
                            </tr>
                          @endforeach
                        </tbody>
                    </table>
                  </div>
                  <!-- Data Table End -->

                </div> <!-- end card body-->
            </div> <!-- end card -->
        </div><!-- end col-->
    </div>
    <!-- end row-->

    
</div> <!-- container -->
<!-- End Content-->

@endsection