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

                    <div class="form-group">
                        <label for="image">{{ __('dashboard.logo') }} <span>*</span> <span>{{ __('dashboard.image_size', ['height' => 100, 'width' => 200]) }}</span></label>
                        <input type="file" class="form-control" name="image" id="image" required>

                        <div class="invalid-feedback">
                          {{ __('dashboard.please_provide') }} {{ __('dashboard.logo') }}
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="link">{{ __('dashboard.web_link') }}</label>
                        <input type="url" class="form-control" name="link" id="link" value="{{ old('link') }}">

                        <div class="invalid-feedback">
                          {{ __('dashboard.please_provide') }} {{ __('dashboard.web_link') }}
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