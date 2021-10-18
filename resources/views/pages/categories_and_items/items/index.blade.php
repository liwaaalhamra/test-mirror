@extends('vendor.layout')

@section('content')
    <link rel="stylesheet" type="text/css" href="https://rawgit.com/wenzhixin/bootstrap-table/master/src/bootstrap-table.css"/>
    <link rel="stylesheet" type="text/css" href="https://rawgit.com/wenzhixin/bootstrap-table/master/src/extensions/reorder-rows/bootstrap-table-reorder-rows.css"/>
    <style>
        table.dataTable thead th {
            border-bottom: 0;
        }
        table.dataTable tfoot th {
            border-top: 0;
        }
        table.dataTable.no-footer {
            border-bottom: 0;
        }
    </style>
    <div class="panel panel-default">
        <div class="panel-heading">
            Items of <b>{{$category->name}}</b>
        </div>
        <div class="row wrapper">
            <div class="col-sm-5 m-b-xs">
                <a href="{{ route('admin.categories.index') }}" class="btn m-b-xs btn-sm btn-default btn-addon">
                    <i class="fa fa-arrow-circle-left"></i>Back
                </a>
                <a href="{{ route('admin.categories.items.create', [$category->id]) }}" class="btn m-b-xs btn-sm btn-default btn-addon">
                    <i class="fa fa-plus"></i>Add Item
                </a>
            </div>
            <div class="col-sm-3">
                <form action="#" method="get" role="search">
                    <input type="text" class="input-sm form-control" name="q" @if(\Illuminate\Support\Facades\Input::has('q'))value="{{\Illuminate\Support\Facades\Input::get('q')}}" @endif placeholder="Find by Name or Description...">
                </form>
            </div>
            <div class="col-sm-4">
            </div>
        </div>
        <div class="table-responsive">
            <table  id="table" data-toggle="table"
                    data-use-row-attr-func="true"
                    data-reorderable-rows="true"  class="table table-hover b-t b-light">
                <thead>
                <tr>
                    <th>ID</th>
                    <th>Type</th>
                    <th>Brand</th>
                    <th>Name</th>
                    <th>Description</th>
                    <th>Image</th>
                    <th>Price</th>
                    <th>Dosage</th>
                    <th>Publish Status</th>
                    <th>Created At</th>
                    <th></th>
                </tr>
                </thead>
                <tbody>
                    @foreach($items as $item)
                    <tr>
                        <td>{{ $item->id }}</td>
                        <td>@if($item->type){{ $item->type->meta_value }}@endif</td>
                        <td>@if($item->brand){{ $item->brand->meta_value }}@endif</td>
                        <td>{{ ucwords($item->name) }}</td>
                        <td>{!! ucwords($item->description) !!}</td>
                        <td>
                            @if($item->getMediaName() != null)
                                <img src="{{$item->image}}" width="100px" data-toggle="modal" data-target="#myModal_{{$item->id}}"  style="cursor: pointer;"/>
                                <div id="myModal_{{$item->id}}" class="modal fade" role="dialog">
                                    <div class="modal-dialog">
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                <button type="button" class="close" data-dismiss="modal">&times;</button>
                                            </div>
                                            <div class="modal-body">
                                                <img src="{{$item->image}}" width="100%;">
                                            </div>
                                            <div class="modal-footer">
                                                <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                                            </div>
                                        </div>

                                    </div>
                                </div>
                            @endif
                        </td>
                        <td><h3>{{ $item->price }} LBP</h3></td>
                        <td><h3>{{ $item->dosage }} </h3></td>
                        <td>
                            @if($item->is_available)
                                <h4><span class="label label-success">Available</span></h4>
                            @else
                                <h4><span class="label label-warning">Not Available</span></h4>
                            @endif
                        </td>
                        <td>{{ \Carbon\Carbon::createFromTimestamp($item->created_at)->format('Y-m-d H:i:s') }}</td>
                        <td>
                            @if(!$item->is_available)
                                <a href="{{ route('admin.categories_items.publish', $item->id) }}" class="btn btn-xs btn-success">
                                    Publish
                                </a>
                            @else
                                <a href="{{ route('admin.categories_items.publish', $item->id) }}" class="btn btn-xs btn-default">
                                    UnPublish
                                </a>
                            @endif
                            <a href="{{ route('admin.categories.items.edit', [$category->id,$item->id]) }}" class="btn btn-xs btn-info"><i class="fa fa-edit"> Edit</i></a>
                            <form action="{{ secure_url('admin/categories/'.$category->id.'/items/'. $item->id) }}" method="POST" class="pull-left delete" style="margin-right: 3px">
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
        <footer class="panel-footer">
            <div class="row">
                <div class="col-sm-8"></div>
                <div class="col-sm-4 text-right text-center-xs">
                    @include('pagination.small', ['paginator' => $items])
                </div>
            </div>
        </footer>
    </div>
@endsection
@section('scripts')
    @parent
    <script src="https://rawgit.com/wenzhixin/bootstrap-table/master/src/bootstrap-table.js" type="text/javascript"></script>
    <script src="https://rawgit.com/isocra/TableDnD/master/js/jquery.tablednd.js" type="text/javascript"></script>
    <script src="https://rawgit.com/wenzhixin/bootstrap-table/master/src/extensions/reorder-rows/bootstrap-table-reorder-rows.js" type="text/javascript"></script>

    <script>
        $(document).ready(function() {

            /* activate the carousel */
            $("#modal-carousel").carousel({interval:false});

            /* change modal title when slide changes */
            $("#modal-carousel").on("slid.bs.carousel",       function () {
                $(".modal-title")
                    .html($(this)
                        .find(".active img")
                        .attr("title"));
            });

            /* when clicking a thumbnail */
            $(".thumbnail").click(function(){
                var content = $(".carousel-inner");
                var title = $(".modal-title");

                content.empty();
                title.empty();

                var id = this.id;
                var repo = $("#img-repo .item");
                var repoCopy = repo.filter("#" + id).clone();
                var active = repoCopy.first();

                active.addClass("active");
                title.html(active.find("img").attr("title"));
                content.append(repoCopy);

                // show the modal
                $("#modal-gallery").modal("show");
            });

        });
    </script>
    <script>
        $(".delete").on("submit", function(){
            return confirm("Are you sure you want to delete this?");
        });

        $(document).ready(function() {
            $('#table').dataTable({
                "bPaginate": false,
                "bLengthChange": true,
                "pageLength": 20,
                "bFilter": false,
                "bInfo": true,
                "ordering": false,
                "bAutoWidth": true,
                // "pagingType": "full_numbers",
            });
        } );
        var $table = $('#table');
        $(function () {
            $table.on('reorder-row.bs.table', function (e, data){
                var http = new XMLHttpRequest();
                var url = location.href.split("categories")[0] + "categories/items/order";
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
