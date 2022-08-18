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
            <div class="col-sm-3">
                <div class="panel panel-default">
                    <div class="panel-heading font-bold">Export Excels</div>
                    <div class="panel-body">
                        <a href="{{ route('admin.export_suppliers') }}" class="btn btn-lg btn-info"><i class="fa fa-edit"> Export All {{$content_header}}</i></a>
                    </div>
                </div>
            </div>
        @endif
        @if(in_array('export',auth()->user()->role->hasAccess($section_name)))
            <div class="col-sm-3">
                <div class="panel panel-default">
                    <div class="panel-heading font-bold">Import Excels</div>
                    <div class="panel-body">
                        <form action="{{ route('admin.import_suppliers') }}" method="POST" enctype="multipart/form-data">
                            {{ csrf_field() }}
                            <input type="file" name="excel" />
                            <button type="submit" class="btn m-b-xs btn-sm btn-success btn-addon">
                                <i class="fa fa-upload"></i>Import {{$content_header}}
                            </button>
                        </form>
                    </div>
                </div>
            </div>
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
                        <a href="{{ route('admin.suppliers.create') }}" class="btn m-b-xs btn-sm btn-default btn-addon">
                            <i class="fa fa-plus"></i>Add {{$content_header}}
                        </a>
                    @endif
                </div>
            </div>
            <div class="row wrapper">
                <form action="{{ secure_url('admin/suppliers') }}" method="get" role="search" id="filter_form" >
                    <div class="">
                        <div class="col-sm-4 m-b-xs">
                            <label><strong>Filter by Category</strong></label>
                            <select class="form-control selectpicker" id="category_id" data-live-search="true" data-actions-box="true" name="category_id" >
                                <option data-tokens="" value="" selected>All</option>
                                @foreach($categories as $category)
                                    <option data-tokens="{{$category->name_en}}" value="{{$category->id}}" @if($selected_category == $category->id) selected @endif > {{$category->name_en}}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="">
                        <div class="col-sm-4 m-b-xs">
                            <label>Date From</label>
                            <div class='input-group date' id='from'>
                                <input type='text' name="min" id="min" class="form-control" />
                                <span class="input-group-addon">
                                <span class="glyphicon glyphicon-calendar"></span>
                            </span>
                            </div>
                        </div>
                        <div class="col-sm-4 m-b-xs">
                            <label>Date To</label>
                            <div class='input-group date' id='to'>
                                <input type='text' name="max" id="max" class="form-control" />
                                <span class="input-group-addon">
                                <span class="glyphicon glyphicon-calendar"></span>
                            </span>
                            </div>
                        </div>
                        <div class="col-sm-2 m-b-xs">
                            <button type="submit" class="btn btn-lg btn-action">GO</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
        <div class="table-responsive">
            <table id="table" class="table table-hover b-t b-light">
                <thead>
                <tr>
                    <th>Order</th>
                    <th>Name</th>
                    <th>Category</th>
                    <th>Address</th>
                    <th>Phone Number</th>
                    <th>Share Link</th>
                    <th>Description</th>
                    <th>Main Image</th>
                    <th>PDF</th>
                    <th>Images</th>
                    <th>Is Closed</th>
                    <th>Status</th>
                    <th>Date Added</th>
                    <th></th>
                </tr>
                </thead>
                <tbody>
                @foreach($items as $item)
                    <tr >
                        <td>#{{ $item->item_order }}</td>
                        <td>{{ ucwords($item->name_en) }}
                            <br>{{ ucwords($item->name_ar) }}
                        </td>
                        <td>
                            @if($item->category)
                                {{ ucwords($item->category->name_en) }}
                                <br>{{ ucwords($item->category->name_ar) }}
                            @else
                                Category Not Assigned yet
                            @endif
                        </td>
                        <td>
                            {{$item->address}}<br>
                            @if($item->latitude != 0 && $item->longitude != 0)
                                <h3><span class="label label-info">
                                    <a target="_blank" href="http://maps.google.com/?q={{ $item->latitude }},{{ $item->longitude }}"> <i aria-hidden="true" style="display: inline-block" class="fa fa-street-view"></i>View Location</a>
                                </span>
                                </h3>
                            @endif
                        </td>
                        <td>{{$item->country_code}}{{$item->phone_number}}</td>
                        <td>
                            @if($item->share_link)
                            <h3>
                            <span class="label label-success">
                                <a target="_blank" href="{{$item->share_link}}"> <i aria-hidden="true" style="display: inline-block" class="fa fa-share-alt"></i> Share Link</a>
                            </span>
                            </h3>
                            @endif
                        </td>
                        <td>
                            {!!  $item->description_en !!}<br>
                            {!!  $item->description_ar !!}
                        </td>
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
                        <td>
                            @if($item->getPdfName() != null)
                                <a class="btn btn-primary view-pdf" data-toggle="modal" data-target="#pdfForm{{$item->id}}" >View PDF</a>
                                <div class="modal fade" id="pdfForm{{$item->id}}" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" >
                                    <div class="modal-dialog centerModal" role="document" style="position: fixed;top: 50%;left: 50%;transform: translate(-50%, -50%);">
                                        <div class="modal-content">
                                            <div class="modal-body">
                                                <object type="application/pdf" data="{{$item->pdf}}" width="100%" height="500">No Support</object>
                                            </div>
                                            <div class="modal-footer">
                                                <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endif
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
                            @if($item->is_closed)
                                <h4><span class="label label-danger">YES</span></h4>
                            @else
                                <h4><span class="label label-success">NO</span></h4>
                            @endif
                        </td>
                        <td>
                            @if($item->is_published)
                                <h4><span class="label label-success">Available</span></h4>
                            @else
                                <h4><span class="label label-warning">Not Available</span></h4>
                            @endif
                        </td>
                        <td>{{ \Carbon\Carbon::createFromTimestamp($item->created_at)->format('Y-m-d H:i:s') }}</td>
                        <td>
                            @if(in_array('publish',auth()->user()->role->hasAccess($section_name)))
                            @if(!$item->is_published)
                                <a href="{{ route('admin.suppliers.publish', $item->id) }}" class="btn btn-md btn-success">
                                    Publish
                                </a>
                            @else
                                <a href="{{ route('admin.suppliers.publish', $item->id) }}" class="btn btn-md btn-warning">
                                    Un-publish
                                </a>
                            @endif
                            @endif
                            @if(in_array('edit',auth()->user()->role->hasAccess($section_name)))
                                <a href="{{ route('admin.suppliers.menu.index',$item->id) }}" class="btn btn-xs btn-primary"><i class="fa fa-arrows-alt"> Manage Menu</i></a>
                                <a href="{{ route('admin.suppliers.edit', $item->id) }}" class="btn btn-md btn-action"><i class="fa fa-edit"> Edit</i></a>
                                <a href="{{ route('admin.suppliers.clone', $item->id) }}" class="btn btn-md btn-info"><i class="far fa-clone"> Clone</i></a>
                                <a href="{{ route('admin.suppliers.reset', $item->id) }}" class="btn btn-xs btn-default"><i class="fa fa-unlock"> Reset password</i></a>
                            @endif
                            @if(in_array('delete',auth()->user()->role->hasAccess($section_name)))
                            <form action="{{ secure_url('admin/suppliers/'. $item->id) }}" method="POST" class="pull-left delete" style="margin-right: 3px">
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
                order: [0,'asc'],
                "pagingType": "full_numbers",

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
                order: [0,'asc'],
                "pagingType": "full_numbers",
            });
        } );
        @endif


    </script>
@endsection

