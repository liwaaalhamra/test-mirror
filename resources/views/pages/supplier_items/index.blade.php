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
                        <a href="{{ route('admin.export_supplier_items') }}" class="btn btn-lg btn-info"><i class="fa fa-edit"> Export All Items</i></a>
                    </div>
                </div>
            </div>
        @endif
        @if(in_array('export',auth()->user()->role->hasAccess($section_name)))
            <div class="col-sm-3">
                <div class="panel panel-default">
                    <div class="panel-heading font-bold">Import Excels</div>
                    <div class="panel-body">
                        <form action="{{ route('admin.import_supplier_items') }}" method="POST" enctype="multipart/form-data">
                            {{ csrf_field() }}
                            <input type="file" name="excel" />
                            <button type="submit" class="btn m-b-xs btn-sm btn-success btn-addon">
                                <i class="fa fa-upload"></i>Import Items
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
                        <a href="{{ route('admin.supplier_items.create') }}" class="btn m-b-xs btn-sm btn-default btn-addon">
                            <i class="fa fa-plus"></i>Add {{$content_header}}
                        </a>
                    @endif
                </div>
            </div>
            <div class="row wrapper">
                <form action="{{ route('admin.items.index') }}" method="get" role="search" id="filter_form" >
                    <div class="col-sm-2 m-b-xs">
                        <label><strong>Filter by Supplier</strong></label>
                        <select class="form-control selectpicker" id="category_id" data-live-search="true" name="category_id" >
                            <option data-tokens="" value="" selected>All</option>
                            @foreach($categories as $category)
                                <option data-tokens="{{$category->name_en}}" value="{{$category->id}}" @if($selected_category == $category->id) selected @endif > {{$category->name_en}}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-sm-2 m-b-xs">
                        <label><strong>Filter by Menu Category</strong></label>
                        <select class="form-control selectpicker" id="sub_category_id" data-live-search="true" name="sub_category_id" >
                            <option data-tokens="" value="" selected>All</option>
                            @foreach($sub_categories as $sub_category)
                                <option data-tokens="{{$sub_category->name_en}}" value="{{$sub_category->id}}" @if($selected_sub_category == $sub_category->id) selected @endif > {{$sub_category->name_en}}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-sm-2 m-b-xs">
                        <label>Date From</label>
                        <div class='input-group date' id='from'>
                            <input type='text' name="min" id="min" class="form-control" />
                            <span class="input-group-addon">
                                <span class="glyphicon glyphicon-calendar"></span>
                            </span>
                        </div>
                    </div>
                    <div class="col-sm-2 m-b-xs">
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
                </form>
            </div>
        </div>
        <div class="table-responsive">
            <table id="table" class="table table-hover b-t b-light">
                <thead>
                <tr>
                    <th>Order</th>
                    <th>SKU</th>
                    <th>Name</th>
                    <th>Supplier</th>
                    <th>Menu Category</th>
                    <th>Unit Type</th>
                    <th>Unit Value</th>
                    <th>Price</th>
                    <th>Discounted Price</th>
                    <th>Quantity</th>
                    <th>Description</th>
                    <th>Image</th>
                    <th>Share Link</th>
                    <th>Status</th>
                    <th>Expiry Date</th>
                    <th>Date Added</th>
                    <th></th>
                </tr>
                </thead>
                <tbody>
                @foreach($items as $item)
                    <tr >
                        <td>#SHOP {{ $item->item_order }}</td>
                        <td>{{ $item->code }}</td>
                        <td>{{ ucwords($item->name_en) }}
                            <br>{{ ucwords($item->name_ar) }}
                        </td>
                        <td>
                            @if($item->supplier_food_group)
                                {{ ucwords($item->supplier_food_group->name_en) }}
                                <br>{{ ucwords($item->supplier_food_group->name_ar) }}
                            @else
                                Supplier Not Assigned yet
                            @endif
                        </td>
                        <td>
                            @if($item->sub_category)
                                {{ ucwords($item->sub_category->name_en) }}<br>{{ ucwords($item->sub_category->name_ar) }}
                            @else
                                Sub Category Not Assigned yet
                            @endif
                        </td>
                        <td>{{ ucwords($item->unit_type) }}</td>
                        <td>{{ ucwords($item->unit_value) }}</td>
                        <td>{{ $item->price_formatted }} </td>
                        <td>{{ $item->discounted_price_formatted }}</td>
                        <td>{{ $item->quantity }} Piece(s)</td>
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
                            @if($item->share_link)
                                <h3>
                                <span class="label label-success">
                                    <a target="_blank" href="{{$item->share_link}}"> <i aria-hidden="true" style="display: inline-block" class="fa fa-share-alt"></i> Share Link</a>
                                </span>
                                </h3>
                            @endif
                        </td>
                        <td>
                            @if($item->is_available)
                                <h4><span class="label label-success">Available</span></h4>
                            @else
                                <h4><span class="label label-warning">Not Available</span></h4>
                            @endif
                        </td>

                        <td>@if($item->expiry_date){{ \Carbon\Carbon::createFromTimestamp($item->expiry_date)->format('Y-m-d H:i:s') }}@endif</td>
                        <td>{{ \Carbon\Carbon::createFromTimestamp($item->created_at)->format('Y-m-d H:i:s') }}</td>
                        <td>
                            @if(in_array('publish',auth()->user()->role->hasAccess($section_name)))
                            @if($item->is_available)
                                <a href="{{ route('admin.supplier_items.publish', $item->id) }}" class="btn btn-md btn-warning">
                                    Set As Not Available
                                </a>
                            @else
                                <a href="{{ route('admin.supplier_items.publish', $item->id) }}" class="btn btn-md btn-success">
                                    Set As Available
                                </a>
                            @endif
                            @endif
                            @if(in_array('edit',auth()->user()->role->hasAccess($section_name)))
                                <a href="{{ route('admin.supplier_items.supplier_customizations.index',$item->id) }}" class="btn btn-xs btn-primary"><i class="fa fa-arrows-alt"> Manage Customizations</i></a>
                                <a href="{{ route('admin.supplier_items.edit', $item->id) }}" class="btn btn-md btn-action"><i class="fa fa-edit"> Edit</i></a>
                            @endif
                            @if(in_array('delete',auth()->user()->role->hasAccess($section_name)))
                            <form action="{{ secure_url('admin/supplier_items/'. $item->id) }}" method="POST" class="pull-left delete" style="margin-right: 3px">
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
            });
        } );
        @endif


    </script>
@endsection

