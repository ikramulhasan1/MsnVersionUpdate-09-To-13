@extends('admin.layouts.master')
@section('title', 'Meeting List')
@section('content')

<!-- jQuery -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

<div class="container-fluid">
    <div class="row mb-2">
        <div class="col-12">
            <a href="{{ url()->current() }}" class="btn btn-info">Refresh</a>
        </div>
    </div>

    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h4 class="header-title">Meeting List</h4>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-striped table-dark nowrap">
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
                                            <input type="checkbox" class="status-toggle"
                                                data-id="{{ $row->id }}"
                                                data-toggle="toggle"
                                                data-on="Approve"
                                                data-off="Pending"
                                                data-onstyle="success"
                                                data-offstyle="danger"
                                                {{ $row->status == 'approve' ? 'checked' : '' }}>
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
