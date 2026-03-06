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
            {{$content_header}}
        </div>
        <div class="table-responsive">
            <table id="table" class="table b-t b-light">
                <thead>
                <tr>
                    <th>Order</th>
                    <th>User</th>
                    <th>User Email</th>
                    <th>SKU</th>
                    <th>Name</th>
                    <th>Type</th>
                    <th>Category</th>
                    <th>Sub Category</th>
                    <th>Sub Sub Category</th>
                    <th>Price</th>
                    <th>Description</th>
                    <th>Images</th>
                    <th>Status</th>
                    <th>Is Available ?</th>
                    <th>Expiry Date</th>
                    <th>Date Added</th>
                    <th></th>
                </tr>
                </thead>
                <tbody>
                @foreach($items as $item)
                    <tr class="@if($item->status == 'approved') success @elseif($item->status == 'pending') default @elseif($item->status == 'expired') warning @elseif($item->status == 'paid') success @elseif($item->status == 'rejected') danger @endif" >
                        <td>#POST {{ $item->id }}</td>
                        <td>{{ $item->User ? $item->User->full_name : "" }}</td>
                        <td>{{ $item->User ? $item->User->email : "" }}</td>
                        <td>{{ $item->code }}</td>
                        <td>{{ ucwords($item->name_en) }}</td>
                        <td>{{ ucwords($item->type) }}</td>
                        <td>
                            @if($item->category)
                                {{ ucwords($item->category->name) }}
                            @else
                                Category Not Assigned
                            @endif
                        </td>
                        <td>
                            @if($item->sub_category)
                                {{ ucwords($item->sub_category->name) }}
                            @else
                                Sub Category Not Assigned
                            @endif
                        </td>
                        <td>
                            @if($item->sub_sub_category)
                                {{ ucwords($item->sub_sub_category->name) }}
                            @else
                                Sub Category Not Assigned
                            @endif
                        </td>
                        <td>{{ $item->price_formatted }} </td>
                        <td>
                            {!!  $item->description_en !!}
                        </td>
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
                            <h4>{{ucwords($item->status)}}</h4>
                        </td>
                        <td>
                            @if($item->is_available)
                                <h4><span class="label label-success">Online</span></h4>
                            @else
                                <h4><span class="label label-danger">Un-published</span></h4>
                            @endif
                        </td>

                        <td>@if($item->expired_at){{ \Carbon\Carbon::createFromTimestamp($item->expired_at)->format('Y-m-d H:i:s') }}@endif</td>
                        <td>{{ \Carbon\Carbon::createFromTimestamp($item->created_at)->format('Y-m-d H:i:s') }}</td>
                        <td>
                            @if(in_array('publish',auth()->user()->role->hasAccess($section_name)))
                                @if($item->is_available)
                                    <a href="{{ route('admin.items.publish', $item->id) }}" class="btn btn-md btn-action">
                                        Un-publish
                                    </a>
                                @else
                                    <a href="{{ route('admin.items.publish', $item->id) }}" class="btn btn-md btn-action">
                                        Publish
                                    </a>
                                @endif
                                @if($item->status != 'pending')
                                    <a href="{{ route('admin.items.reject', $item->id) }}" class="btn btn-md btn-action">
                                        Reject
                                    </a>
                                @endif
                                @if($item->status != 'pending' && $item->status != 'approved')
                                    <a href="{{ route('admin.items.approve', $item->id) }}" class="btn btn-md btn-action">
                                        Approve
                                    </a>
                                @endif
                            @endif
                            @if(in_array('edit',auth()->user()->role->hasAccess($section_name)))
{{--                                <a href="{{ route('admin.items.customizations.index',$item->id) }}" class="btn btn-xs btn-primary"><i class="fa fa-arrows-alt"> Manage Customizations</i></a>--}}
{{--                                <a href="{{ route('admin.items.edit', $item->id) }}" class="btn btn-md btn-action"><i class="fa fa-edit"> Edit</i></a>--}}
                            @endif
                            @if(in_array('delete',auth()->user()->role->hasAccess($section_name)))
                            <form action="{{ secure_url('admin/items/'. $item->id) }}" method="POST" class="pull-left delete" style="margin-right: 3px">
                                {{ csrf_field() }}
                                {{ method_field('DELETE') }}

                                <button type="submit" class="btn btn-md btn-danger">
                                    <i class="fa fa-trash"></i> Delete
                                </button>
                            </form>
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
        $(function() {
            var from_datetime = "{{$from}}";
            var to_datetime = "{{$to}}";
            if (!from_datetime.trim()) {
                $('#from').datetimepicker({
                    format: 'YYYY/MM/DD'
                });
            } else {
                var from_formatted = "{{\Carbon\Carbon::createFromTimestamp($from)->format('Y-m-d')}}";
                $('#from').datetimepicker({
                    format: 'YYYY/MM/DD',
                    defaultDate: from_formatted
                });
            }
            if (!to_datetime.trim()) {
                $('#to').datetimepicker({
                    format: 'YYYY/MM/DD'
                });
            } else {
                var from_formatted = "{{\Carbon\Carbon::createFromTimestamp($to)->format('Y-m-d')}}";
                $('#to').datetimepicker({
                    format: 'YYYY/MM/DD',
                    defaultDate: from_formatted
                });
            }
        });
        $(".delete").on("submit", function(){
            return confirm("Are you sure you want to delete this?");
        });
        @if(in_array('export',auth()->user()->role->hasAccess($section_name)))
        $(document).ready(function() {
            $('.table').dataTable({
                "bPaginate": true,
                "bLengthChange": true,
                "pageLength": 50,
                "bFilter": true,
                "bInfo": true,
                "ordering": true,
                "bAutoWidth": true,
                "pagingType": "full_numbers",
                order: [16,'desc'],
                dom: 'Bfrtip',
                buttons: [
                    'copy', 'csv', 'excel', 'print'
                ]
            });
        } );
        @else
        $(document).ready(function() {
            $('.table').dataTable({
                "bPaginate": true,
                "bLengthChange": true,
                "pageLength": 50,
                "bFilter": true,
                "bInfo": true,
                "ordering": true,
                "bAutoWidth": true,
                "pagingType": "full_numbers",
                order: [13,'desc'],
            });
        } );
        @endif


    </script>
@endsection

