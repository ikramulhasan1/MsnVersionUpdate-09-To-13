@extends('admin.layouts.master')
{{-- @section('title', $title) --}}
@section('content')

<!-- Start Content-->
<div class="container-fluid">

    <!-- start page title -->
    <!-- Include page breadcrumb -->
    {{-- @include('admin.inc.breadcrumb') --}}
    <!-- end page title -->


    <div class="row">
        <div class="col-12">
            <a href="{{ route('admin.redirects.index') }}" class="btn btn-info">{{ __('dashboard.back') }}</a>
        </div>
    </div>

    <div class="row">
        <div class="col-12 col-lg-8">
            <div class="card">
                <div class="card-header">
                    {{-- <h4 class="header-title">{{ __('dashboard.edit') }} {{ $title }}</h4> --}}
                </div>
                <form class="needs-validation" novalidate action="{{ route('admin.redirects.update', $row->id) }}" method="post" enctype="multipart/form-data">
                    @csrf
                    @method('PUT')
                    <div class="card-body">

                        <!-- Form Start -->
                        <div class="form-group">
                            <label for="submitted_url">Submitted Url <span>*</span></label>
                            <input type="text" class="form-control" name="submitted_url" id="submitted_url" value="{{ $row->submitted_url }}" required>
                        </div>
                        <div class="form-group">
                            <label for="redirect_to">Submitted Url <span>*</span></label>
                            <input type="text" class="form-control" name="redirect_to" id="redirect_to" value="{{ $row->redirect_to }}" required>
                        </div>

                    
                        

                        {{-- <div class="form-group">
                            <label for="status">{{ __('dashboard.select_status') }}</label>
                            <select class="wide" name="status" id="status" data-plugin="customselect">
                                <option value="1" @if( $row->status == 1 ) selected @endif>{{ __('dashboard.active') }}</option>
                                <option value="0" @if( $row->status == 0 ) selected @endif>{{ __('dashboard.inactive') }}</option>
                            </select>
                        </div> --}}
                        <!-- Form End -->

                    </div>
                    <div class="card-footer">
                        <div class="form-group">
                            <button type="submit" class="btn btn-primary">{{ __('dashboard.update') }}</button>
                        </div>
                    </div>
                </form>
            </div>
        </div><!-- end col-->
    </div>
    <!-- end row-->


</div> <!-- container -->
<!-- End Content-->
<script>
    CKEDITOR.replace('editor', {
        on: {
            instanceReady: function(ev) {
                this.dataProcessor.writer.setRules('strong', {
                    indent: false,
                    breakBeforeOpen: false,
                    breakAfterOpen: false,
                    breakBeforeClose: false,
                    breakAfterClose: false
                });
            }
        },
        coreStyles_bold: {
            element: 'b',
            overrides: 'strong'
        } // Converts <strong> to <b>
    });
    CKEDITOR.replace('editor1', {
        on: {
            instanceReady: function(ev) {
                this.dataProcessor.writer.setRules('strong', {
                    indent: false,
                    breakBeforeOpen: false,
                    breakAfterOpen: false,
                    breakBeforeClose: false,
                    breakAfterClose: false
                });
            }
        },
        coreStyles_bold: {
            element: 'b',
            overrides: 'strong'
        } // Converts <strong> to <b>
    });
</script>
@endsection