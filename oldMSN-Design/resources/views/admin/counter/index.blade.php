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
            <!-- Add modal button -->
            <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#addModal">{{ __('dashboard.add_new') }}</button>
            <!-- Include Add modal -->
            @include($view.'.create')

            <a href="{{ route($route.'.index') }}" class="btn btn-info">{{ __('dashboard.refresh') }}</a>
        </div>
    </div>

    <div class="row">
        <div class="col-12">

            <div class="card">
                <div class="card-header">
                    <h4 class="header-title">{{ $title }} {{ __('dashboard.list') }}</h4>
                    <span>{{ __('dashboard.prefer_cells', ['cells' => 4]) }}</span>
                </div>
                <div class="card-body">

                  <!-- Data Table Start -->
                  <div class="table-responsive">
                    <table id="basic-datatable" class="table table-striped table-hover table-dark nowrap full-width">
                        <thead>
                            <tr>
                                <th>{{ __('dashboard.no') }}</th>
                                <th>{{ __('dashboard.title') }}</th>
                                {{-- <th>{{ __('dashboard.icon') }}</th> --}}
                                <th>{{ __('dashboard.value') }}</th>
                                <th>{{ __('dashboard.status') }}</th>
                                <th>{{ __('dashboard.action') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                          @foreach( $rows as $key => $row )
                            <tr>
                                <td>{{ $key + 1 }}</td>
                                <td>{!! str_limit(strip_tags($row->title), 50, ' ...') !!}</td>
                                {{-- <td>
                                    @if(!empty($row->icon))
                                    <div class="btn btn-secondary btn-sm">{!! $row->icon !!}</div>
                                    @endif
                                </td> --}}
                                <td>{{ $row->value }}</td>
                                <td>
                                    @if( $row->status == 1 )
                                    <span class="badge badge-success badge-pill">{{ __('dashboard.active') }}</span>
                                    @else
                                    <span class="badge badge-danger badge-pill">{{ __('dashboard.inactive') }}</span>
                                    @endif
                                </td>
                                <td>
                                    <button type="button" class="btn btn-success btn-sm" data-toggle="modal" data-target="#showModal-{{ $row->id }}">
                                        <i class="fas fa-eye"></i>
                                    </button>
                                    <!-- Include Show modal -->
                                    @include($view.'.show')

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