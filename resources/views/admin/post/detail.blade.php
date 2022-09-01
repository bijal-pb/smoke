 <!-- Modal -->
 

<!-- Modal -->
<div class="modal fade" id="default-example-modal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">
                    Post Details
                    <small class="m-0 text-muted">
                    </small>
                </h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true"><i class="fal fa-times"></i></span>
                </button>
            </div>
            <div class="modal-body">
            <table class="table table-bordered table-striped w-100 dataTable dtr-inline">
                <form id="catForm" enctype="multipart/form-data" >
                    <div class="form-group">
                        <tr>
                            <th> <label class="form-label" for="category">Category</label></th>
                            <td> <input disabled id="category" name="category" class="form-control"></td>
                        </tr>
                    </div>
                 <div class="form-group">
                    <tr>
                        <th> <label class="form-label" for="image">Image</label>
                        <td> <img src="" name="image" id="image" width="100" height="100"/></td>
                    </tr>
                 </div>
                 <div class="form-group">
                    <tr>
                        <th> <label class="form-label" for="flavour_category_id">Flavour Category</label>
                        <td> <input disabled id="flavour_category_id" name="flavour_category_id" class="form-control"></td>
                    </tr>
                 </div>
                 <div class="form-group">
                    <tr>
                        <th> <label class="form-label" for="flavour">Flavour</label>
                        <td> <input disabled id="flavour_id" name="flavour_id" class="form-control"></td>
                    </tr>
                 </div>
                 <div class="form-group">
                    <tr>
                        <th> <label class="form-label" for="comment">Comment</label>
                        <td> <input disabled id="comment" name="comment" class="form-control"></td>
                    </tr>
                 </div>
                 <div class="form-group">
                    <tr>
                        <th> <label class="form-label" for="rate">Rate</label>
                        <td> <input disabled id="rate" name="rate" class="form-control"></td>
                    </tr>
                 </div>
                 <div class="form-group">
                    <tr>
                        <th> <label class="form-label" for="post_by">Post By</label>
                        <td> <input disabled id="post_by" name="post_by" class="form-control"></td>
                    </tr>
                 </div>
                </form>
            </table>
            </div>
        </div>
    </div>
</div>