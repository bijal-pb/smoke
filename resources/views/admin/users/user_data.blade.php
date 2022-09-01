<!-- datatable start -->

            @foreach($users as $row)
            <tr>
                <td> {{ $row->id }} </td>
                <td> {{ $row->user_name }} </td>
                <td> {{ $row->email }}</td>
                <td> 
                    <div class="custom-control custom-switch">
                        <input type="radio" class="custom-control-input active" data-id="{{ $row->id }}" id="active{{ $row->id }}" name="active{{ $row->id }}" {{ $row->status == 1 ? 'checked' : '' }}>
                        <label class="custom-control-label" for="active{{ $row->id }}"></label>
                    </div>
                </td>
                <td> {{ $row->role }} </td>
                <td>
                    <button class="edit-cat btn btn-outline-warning btn-sm btn-icon"" data-toggle="modal" data-target="#default-example-modal" data-id="{{ $row->id }}"><i class="fal fa-pencil"></i></button>  
                    {{-- <button class="delete-cat btn btn-outline-danger btn-sm btn-icon"" data-id="{{ $row->id }}"><i class="fal fa-trash"></i></button> --}}
                </td>
            </tr>
            @endforeach
            <tr>
                <td colspan="6">
                    <div class="row">
                        <div class="col-sm-12 col-md-6">
                            <div class="dataTables_info" id="dt-basic-example_info" role="status" aria-live="polite">Showing {{ $users->firstItem()}} to {{ $users->lastItem()}} of {{ $users->total() }} users</div>
                        </div>
                        <div class="col-sm-12 col-md-6 float-right text-right pg">
                            {{ $users->links('pagination::bootstrap-4') }}
                        </div>
                    </div>
                </td>
            </tr>
