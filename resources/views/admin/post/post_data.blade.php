@foreach($posts as $row)
        <tr>
            <td> {{ $row->id }} </td>
            <td> {{ $row->post_by }} </td>
            <td> {{ $row->category }} </td>
            <td> {{ $row->comment }} </td>
            <td> {{ $row->rate }} </td>
            <td>
                <button class="detail-post btn btn-outline-warning btn-sm btn-icon" data-toggle="modal" data-target="#default-example-modal" data-id="{{ $row->id }}"><i class="fal fa-list"></i></button>  
            </td>
        </tr>
        @endforeach
        <tr>
            <td colspan="6">
                <div class="row">
                    <div class="col-sm-12 col-md-6">
                        <div class="dataTables_info" id="dt-basic-example_info" role="status" aria-live="polite">Showing {{ $posts->firstItem()}} to {{ $posts->lastItem()}} of {{ $posts->total() }} Posts</div>
                    </div>
                    <div class="col-sm-12 col-md-6 text-right pg">
                        {{ $posts->links('pagination::bootstrap-4') }}
                    </div>
                </div>
            </td>
        </tr>