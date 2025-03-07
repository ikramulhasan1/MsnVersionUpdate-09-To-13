@extends('admin.layouts.master')
{{-- @section('title', $title) --}}
@section('content')

<!-- Start Content-->
<div class="container-fluid">
    
    <!-- start page title -->
    <!-- Include page breadcrumb -->
    {{-- @include('admin.inc.breadcrumb') --}}
    <!-- end page title --> 


    <div class="row mt-3">
        <div class="col-12">
            <a href="{{ route('admin.subservices.create') }}" class="btn btn-primary">{{ __('dashboard.add_new') }}</a>

            <a href="{{ route('admin.subservices.index') }}" class="btn btn-info">{{ __('dashboard.refresh') }}</a>
        </div>
    </div>

    <div class="row">
        <div class="col-12">

            <div class="card">
                <div class="card-header">
                    {{-- <h4 class="header-title">{{ $title }} {{ __('dashboard.list') }}</h4> --}}
                </div>
                <div class="card-body">
                  
                  <!-- Data Table Start -->
                  <div class="table-responsive">
                    <table id="basic-datatable" class="table table-striped table-hover table-dark nowrap full-width">
                        <thead>
                            <tr>
                                <th>{{ __('dashboard.no') }}</th>
                                <th>{{ __('dashboard.thumbnail') }}</th>
                                <th>{{ __('dashboard.title') }}</th>
                                <th>Service</th>
                                <th>{{ __('dashboard.status') }}</th>
                                <th>{{ __('dashboard.action') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                          @foreach( $rows as $key => $row )
                            <tr>
                                <td>{{ $key + 1 }}</td>
                                <td>
                                    @if(is_file('uploads/'.$path.'/'.$row->image_path))
                                    <img src="{{ asset('uploads/'.$path.'/'.$row->image_path) }}" class="img-fluid" alt="{{ $row->title }}">
                                    @endif
                                </td>
                                <td>{!! str_limit(strip_tags($row->title), 50, ' ...') !!}</td>
                                <td>{!! str_limit(strip_tags($row->service->title), 30, ' ...') !!}</td>
                                <td>
                                    @if( $row->status == 1 )
                                    <span class="badge badge-success badge-pill">{{ __('dashboard.active') }}</span>
                                    @else
                                    <span class="badge badge-danger badge-pill">{{ __('dashboard.inactive') }}</span>
                                    @endif
                                </td>
                                <td>
                                    <a href="{{ route('admin.subservices.show', [$row->id]) }}" class="btn btn-success btn-sm">
                                        <i class="fas fa-eye"></i>
                                    </a>

                                    <a href="{{ route('admin.subservices.edit',[$row->id]) }}" class="btn btn-primary btn-sm">
                                        <i class="far fa-edit"></i>
                                    </a>

                                    <button type="button" class="btn btn-danger btn-sm" data-toggle="modal" data-target="#deleteModal-{{ $row->id }}">
                                        <i class="fas fa-trash-alt"></i>
                                    </button>
                                    <!-- Include Delete modal -->

                                        <!-- Delete modal -->
                                    <div class="modal fade" id="deleteModal-{{ $row->id }}" tabindex="-1" role="dialog" aria-labelledby="mySmallModalLabel" aria-hidden="true">
                                        <div class="modal-dialog modal-sm">
                                        <form action="{{ route('admin.subservices.destroy', [$row->id]) }}" method="post" class="delete-form">
                                        @csrf
                                        @method('DELETE')
                                            <div class="modal-content">
                                                <div class="modal-body text-center">
                                                    <h3>{{ __('dashboard.are_you_sure') }}</h3>
                                                    <p>{{ __('dashboard.delete_warning') }}</p>
                                                </div>
                                                <div class="modal-footer">
                                                    <button type="submit" class="btn btn-danger">{{ __('dashboard.confirm') }}</button>
                                                    <button type="button" class="btn btn-light" data-dismiss="modal">{{ __('dashboard.close') }}</button>
                                                </div>
                                            </div><!-- /.modal-content -->
                                        </form>
                                        </div><!-- /.modal-dialog -->
                                    </div>
                                    {{-- @include('admin.inc.delete') --}}
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