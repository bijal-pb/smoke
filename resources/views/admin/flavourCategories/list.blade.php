@extends('layouts.admin.master') 
@section('content')
<div class="subheader">
    <h1 class="subheader-title">
    <i class=" subheader-icon fal fa-amp-guitar"></i>Flavour Categories <span class='fw-300'></span> <sup class='badge badge-primary fw-500'></sup>
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
                   Flavour Categories <span class="fw-300"><i></i></span>
                </h2>
                <div class="panel-toolbar">
                    <button class="btn btn-panel" data-action="panel-collapse" data-toggle="tooltip" data-offset="0,10" data-original-title="Collapse"></button>
                    <button class="btn btn-panel" data-action="panel-fullscreen" data-toggle="tooltip" data-offset="0,10" data-original-title="Fullscreen"></button>
                    {{-- <button class="btn btn-panel" data-action="panel-close" data-toggle="tooltip" data-offset="0,10" data-original-title="Close"></button> --}}
                </div>
            </div>
            <div class="panel-container show">
                <button type="button" id="btn-add" class="btn btn-primary float-right m-3" data-toggle="modal" data-target="#default-example-modal">Add Flavour Category</button>
                <div class="panel-content" >
                    <div id="categoryData">
                        <table id="flavourcategory-table" class="table table-bordered table-hover table-striped w-100 dataTable dtr-inline">
                            <thead class="bg-primary-600">
                                <tr>
                                    <th>Id </th>
                                    <th>Name </th>
                                     <th>Action</th>
                                </tr>
                            </thead>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @include('admin.flavourCategories.create')
</div>
@endsection

@section('page_js')
<script type="text/javascript">
    $(document).ready(function(){
        $('#btn-add').click(function () {
            $('#catForm').trigger("reset");
        });
        
        var table =  $('#flavourcategory-table').DataTable(
                {
                    responsive: true,
                    serverSide: true,
                    ajax: "{{ route('admin.flavour.category.list') }}",
                    columns: [
                        {data: 'id', name: 'Id'},
                        {data: 'name', name: 'Name'},
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

        // Clicking the save button on the open modal for both CREATE and UPDATE
        $("#save").click(function (e) {
            var formData = {
                cat_id: $('#cat_id').val(),
                name: $('#name').val(),
            };
            var ajaxurl = "{{ route('admin.flavour.category.store') }}";
            $.ajax({
                type: "POST",
                url: ajaxurl,
                data: formData,
                dataType: 'json',
                headers: {
                    'X-CSRF-TOKEN': "{{csrf_token()}}"
                },
                success: function (data) {
                    if(data.status == 'success')
                    {
                        toastr['success']('Save successfully!');
                        $('#default-example-modal').modal('hide');
                        $('#catForm').trigger("reset");
                        table.ajax.reload( null, false);
                    }else{
                        toastr['error'](data.message);
                    }
                },
                error: function (data) {
                    toastr['error']('Something went wrong, Please try again!');
                    console.log('Error:', data);
                }
            });
        });

        $(document).on("click", ".edit-cat" , function () {
            var ajaxurl = $(this).data('url');
            $.ajax({
                type: "GET",
                url: ajaxurl,
                dataType: 'json',
                headers: {
                    'X-CSRF-TOKEN': "{{csrf_token()}}"
                },
                success: function (data) {
                    $('#catForm').trigger("reset");
                    $('#name').val(data.data.name);
                    $('#cat_id').val(data.data.id);
                },
                error: function (data) {
                    console.log('Error:', data);
                }
            });
            
        });

        $(document).on("click", ".delete-cat" , function () {
            var cat_id = $(this).data('url');
            var ajaxurl =  $(this).data('url');
                Swal.fire(
                {
                    title: "Are you sure?",
                    text: "You won't be able to revert this!",
                    type: "warning",
                    showCancelButton: true,
                    confirmButtonText: "Yes, delete it!"
                }).then(function(result)
                {
                    if (result.value)
                    {
                        $.ajax({
                            type: "POST",
                            url: ajaxurl,
                            dataType: 'json',
                            headers: {
                                'X-CSRF-TOKEN': "{{csrf_token()}}"
                            },
                            success: function (data) {
                                toastr['success']('Deleted successfully!');
                                table.ajax.reload( null, false);
                            },
                            error: function (data) {
                                toastr['error']('Something went wrong, Please try again!');
                                console.log('Error:', data);
                            }
                        });
                    }
                });
        });

    });

</script>
@endsection