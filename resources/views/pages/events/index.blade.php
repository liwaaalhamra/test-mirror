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
            {{$content_header}}
        </div>
        <div class="row wrapper">
            <div class="col-sm-5 m-b-xs">
                <a href="{{ route('admin.events.create') }}" class="btn m-b-xs btn-sm btn-default btn-addon">
                    <i class="fa fa-plus"></i>Add {{$content_header}}
                </a>
            </div>
            <div class="col-sm-3">
            </div>
            <div class="col-sm-4">
            </div>
        </div>
        <div class="table-responsive">
            <table id="table" class="table table-striped b-t b-light">
                <thead>
                <tr>
                    <th>Target</th>
                    <th>Name</th>
                    <th>Link</th>
                    <th>Description</th>
                    <th>Short Description</th>
                    <th>DateTime</th>
                    <th>List of Images</th>
                    <th>Location</th>
                    <th>Checkin Points</th>
                    <th>Status</th>
                    <th>Date Added</th>
                    <th></th>
                </tr>
                </thead>
                <tbody>
                    @foreach($items as $item)
                    <tr>
                        <td>{{ ucwords($item->target) }}</td>
                        <td>{{ ucwords($item->name) }}</td>
                        <td>{{ $item->link }}</td>
                        <td>{!! ucwords($item->description) !!}</td>
                        <td>{{ $item->short_description }}</td>
                        <td>{{ \Carbon\Carbon::createFromTimestamp($item->item_datetime)->format('Y-m-d H:i:s') }}</td>
                        <td>
                            @if(count($item->images) > 0)
                                <a title="Image 1" href="#">
                                    <img class="thumbnail img-responsive" id="{{$item->id}}" src="{{ $item->images[0]->media_name }}" style="width: 100px">
                                </a>
                                <div class="hidden" id="img-repo">
                                    @foreach($item->images as $i => $image)
                                        <div class="item" id="{{$item->id}}">
                                            <img class="thumbnail img-responsive" title="Image {{$i+1}}" src="{{ $image->media_name }}" style="width: 100%">
                                        </div>
                                    @endforeach
                                </div>
                                <div class="modal" id="modal-gallery" role="dialog">
                                    <div class="modal-dialog">
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                <button class="close" type="button" data-dismiss="modal">×</button>
                                                <h3 class="modal-title"></h3>
                                            </div>
                                            <div class="modal-body">
                                                <div id="modal-carousel" class="carousel">

                                                    <div class="carousel-inner">
                                                    </div>

                                                    <a class="carousel-control left" href="#modal-carousel" data-slide="prev"><i class="glyphicon glyphicon-chevron-left"></i></a>
                                                    <a class="carousel-control right" href="#modal-carousel" data-slide="next"><i class="glyphicon glyphicon-chevron-right"></i></a>

                                                </div>
                                            </div>
                                            <div class="modal-footer">
                                                <button class="btn btn-default" data-dismiss="modal">Close</button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endif
                        </td>
                        <td>
                            @if($item->latitude != 0 && $item->longitude != 0)
                                <span class="label label-info">
                                    <a target="_blank" href="http://maps.google.com/?q={{ $item->latitude }},{{ $item->longitude }}"> <i aria-hidden="true" style="display: inline-block" class="fa fa-street-view"></i>View Location</a>
                                </span>
                            @endif
                        </td>
                        <td>
                            @if($item->points != null)
                                {{$item->points}} Pts
                            @else
                                n/a
                            @endif
                        </td>
                        <td>
                            @if($item->is_published)
                                <h4><span class="label label-success">Published</span></h4>
                            @else
                                <h4><span class="label label-danger">Un-published</span></h4>
                            @endif
                        </td>
                        <td>{{ \Carbon\Carbon::createFromTimestamp($item->created_at)->format('d-m-Y') }}</td>
                        <td>
                            <a href="{{ route('admin.events.public', $item->id) }}" class="btn btn-md btn-warning">
                                Show To Public
                            </a>
                            <a href="{{ route('admin.events.private', $item->id) }}" class="btn btn-md btn-default">
                                Show To Private
                            </a>
                            @if($item->is_published)
                                <a href="{{ route('admin.events.publish', $item->id) }}" class="btn btn-md btn-warning">
                                    Un-publish
                                </a>
                            @else
                                <a href="{{ route('admin.events.publish', $item->id) }}" class="btn btn-md btn-success">
                                    Publish
                                </a>
                            @endif
                            <a href="{{ route('admin.events.edit', $item->id) }}" class="btn btn-xs btn-action"><i class="fa fa-edit"> Edit</i></a>
                            <form action="{{ secure_url('admin/events/'. $item->id) }}" method="POST" class="pull-left delete" style="margin-right: 3px">
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

