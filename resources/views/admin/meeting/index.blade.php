@extends('admin.layouts.master')
@section('title', 'Meeting List')
@section('content')

<!-- jQuery -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css"/>
<script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>

<style>
/* Smart Toggle Styles */
.smart-toggle {
    position: relative;
    display: inline-block;
    width: 60px;
    height: 30px;
}
.smart-toggle input {
    opacity: 0;
    width: 0;
    height: 0;
}
.slider {
    position: absolute;
    cursor: pointer;
    background-color: #dc3545;
    border-radius: 34px;
    top: 0; left: 0;
    right: 0; bottom: 0;
    transition: .4s;
}
.slider:before {
    position: absolute;
    content: "";
    height: 24px;
    width: 24px;
    left: 3px;
    bottom: 3px;
    background-color: white;
    border-radius: 50%;
    transition: .4s;
}
input:checked + .slider {
    background-color: #28a745;
}
input:checked + .slider:before {
    transform: translateX(30px);
}
.slider-text {
    position: absolute;
    width: 100%;
    text-align: center;
    top: 3px;
    color: white;
    font-size: 12px;
    font-weight: bold;
    pointer-events: none;
}
.smart-toggle.loading .slider:before {
    background: #f3f3f3 url('https://i.imgur.com/llF5iyg.gif') no-repeat center;
    background-size: 18px;
}
</style>
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
                                    <th>SL</th>
                                    <th>Name</th>
                                    <th>Email</th>
                                    <th>Phone</th>
                                    <th>City</th>
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
                                        <td>{{ $row->name }}</td>
                                        <td>{{ $row->email }}</td>
                                        <td>{{ $row->phone }}</td>
                                        <td>{{ $row->city }}</td>
                                        <td>{{ $row->meeting_time }}</td>
                                        <td style="font-weight: bold; color:
                                            {{ $daysDiff >= 0 && $daysDiff <= 7 ? 'red' :
                                               ($daysDiff > 7 && $daysDiff <= 14 ? 'green' : 'inherit') }};">
                                            {{ $row->date }}
                                        </td>
                                        <td>{{ $row->location }}</td>
                                        <td>
                                            <label class="smart-toggle {{ $row->status }}" data-id="{{ $row->id }}">
                                                <input type="checkbox" {{ $row->status == 'approve' ? 'checked' : '' }}>
                                                <span class="slider"></span>
                                                <span class="slider-text">{{ $row->status == 'approve' ? 'Approved' : 'Pending' }}</span>
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
