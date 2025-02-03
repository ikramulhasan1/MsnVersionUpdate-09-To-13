    <!-- Show modal content -->
    <div id="showModal-{{ $row->id }}" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title" id="myModalLabel">{{ __('dashboard.view') }} {{ $title }}</h4>
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                </div>
                <div class="modal-body">
                    <!-- Details View Start -->
                    <h4><span class="text-highlight">{{ __('dashboard.subject') }}:</span> {{ $row->subject }}</h4>
                    <p><span class="text-highlight">{{ __('dashboard.name') }}:</span> {{ $row->name }}</p>
                    <p><span class="text-highlight">{{ __('dashboard.email') }}:</span> {{ $row->email }}</p>
                    @if(isset($row->phone))
                    <p><span class="text-highlight">{{ __('dashboard.phone') }}:</span> {{ $row->phone }}</p>
                    @endif
                    <hr/>
                    <p><span class="text-highlight">{{ __('dashboard.message') }}:</span> {!! strip_tags($row->message, '<p><a><b><i><u><strong><br><ul><ol><li><del><ins><sup><sub><pre>') !!}</p>

                    <hr/>
                    <p><span class="text-highlight">{{ __('dashboard.status') }}:</span> 
                    @if( $row->status == 1 )
                    <span class="badge badge-success badge-pill">{{ __('dashboard.active') }}</span>
                    @else
                    <span class="badge badge-danger badge-pill">{{ __('dashboard.inactive') }}</span>
                    @endif
                    </p>
                    <!-- Details View End -->
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-light" data-dismiss="modal">{{ __('dashboard.close') }}</button>
                </div>
            </div><!-- /.modal-content -->
        </div><!-- /.modal-dialog -->
    </div><!-- /.modal -->