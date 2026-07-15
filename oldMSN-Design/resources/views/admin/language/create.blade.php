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
                        <label for="name">{{ __('dashboard.name') }} <span>*</span></label>
                        <input type="text" class="form-control" name="name" id="name" value="{{ old('name') }}" required>

                        <div class="invalid-feedback">
                          {{ __('dashboard.please_provide') }} {{ __('dashboard.name') }}
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="code"> {{ __('dashboard.locale') }} <span>*</span></label>
                        <input type="text" class="form-control" name="code" id="code" value="{{ old('code') }}" required>

                        <div class="invalid-feedback">
                          {{ __('dashboard.please_provide') }} {{ __('dashboard.locale') }}
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