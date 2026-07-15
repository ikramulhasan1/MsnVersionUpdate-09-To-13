    <!-- Add modal content -->
    <div id="addModal" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
              <form class="needs-validation" novalidate action="{{ route($route.'.store') }}" method="post" enctype="multipart/form-data">
                @csrf

                <div class="modal-header">
                    <h4 class="modal-title" id="myModalLabel">{{ __('dashboard.add') }} {{ $title }}</h4>
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                </div>
                <div class="modal-body">
                    <!-- Form Start -->
                    <div class="form-group">
                        <label for="title">{{ __('dashboard.title') }} <span>*</span></label>
                        <input type="text" class="form-control" name="title" id="title" value="{{ old('title') }}" required>

                        <div class="invalid-feedback">
                          {{ __('dashboard.please_provide') }} {{ __('dashboard.title') }}
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="description">{{ __('dashboard.description') }}</label>
                        <textarea class="form-control summernote" name="description" id="description" rows="8">{{ old('description') }}</textarea>
                    </div>

                    {{-- <div class="form-group">
                        <label for="icon">{{ __('dashboard.icon') }}</label>
                        <input type="text" class="form-control" name="icon" id="icon" value="{{ old('icon') }}" placeholder='<i class="far fa-star"></i>'>

                        <div class="invalid-feedback">
                          {{ __('dashboard.please_provide') }} {{ __('dashboard.icon') }}
                        </div>
                    </div> --}}

                    <div class="form-group">
                        <label for="value">{{ __('dashboard.value') }} <span>*</span></label>
                        <input type="number" class="form-control" name="value" id="value" value="{{ old('value') }}" required>

                        <div class="invalid-feedback">
                          {{ __('dashboard.please_provide') }} {{ __('dashboard.value') }}
                        </div>
                    </div>
                    <!-- Form End -->
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-light" data-dismiss="modal">{{ __('dashboard.close') }}</button>
                    <button type="submit" class="btn btn-primary">{{ __('dashboard.save') }}</button>
                </div>
              </form>
            </div><!-- /.modal-content -->
        </div><!-- /.modal-dialog -->
    </div><!-- /.modal -->