@extends('vendor.layout')

@section('content')
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
                <a href="{{ route('admin.restaurants_cafes.index') }}" class="btn m-b-xs btn-sm btn-default btn-addon">
                    <i class="fa fa-arrow-circle-left"></i>Back
                </a>
                <a href="{{ route('admin.restaurants_cafes.items.create', [$category->id]) }}" class="btn m-b-xs btn-sm btn-default btn-addon">
                    <i class="fa fa-plus"></i>Add Item
                </a>
            </div>
            <div class="col-sm-3">
            </div>
            <div class="col-sm-4">
            </div>
        </div>
        <div class="table-responsive">
            <table class="table table-striped b-t b-light">
                <thead>
                <tr>
                    <th>Name</th>
                    <th>Cuisine Type</th>
                    <th>Level</th>
                    <th>Description</th>
                    <th>List of Images</th>
                    {{--<th>Publish Status</th>--}}
                    <th>Date Added</th>
                    <th></th>
                </tr>
                </thead>
                <tbody>
                    @foreach($items as $item)
                    <tr>
                        <td>{{ ucwords($item->name) }}</td>
                        <td>{{ ucwords($item->cuisine_type) }}</td>
                        <td>{{ ucwords($item->level) }}</td>
                        <td>{!! ucwords($item->description) !!}</td>
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
                        {{--<td>--}}
                            {{--@if($item->is_published)--}}
                                {{--<h4><span class="label label-success">Published</span></h4>--}}
                            {{--@else--}}
                                {{--<h4><span class="label label-warning">Not Published</span></h4>--}}
                            {{--@endif--}}
                        {{--</td>--}}
                        <td>{{ \Carbon\Carbon::createFromTimestamp($item->created_at)->format('Y-m-d H:i:s') }}</td>
                        <td>
                            {{--@if(!$item->is_published)--}}
                                {{--<a href="{{ route('admin.restaurants_cafes.publish', $item->id) }}" class="btn btn-xs btn-success">--}}
                                    {{--Publish--}}
                                {{--</a>--}}
                            {{--@else--}}
                                {{--<a href="{{ route('admin.restaurants_cafes.publish', $item->id) }}" class="btn btn-xs btn-default">--}}
                                    {{--UnPublish--}}
                                {{--</a>--}}
                            {{--@endif--}}
                            <a href="{{ route('admin.restaurants_cafes.items.edit', [$category->id,$item->id]) }}" class="btn btn-xs btn-info"><i class="fa fa-edit"> Edit</i></a>
                            <form action="{{ secure_url('admin/restaurants_cafes/'.$category->id.'/items/'. $item->id) }}" method="POST" class="pull-left delete" style="margin-right: 3px">
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
            $('.table').dataTable({
                "bPaginate": true,
                "bLengthChange": true,
                "pageLength": 50,
                "bFilter": true,
                "bInfo": true,
                "ordering": true,
                "bAutoWidth": true,
                "pagingType": "full_numbers"
            });
        } );
    </script>
@endsection
