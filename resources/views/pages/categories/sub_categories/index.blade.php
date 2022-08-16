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
            {{$content_header}} of <b>{{$category->name_en}}</b>
        </div>
        <div class="row wrapper">
            <div class="col-sm-5 m-b-xs">
                <a href="{{ route('admin.categories.index') }}" class="btn m-b-xs btn-sm btn-default btn-addon">
                    <i class="fa fa-arrow-circle-left"></i>Back
                </a>
                @if(in_array('add',auth()->user()->role->hasAccess($section_name)))
                    <a href="{{ route('admin.categories.sub_categories.create', [$category->id]) }}" class="btn m-b-xs btn-sm btn-default btn-addon">
                        <i class="fa fa-plus"></i>Add {{$content_header}}
                    </a>
                @endif
            </div>

            <div class="col-sm-3">
            </div>
            <div class="col-sm-4">
            </div>
        </div>
        <div class="table-responsive">
            <table  id="table" class="table table-hover b-t b-light">
                <thead>
                <tr>
                    <th>Order</th>
                    <th>Name English</th>
{{--                    <th>Name Arabic</th>--}}
{{--                    <th>Description English</th>--}}
{{--                    <th>Description Arabic</th>--}}
{{--                    <th>Image</th>--}}
{{--                    <th>Is Featured ?</th>--}}
                    <th>Status</th>
                    <th>Created At</th>
                    <th></th>
                </tr>
                </thead>
                <tbody>
                    @foreach($items as $item)
                    <tr>

                        <td># {{ $item->item_order }}</td>
                        <td>{{ ucwords($item->name_en) }}</td>
{{--                        <td>{{ $item->name_ar }}</td>--}}
{{--                        <td>{!! $item->description_en !!}</td>--}}
{{--                        <td>{!! $item->description_ar !!}</td>--}}
{{--                        <td>--}}
{{--                            @if($item->getMediaName() != null)--}}
{{--                                <img src="{{$item->image}}" width="100px" data-toggle="modal" data-target="#myModal_{{$item->id}}"  style="cursor: pointer;"/>--}}
{{--                                <div id="myModal_{{$item->id}}" class="modal fade" role="dialog">--}}
{{--                                    <div class="modal-dialog">--}}
{{--                                        <div class="modal-content">--}}
{{--                                            <div class="modal-header">--}}
{{--                                                <button type="button" class="close" data-dismiss="modal">&times;</button>--}}
{{--                                            </div>--}}
{{--                                            <div class="modal-body">--}}
{{--                                                <img src="{{$item->image}}" width="100%;">--}}
{{--                                            </div>--}}
{{--                                            <div class="modal-footer">--}}
{{--                                                <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>--}}
{{--                                            </div>--}}
{{--                                        </div>--}}

{{--                                    </div>--}}
{{--                                </div>--}}
{{--                            @endif--}}
{{--                        </td>--}}
{{--                        <td>--}}
{{--                            @if($item->is_featured)--}}
{{--                                <h4><span class="label label-success">YES</span></h4>--}}
{{--                            @else--}}
{{--                                <h4><span class="label label-warning">NO</span></h4>--}}
{{--                            @endif--}}
{{--                        </td>--}}
                        <td>
                            @if($item->is_published)
                                <h4><span class="label label-success">Published</span></h4>
                            @else
                                <h4><span class="label label-danger">Un-published</span></h4>
                            @endif
                        </td>
                        <td>{{ \Carbon\Carbon::createFromTimestamp($item->created_at)->format('Y-m-d H:i:s') }}</td>
                        <td>
                            @if(in_array('publish',auth()->user()->role->hasAccess($section_name)))
                                @if($item->is_published)
                                    <a href="{{ route('admin.categories.sub_categories.publish', $item->id) }}" class="btn btn-md btn-action">
                                        Un-publish
                                    </a>
                                @else
                                    <a href="{{ route('admin.categories.sub_categories.publish', $item->id) }}" class="btn btn-md btn-action">
                                        Publish
                                    </a>
                                @endif
                            @endif
                            @if(in_array('edit',auth()->user()->role->hasAccess($section_name)))
                                <a href="{{ route('admin.sub_categories.supplier_sub_categories.index',$item->id) }}" class="btn btn-xs btn-action"><i class="fa fa-arrows-alt"> View 2nd level Categories</i></a>
{{--                                @if($item->category->is_supplier)--}}
{{--                                    <a href="{{ route('admin.sub_categories.supplier_sub_categories.index',$item->id) }}" class="btn btn-xs btn-primary"><i class="fa fa-arrows-alt">View 1st level Categories</i></a>--}}
{{--                                @endif--}}
{{--                                <a href="{{ route('admin.categories.sub_categories.edit', [$category->id,$item->id]) }}" class="btn btn-xs btn-action"><i class="fa fa-edit"> Edit</i></a>--}}
                            @endif
                            @if(in_array('delete',auth()->user()->role->hasAccess($section_name)))
{{--                                <form action="{{ secure_url('admin/categories/'.$category->id.'/sub_categories/'. $item->id) }}" method="POST" class="pull-left delete" style="margin-right: 3px">--}}
{{--                                    {{ csrf_field() }}--}}
{{--                                    {{ method_field('DELETE') }}--}}

{{--                                    <button type="submit" class="btn btn-xs btn-danger">--}}
{{--                                        <i class="fa fa-trash"></i> Delete--}}
{{--                                    </button>--}}
{{--                                </form>--}}
                            @endif
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
            $('#table').dataTable({
                "bPaginate": true,
                "bLengthChange": true,
                "pageLength": 50,
                "bFilter": true,
                "bInfo": true,
                "ordering": false,
                "bAutoWidth": true,
                "pagingType": "full_numbers",
                dom: 'Bfrtip',
                buttons: [
                    'copy', 'csv', 'excel', 'print'
                ]
            });
        } );

    </script>
@endsection
