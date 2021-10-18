@extends('vendor.layout')

@section('content')
    <link rel="stylesheet" type="text/css" href="https://rawgit.com/wenzhixin/bootstrap-table/master/src/bootstrap-table.css"/>
    <link rel="stylesheet" type="text/css" href="https://rawgit.com/wenzhixin/bootstrap-table/master/src/extensions/reorder-rows/bootstrap-table-reorder-rows.css"/>

    <div class="panel panel-default">
        <div class="panel-heading">
            Redeem Items
        </div>
        <div class="row wrapper">
            <div class="col-sm-5 m-b-xs">
                <a href="{{ route('admin.redeem_items.create') }}" class="btn m-b-xs btn-sm btn-default btn-addon">
                    <i class="fa fa-plus"></i>Add Item
                </a>
            </div>
            <div class="col-sm-3">
            </div>
        </div>
        <div class="table-responsive">
            <table id="table" data-toggle="table"
                   data-use-row-attr-func="true"
                   data-reorderable-rows="true" class="table table-striped b-t b-light">
                <thead>
                <tr>
                    <th>ID</th>
                    <th>Title</th>
                    {{--<th>Description</th>--}}
                    <th>Points</th>
                    <th>Quantity</th>
                    <th>Status</th>
                    <th>Image</th>
                    <th>Date Added</th>
                    <th></th>
                </tr>
                </thead>
                <tbody>
                    @foreach($items as $item)
                    <tr>
                        <td>{{ $item->id }}</td>
                        <td>{{ $item->title }}</td>
                        {{--<td>{{ $item->description }}</td>--}}
                        <td><h3>{{ $item->points }}</h3></td>
                        <td>{{ $item->quantity }}</td>
                        <td>
                            @if($item->is_activated == 0)
                                <span class="label label-warning"> Not Activated</span>
                            @else
                                <span class="label label-success"> Activated</span>
                            @endif
                        </td>
                        <td>
                            @if($item->image != null)
                                <img src="{{secure_url('public/uploads/images/redeem')}}{{DIRECTORY_SEPARATOR}}{{$item->image}}" width="100px" data-toggle="modal" data-target="#myModal_{{$item->id}}"  style="cursor: pointer;"/>
                                <div id="myModal_{{$item->id}}" class="modal fade" role="dialog">
                                    <div class="modal-dialog">
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                <button type="button" class="close" data-dismiss="modal">&times;</button>
                                            </div>
                                            <div class="modal-body">
                                                <img src="{{secure_url('public/uploads/images/redeem')}}{{DIRECTORY_SEPARATOR}}{{$item->image}}" width="100%;">
                                            </div>
                                            <div class="modal-footer">
                                                <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                                            </div>
                                        </div>

                                    </div>
                                </div>
                            @endif
                        </td>
                        <td>{{ $item->date }}</td>
                        <td>
                            @if($item->is_activated == 0)
                                <a href="{{ route('admin.redeem_items.toggle', $item->id) }}" class="btn btn-xs btn-success">
                                    Activate
                                </a>
                            @else
                                <a href="{{ route('admin.redeem_items.toggle', $item->id) }}" class="btn btn-xs btn-default">
                                    Deactivate
                                </a>
                            @endif
                            <a href="{{ route('admin.redeem_items.edit', $item->id) }}" class="btn btn-xs btn-info"><i class="fa fa-edit"> Edit</i></a>
                            <form action="{{ secure_url('admin/redeem_items/'. $item->id) }}" method="POST" class="pull-left delete" style="margin-right: 3px">
                                {{ csrf_field() }}
                                {{ method_field('DELETE') }}

                                <button type="submit" class="btn btn-xs btn-danger">
                                    <i class="fa fa-trash"></i> Delete
                                </button>
                            </form>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
@endsection
@section('scripts')
    @parent
    <script src="https://rawgit.com/wenzhixin/bootstrap-table/master/src/bootstrap-table.js" type="text/javascript"></script>
    <script src="https://rawgit.com/isocra/TableDnD/master/js/jquery.tablednd.js" type="text/javascript"></script>
    <script src="https://rawgit.com/wenzhixin/bootstrap-table/master/src/extensions/reorder-rows/bootstrap-table-reorder-rows.js" type="text/javascript"></script>

    <script>
        $(".delete").on("submit", function(){
            return confirm("Are you sure you want to delete this?");
        });
        $(document).ready(function() {
            $('.table').dataTable({
                "bPaginate": true,
                "bLengthChange": true,
                "pageLength": 50,
                "bFilter": true,
                "bInfo": true,
                "ordering": false,
                "bAutoWidth": true,
                "pagingType": "full_numbers"
            });
        } );
        var $table = $('#table');
        $(function () {
            $table.on('reorder-row.bs.table', function (e, data){
                var http = new XMLHttpRequest();
                var url = window.location.href + "/order/items";
                http.open("POST", url, true);
                var queryString = "ids=";
                for(var i=0;i<data.length;i++){
                    queryString += data[i][0];
                    //Append an & except after the last element
                    if(i < data.length - 1) {
                        queryString += ",";
                    }
                }
                //Send the proper header information along with the request
                var csrfToken = "{{ csrf_token() }}";
                http.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
                http.setRequestHeader('X-CSRF-TOKEN', csrfToken);
                http.onreadystatechange = function() {//Call a function when the state changes.
                    if(http.readyState == 4 && http.status == 200) {
                        console.log(http.responseText);
                        if(http.responseText == 'ok'){

                        }
                    }
                }
                http.send(queryString);

            });
        });
    </script>
@endsection

