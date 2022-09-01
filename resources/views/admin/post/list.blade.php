@extends('layouts.admin.master') 
@section('content')
<div class="subheader">
    <h1 class="subheader-title">
        <i class='subheader-icon fal fa-id-badge'></i> Posts <span class='fw-300'></span> <sup class='badge badge-primary fw-500'></sup>
        {{-- <small>
            Insert page description or punch line
        </small> --}}
    </h1>
</div>
<!-- Your main content goes below here: -->
<div class="row">
    <div class="col-xl-12">
        <div id="panel-1" class="panel">
            <div class="panel-hdr">
                <h2>
                  Posts <span class="fw-300"><i></i></span>
                </h2>
                <div class="panel-toolbar">
                    <button class="btn btn-panel" data-action="panel-collapse" data-toggle="tooltip" data-offset="0,10" data-original-title="Collapse"></button>
                    <button class="btn btn-panel" data-action="panel-fullscreen" data-toggle="tooltip" data-offset="0,10" data-original-title="Fullscreen"></button>
                    {{-- <button class="btn btn-panel" data-action="panel-close" data-toggle="tooltip" data-offset="0,10" data-original-title="Close"></button> --}}
                </div>
            </div>
            <div class="panel-container show">
                <!-- <button type="button" id="btn-add" class="btn btn-primary float-right m-3" data-toggle="modal" data-target="#default-example-modal">Add Flavour</button> -->
                <div class="panel-content" >
                    <div id="categoryData">
                        <table id="post-table" class="table table-bordered table-hover table-striped w-100 dataTable dtr-inline">
                            <thead class="bg-primary-600">
                                <tr>
                                    <th>Id </th>
                                    <th>User Name </th>
                                    <th>Category</th>
                                     <th>Comment</th>
                                     <th>Rate</th>
                                     <th>Info</th>
                                </tr>
                            </thead>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @include('admin.post.detail')
</div>
@endsection

@section('page_js')
<script type="text/javascript">
    $(document).ready(function(){
        $('#btn-add').click(function () {
                $('#catForm').trigger("reset");
            });

            var table =  $('#post-table').DataTable(
                {
                    responsive: true,
                    serverSide: true,
                    ajax: "{{ route('admin.post.list') }}",
                    columns: [
                        {data: 'id', name: 'Id'},
                         {data: 'post_by', name: 'Post_by'},
                        {data: 'category', name: 'Category'},
                        {data: 'comment', name: 'Comment', orderable: false},
                        {data: 'rate', name: 'Rate'},
                        {data: 'action', name: 'Action', orderable: false, searchable: false},
                        
                    ],
                    lengthChange: true,
                    dom: '<"float-left"B><"float-right"f>rt<"row"<"col-sm-4"l><"col-sm-4"i><"col-sm-4"p>>',
                        // "<'row mb-3'<'col-sm-12 col-md-6 d-flex align-items-center justify-content-start'f><'col-sm-12 col-md-6 d-flex align-items-center justify-content-end'lB>>" +
                        // "<'row'<'col-sm-12'tr>>" +
                        // "<'row'<'col-sm-12 col-md-5'i><'col-sm-12 col-md-7'p>>",
                    buttons: [
                        {
                            extend: 'pdfHtml5',
                            text: 'PDF',
                            titleAttr: 'Generate PDF',
                            className: 'btn-outline-primary btn-sm mr-1'
                        },
                        {
                            extend: 'excelHtml5',
                            text: 'Excel',
                            titleAttr: 'Generate Excel',
                            className: 'btn-outline-primary btn-sm mr-1'
                        },
                        {
                            extend: 'csvHtml5',
                            text: 'CSV',
                            titleAttr: 'Generate CSV',
                            className: 'btn-outline-primary btn-sm mr-1'
                        },
                        {
                            extend: 'copyHtml5',
                            text: 'Copy',
                            titleAttr: 'Copy to clipboard',
                            className: 'btn-outline-primary btn-sm mr-1'
                        },
                        {
                            extend: 'print',
                            text: 'Print',
                            titleAttr: 'Print Table',
                            className: 'btn-outline-primary btn-sm'
                        }
                    ]
                });
        // upload image 

        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });

        var oTable = $('#post_data').dataTable(); 
        oTable.hide();

        $('#catForm').submit(function(e) {
            e.preventDefault();
            let formData = new FormData(this);
            var ajaxurl = "{{ route('admin.post.store') }}";
            $.ajax({
                type:'POST',
                data: formData,
                contentType: false,
                processData: false,
                success: (data) => {
                    if(data.status == 'success')
                    {
                        $('#image').val('');
                        $('#default-example-modal').modal('hide');
                        $('#catForm').trigger("reset");
                        toastr['success']('Save successfully!');
                         table.ajax.reload( null, false);
                    }else{
                        toastr['error'](data.message);
                    }
                },
                error: function(response){
                    toastr['error']('Something went wrong, Please try again!');
                    console.log('Error:', data);
                }
            });
        });
      
        $(document).on("click", ".detail-post" , function () {
          
            var ajaxurl =  $(this).data('url');
            $.ajax({
                type: "GET",
                url: ajaxurl,
                dataType: 'json',
                headers: {
                    'X-CSRF-TOKEN': "{{csrf_token()}}"
                },
                success: function (data) {
                    $('#category').val(data.data.category);
                  $('#image').attr('src',data.data.image);
                    $('#post_id').val(data.data.id);
                    $('#flavour_category_id').val(data.data.flavour_category_name);
                     $('#flavour_id').val(data.data.flavour);
                     $('#comment').val(data.data.comment);
                     $('#rate').val(data.data.rate);
                     $('#post_by').val(data.data.post_by);
                },
                error: function (data) {
                    console.log('Error:', data);
                }
            });
            
        });
       
    });

</script>
@endsection