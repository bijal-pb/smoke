        @foreach($flavourCats as $row)
        <tr>
            <td> {{ $row->id }}
            <td> {{ $row->name }} </td>
            <td>
                <button class="edit-cat btn btn-outline-warning btn-sm btn-icon"" data-toggle="modal" data-target="#default-example-modal" data-id="{{ $row->id }}"><i class="fal fa-pencil"></i></button>  
                <button class="delete-cat btn btn-outline-danger btn-sm btn-icon"" data-id="{{ $row->id }}"><i class="fal fa-trash"></i></button>
            </td>
        </tr>
        @endforeach
        <tr>
            <td colspan="3"> 
                <div class="row">
                    <div class="col-sm-12 col-md-6">
                        <div class="dataTables_info" id="dt-basic-example_info" role="status" aria-live="polite">Showing {{ $flavourCats->firstItem()}} to {{ $flavourCats->lastItem()}} of {{ $flavourCats->total() }} Flavour categories</div>
                    </div>
                    <div class="col-sm-12 col-md-6 text-right pg">
                        {{ $flavourCats->links('pagination::bootstrap-4') }}
                    </div>
                </div>
            </td>
        </tr>