        @foreach($flavours as $row)
        <tr>
            <td> {{ $row->id }} </td>
            <td> {{ $row->name }} </td>
            <td> {{ $row->flavour_cat_name }} </td>
            <td>
                <button class="edit-cat btn btn-outline-warning btn-sm btn-icon"" data-toggle="modal" data-target="#default-example-modal" data-id="{{ $row->id }}"><i class="fal fa-pencil"></i></button>  
                <button class="delete-cat btn btn-outline-danger btn-sm btn-icon"" data-id="{{ $row->id }}"><i class="fal fa-trash"></i></button>
            </td>
        </tr>
        @endforeach
        <tr>
            <td colspan="4">
                <div class="row">
                    <div class="col-sm-12 col-md-6">
                        <div class="dataTables_info" id="dt-basic-example_info" role="status" aria-live="polite">Showing {{ $flavours->firstItem()}} to {{ $flavours->lastItem()}} of {{ $flavours->total() }} Flavours</div>
                    </div>
                    <div class="col-sm-12 col-md-6 text-right pg">
                        {{ $flavours->links('pagination::bootstrap-4') }}
                    </div>
                </div>
            </td>
        </tr>