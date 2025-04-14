    <!-- Edit modal content -->
    <div id="rejectModal-{{ $row->id }}" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
              <form class="needs-validation" novalidate action="{{ route($route.'.action', ['id' => $row->id, 'action' => 'reject']) }}" method="post" enctype="multipart/form-data">
                @csrf
                <div class="modal-header">
                    <h4 class="modal-title" id="myModalLabel">{{ $title }} #{{ $row->id }}</h4>
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                </div>
                <div class="modal-body">
                    <!-- Form Start -->
                    <div class="form-group">
                        <label for="subject">{{ __('dashboard.subject') }} <span>*</span></label>
                        <input type="text" class="form-control" name="subject" id="subject" value="{{ $template_rejected->title }}" required>

                        <div class="invalid-feedback">
                            {{ __('dashboard.please_provide') }} {{ __('dashboard.subject') }}
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="message">{{ __('dashboard.message') }} <span>*</span></label>
                        <textarea class="form-control summernote" name="message" id="message" rows="8" required>{!! $template_rejected->description !!}</textarea>

                        <div class="invalid-feedback">
                            {{ __('dashboard.please_provide') }} {{ __('dashboard.message') }}
                        </div>
                    </div>
                    <!-- Form End -->
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-light" data-dismiss="modal">{{ __('dashboard.close') }}</button>
                    <button type="submit" class="btn btn-danger">{{ __('dashboard.reject') }}</button>
                </div>
              </form>
            </div><!-- /.modal-content -->
        </div><!-- /.modal-dialog -->
    </div><!-- /.modal -->