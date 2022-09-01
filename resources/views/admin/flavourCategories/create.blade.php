<!-- Modal -->
<div class="modal fade" id="default-example-modal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">
                    Flavour Category
                    <small class="m-0 text-muted">
                    </small>
                </h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true"><i class="fal fa-times"></i></span>
                </button>
            </div>
            <div class="modal-body">
                <form id="catForm">
                    {{-- <div class="form-group">
                        <label class="form-label" for="category_id">Select Category</label>
                        <select id="category_id" name="category_id" class="form-control">
                            @foreach($cats as $c)
                                <option value="{{ $c->id }}"> {{ $c->name }}</option>
                            @endforeach
                        </select>
                    </div> --}}
                    <div class="form-group">
                        <label class="form-label" for="name">Name</label>
                        <input type="text" id="name" name="name" class="form-control">
                        <input type="hidden" value="" id="cat_id">
                    </div>
                    {{-- <div class="form-group">
                        <label class="form-label" for="is_approve">Is Approve : </label>
                        <select id="is_approve" name="is_approve" class="form-control">
                            <option value="1"> Yes</option>
                            <option value="0"> No</option>
                        </select>
                    </div> --}}
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                <button type="button" id="save" class="btn btn-success">Save</button>
            </div>
        </div>
    </div>
</div>