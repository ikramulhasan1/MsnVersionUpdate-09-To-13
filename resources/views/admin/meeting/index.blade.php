@extends('admin.layouts.master')
@section('title', $title)
@section('content')
<link href="https://gitcdn.github.io/bootstrap-toggle/2.2.2/css/bootstrap-toggle.min.css" rel="stylesheet">
<script src="https://gitcdn.github.io/bootstrap-toggle/2.2.2/js/bootstrap-toggle.min.js"></script>
<!-- Start Content-->
<div class="container-fluid">
    
    <!-- start page title -->
    <!-- Include page breadcrumb -->
    @include('admin.inc.breadcrumb')
    <!-- end page title --> 


    <div class="row">
        <div class="col-12">
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
                                <th>{{ __('dashboard.name') }}</th>
                                <th>{{ __('dashboard.email') }}</th>
                                <th>{{ __('dashboard.phone') }}</th>
                                <th>{{ __('dashboard.city') }}</th>
                                <th>{{ __('dashboard.time') }}</th>
                                <th>{{ __('dashboard.date') }}</th>
                                <th>{{ __('dashboard.location') }}</th>
                                <th>{{ __('dashboard.status') }}</th>
                                <th>{{ __('dashboard.action') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                          @foreach( $rows as $key => $row )
                            @php
                                // $currentDate = \Carbon\Carbon::now();
                                // $diffInDays = $currentDate->diffInDays($rowDate, false);
                            
                                $today = \Carbon\Carbon::today();
                                $rowDate = \Carbon\Carbon::parse($row->date);
                                $daysDiff = $today->diffInDays($rowDate, false);
                            @endphp
                         
                            <tr>
                                <td>{{ $key + 1 }}</td>
                                <td><a href="{{ route($route.'.show', [$row->id]) }}">#{{ $row->name }}</a></td>
                                <td>{{ $row->email }}</td>
                                <td>{{ $row->phone }}</td>
                                <td>{{ $row->city }}</td>
                                <td>{{ $row->meeting_time }}</td>

                                <td style="font-weight: bold; color: 
                                    {{ $daysDiff >= 0 && $daysDiff <= 7 ? 'red' : 
                                    ($daysDiff > 7 && $daysDiff <= 14 ? 'green' : 'inherit') }}">
                                    {{ $row->date }}
                                </td>

                                <td>{{ $row->location }}</td>
                                {{-- <td>{{ date('h:i:s A | d-M-y', strtotime($row->created_at)) }}</td> --}}
                                {{-- <td>
                                    @if( $row->status == 'pending' )
                                    <span class="badge badge-warning badge-pill">{{ __('dashboard.pending') }}</span>
                                    @elseif( $row->status == 'approve' )
                                    <span class="badge badge-success badge-pill">{{ __('dashboard.approved') }}</span>
                                    @endif
                                </td> --}}

                                <td>
                                    {{-- <input type="checkbox"
                                           class="status-toggle"
                                           data-id="{{ $row->id }}"
                                           {{ $row->status == 'approve' ? 'checked' : '' }}
                                    > --}}
                                    <input type="checkbox" data-id="{{ $row->id }}" {{ $row->status == 'approve' ? 'checked' : '' }} data-toggle="toggle" data-on="approve" data-off="pending" data-onstyle="success" data-offstyle="danger">

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



<script>
    document.addEventListener('DOMContentLoaded', function () {
        const toggles = document.querySelectorAll('.status-toggle');

        toggles.forEach(toggle => {
            toggle.addEventListener('change', function () {
                const status = this.checked ? 'approve' : 'pending';
                const userId = this.getAttribute('data-id');

                fetch("{{ route($route.'.store') }}", {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Content-Type': 'application/json',
                    },
                    body: JSON.stringify({
                        id: userId,
                        status: status
                    })
                })
                .then(response => response.json())
                .then(data => {
                    if(data.success){
                        alert(data.message);
                    } else {
                        alert("Something went wrong.");
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                });
            });
        });
    });
</script>


@endsection