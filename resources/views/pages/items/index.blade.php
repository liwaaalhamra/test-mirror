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

    <div class="row">
        @if(in_array('export',auth()->user()->role->hasAccess($section_name)))
{{--            <div class="col-sm-3">--}}
{{--                <div class="panel panel-default">--}}
{{--                    <div class="panel-heading font-bold">Export Excels</div>--}}
{{--                    <div class="panel-body">--}}
{{--                        <a href="{{ route('admin.export_items') }}" class="btn btn-lg btn-info"><i class="fa fa-edit"> Export All Items</i></a>--}}
{{--                    </div>--}}
{{--                </div>--}}
{{--            </div>--}}
        @endif
        @if(in_array('export',auth()->user()->role->hasAccess($section_name)))
{{--            <div class="col-sm-3">--}}
{{--                <div class="panel panel-default">--}}
{{--                    <div class="panel-heading font-bold">Import Excels</div>--}}
{{--                    <div class="panel-body">--}}
{{--                        <form action="{{ route('admin.import_items') }}" method="POST" enctype="multipart/form-data">--}}
{{--                            {{ csrf_field() }}--}}
{{--                            <input type="file" name="excel" />--}}
{{--                            <button type="submit" class="btn m-b-xs btn-sm btn-success btn-addon">--}}
{{--                                <i class="fa fa-upload"></i>Import Items--}}
{{--                            </button>--}}
{{--                        </form>--}}
{{--                    </div>--}}
{{--                </div>--}}
{{--            </div>--}}
        @endif
    </div>

    <div class="panel panel-default">
        <div class="panel-heading">
            {{$content_header}}
        </div>
        <div class="row wrapper">
            <div class="row">
                <div class="col-sm-1 m-b-xs">
                    @if(in_array('add',auth()->user()->role->hasAccess($section_name)))
{{--                        <a href="{{ route('admin.items.create') }}" class="btn m-b-xs btn-sm btn-default btn-addon">--}}
{{--                            <i class="fa fa-plus"></i>Add {{$content_header}}--}}
{{--                        </a>--}}
                    @endif
                </div>
            </div>
            <div class="row wrapper">
                <form action="{{ route('admin.items.index') }}" method="get" role="search" id="filter_form" >
                    <div class="col-sm-2">
                        <label><strong>Filter by Category</strong></label>
                        <select class="form-control selectpicker" id="category_id" data-live-search="true" name="category_id" >
                            <option data-tokens="" value="" selected>All</option>
                            @foreach($categories as $category)
                                <option data-tokens="{{$category->name}}" value="{{$category->id}}" @if($selected_category == $category->id) selected @endif > {{$category->name}}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-sm-2">
                        <label><strong>Filter by 1st level Category</strong></label>
                        <select class="form-control selectpicker" id="sub_category_id" data-live-search="true" name="sub_category_id" >
                            <option data-tokens="" value="" selected>All</option>
                            @foreach($sub_categories as $sub_category)
                                <option data-tokens="{{$sub_category->name}}" value="{{$sub_category->id}}" @if($selected_sub_category == $sub_category->id) selected @endif > {{$sub_category->name}}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-sm-2">
                        <label><strong>Filter by 2nd level Category</strong></label>
                        <select class="form-control selectpicker" id="sub_sub_category_id" data-live-search="true" name="sub_sub_category_id" >
                            <option data-tokens="" value="" selected>All</option>
                            @foreach($sub_sub_categories as $sub_sub_category)
                                <option data-tokens="{{$sub_sub_category->name}}" value="{{$sub_sub_category->id}}" @if($selected_sub_sub_category == $sub_sub_category->id) selected @endif > {{$sub_sub_category->name}}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-sm-2">
                        <label>Date From</label>
                        <div class='input-group date' id='from'>
                            <input type='text' name="min" id="min" class="form-control" />
                            <span class="input-group-addon">
                                <span class="glyphicon glyphicon-calendar"></span>
                            </span>
                        </div>
                    </div>
                    <div class="col-sm-2">
                        <label>Date To</label>
                        <div class='input-group date' id='to'>
                            <input type='text' name="max" id="max" class="form-control" />
                            <span class="input-group-addon">
                                <span class="glyphicon glyphicon-calendar"></span>
                            </span>
                        </div>
                    </div>
                    <div class="col-sm-2">                   
                        <label></label>
                        <div class='input-group'>
                            <button type="submit" class="btn btn-lg btn-action">GO</button>
                        </div>
                    </div>
                </form>
            </div>
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
                    <tr class="@if($item->status == 'approved') success @elseif($item->status == 'pending') default @elseif($item->status == 'expired') warning @elseif($item->status == 'paid') success @endif" >
                        <td>#POST {{ $item->item_order }}</td>
                        <td>{{ $item->User ? $item->User->full_name : "" }}</td>
                        <td>{{ $item->User ? $item->User->email : "" }}</td>
                        <td>{{ $item->code }}</td>
                        <td>{{ ucwords($item->name_en) }}</td>
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
                                    @if($item->status != 'in_review')
                                        <a href="{{ route('admin.items.approve', $item->id) }}" class="btn btn-md btn-action">
                                            Reject
                                        </a>
                                    @else
                                        <a href="{{ route('admin.items.approve', $item->id) }}" class="btn btn-md btn-action">
                                            Approve
                                        </a>
                                    @endif
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
                order: [12,'desc'],
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
                order: [12,'desc'],
            });
        } );
        @endif


    </script>
@endsection

