@extends('admin.layouts.master')
@section('title', 'Meeting List')
@section('content')

<!-- jQuery -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
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
                                    <th>SL</th>
                                    <th>Name</th>
                                    <th>Email</th>
                                    <th>Phone</th>
                                    {{-- <th>City</th> --}}
                                    <th>Time</th>
                                    <th>Date</th>
                                    <th>Location</th>
                                    <th>Status</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($rows as $key => $row)
                                    @php
                                        $today = \Carbon\Carbon::today();
                                        $rowDate = \Carbon\Carbon::parse($row->date);
                                        $daysDiff = $today->diffInDays($rowDate, false);
                                    @endphp
                                    <tr>
                                        <td>{{ $key + 1 }}</td>
                                        <td><a href="{{ route($route.'.show', [$row->id]) }}">{{ $row->name }}</a></td>
                                        <td><a href="mailto:{{ $row->email }}" target="_blank" rel="noopener noreferrer">{{ $row->email }}</a></td>
                                        <td><a target="_blank" rel="noopener noreferrer" href="https://wa.me/{{ $row->phone }}">{{ $row->phone }}</a></td>
                                        {{-- <td>{{ $row->city }}</td> --}}
                                        <td>{{ $row->meeting_time }}</td>
                                        <td style="font-weight: bold; color:
                                            {{ $daysDiff >= 0 && $daysDiff <= 7 ? 'red' :
                                               ($daysDiff > 7 && $daysDiff <= 14 ? 'green' : 'inherit') }};">
                                            {{ $row->date }}
                                        </td>
                                        <td>{{ $row->location }}</td>
                                      
                                        <script src="https://cdn.jsdelivr.net/npm/@tailwindcss/browser@4"></script>

                                        <td>
                                            <label class="inline-flex items-center cursor-pointer">
                                                <input type="checkbox" value="{{ $row->status }}" class="sr-only peer status-toggle" 
                                                    data-id="{{ $row->id }}"
                                                    {{ $row->status == 'approve' ? 'checked' : '' }}>
                                                <div class="relative w-11 h-6 bg-gray-200 rounded-full peer peer-focus:ring-4 peer-focus:ring-blue-300 dark:peer-focus:ring-blue-800 dark:bg-gray-700 peer-checked:after:translate-x-full rtl:peer-checked:after:-translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-0.5 after:start-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all dark:border-gray-600 peer-checked:bg-blue-600 dark:peer-checked:bg-blue-600"></div>
                                                
                                            </label>
                                        </td>
                                        
                                        <td>
                                            <a href="{{ route('admin.meetinggets.show', [$row->id]) }}" class="btn btn-success btn-sm">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            <button type="button" class="btn btn-danger btn-sm" data-toggle="modal" data-target="#deleteModal-{{ $row->id }}">
                                                <i class="fas fa-trash-alt"></i>
                                            </button>
                                            @include('admin.inc.delete')
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div> 
            </div>
        </div>
    </div>
</div>

<script>
$(document).ready(function () {
    $('.status-toggle').change(function () {
        let status = $(this).prop('checked') ? 'approve' : 'pending';
        let id = $(this).data('id');

        $.ajax({
            url: '{{ route("admin.meetinggets.store") }}',
            type: 'POST',
            data: {
                _token: '{{ csrf_token() }}',
                id: id,
                status: status
            },
            success: function (res) {
                if (res.success) {
                    toastr.success('Status updated to ' + res.status);
                } else {
                    toastr.error('Update failed');
                }
            },
            error: function () {
                toastr.error('AJAX request failed');
            }
        });
    });
});
</script>

@endsection
