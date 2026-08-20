{{--
    Phase O5 (Keyword List/Project Management) — shared by
    keyword-research/index.blade.php and keyword-magic-tool/index.blade.php
    (both @include this same partial once). public/js/keyword-lists.js's
    own KeywordLists.open({keyword, volume, difficulty, cpc}) function
    populates and shows this modal — every "Add to List" button on both
    pages calls that one function rather than each page having its own
    separate modal/JS.
--}}
<div class="modal fade" id="kw-add-to-list-modal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Add to Keyword List</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <p class="small mb-3">Adding: <strong id="kw-add-keyword-label"></strong></p>

                <div id="kw-existing-lists-wrapper" class="mb-3">
                    <label class="form-label small">Add to existing list</label>
                    <select class="form-select" id="kw-existing-list-select">
                        <option value="">— Select a list —</option>
                    </select>
                </div>

                <p class="text-secondary small text-center mb-3">or</p>

                <div>
                    <label for="kw-new-list-name" class="form-label small">Create a new list</label>
                    <input type="text" class="form-control" id="kw-new-list-name" placeholder="e.g. Blog Ideas Q3">
                </div>

                <div id="kw-add-to-list-error" class="alert alert-danger small mt-3 d-none"></div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-primary" id="kw-add-to-list-submit">Add</button>
            </div>
        </div>
    </div>
</div>