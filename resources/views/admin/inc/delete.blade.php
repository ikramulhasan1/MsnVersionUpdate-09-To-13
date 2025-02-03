    <!-- Delete modal -->
    <div class="modal fade" id="deleteModal-{{ $row->id }}" tabindex="-1" role="dialog" aria-labelledby="mySmallModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-sm">
          <form action="{{ route($route.'.destroy', [$row->id]) }}" method="post" class="delete-form">
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
    </div><!-- /.modal -->