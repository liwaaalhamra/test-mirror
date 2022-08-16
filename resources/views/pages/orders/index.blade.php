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
        <audio controls style="display: none">
            <source  id="source" src="https://bcbd7bf3.ngrok.io/healr/ping.mp3" type="audio/mpeg">
            Your browser does not support the audio element.
        </audio>
        <div class="row wrapper">
            {{--<div class="col-sm-5 m-b-xs">--}}
                {{--<a href="{{ route('admin.users.create') }}" class="btn m-b-xs btn-sm btn-default btn-addon">--}}
                    {{--<i class="fa fa-plus"></i>Add {{$content_header}}--}}
                {{--</a>--}}
            {{--</div>--}}
            <form action="{{ secure_url('admin/orders') }}" method="get" role="search" id="filter_form" >
                <div class="col-sm-12">
                    <div class="col-sm-3 m-b-xs">
                        <label><strong>Filter by User</strong></label>
                        <select class="form-control selectpicker" id="user_id" data-live-search="true" name="user_id" >
                            <option data-tokens="" value="" selected>All</option>
                            @foreach($users as $user)
                                <option data-tokens="{{$user->first_name}} {{$user->last_name}}" value="{{$user->id}}" @if($selected_user == $user->id) selected @endif >{{$user->first_name}} {{$user->last_name}} @if($user->primary_phone) [{{$user->country_code}}{{$user->primary_phone}}] @endif @if($user->email) [{{$user->email}}] @endif</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-sm-3 m-b-xs">
                        <label><strong>Filter by Restaruant</strong></label>
                        <select class="form-control selectpicker" id="restaurant_id" data-live-search="true" name="restaurant_id" >
                            <option data-tokens="" value="" selected>All</option>
                            @foreach($restaurants as $restaurant)
                                <option data-tokens="{{$restaurant->name_en}} {{$restaurant->name_ar}}" value="{{$restaurant->id}}" @if($selected_restaurant == $restaurant->id) selected @endif >{{$restaurant->name_en}} </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-sm-3 m-b-xs">
                        <label><strong>Filter by Supplier</strong></label>
                        <select class="form-control selectpicker" id="supplier_id" data-live-search="true" name="supplier_id" >
                            <option data-tokens="" value="" selected>All</option>
                            @foreach($suppliers as $supplier)
                                <option data-tokens="{{$supplier->name_en}} {{$supplier->name_ar}}" value="{{$supplier->id}}" @if($selected_supplier == $supplier->id) selected @endif >{{$supplier->name_en}} </option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="col-sm-12">
                    <div class="col-sm-3 m-b-xs">
                        <label><strong>Filter by Status</strong></label>
                        <select class="form-control selectpicker" id="status" data-live-search="true" name="status" >
                            <option data-tokens="" value="" selected>All</option>
                            @foreach($status as $single_status)
                                <option data-tokens="{{$single_status->label}}" value="{{$single_status->status}}" @if($selected_status == $single_status->status) selected @endif > {{ucwords($single_status->label)}}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-sm-3 m-b-xs">
                        <label><strong>Filter by Payment Type</strong></label>
                        <select class="form-control selectpicker" id="payment_type" data-live-search="true" name="payment_type" >
                            <option data-tokens="" value="" selected>All</option>
                            @foreach($payment_types as $payment_type)
                                <option data-tokens="{{$payment_type}}" value="{{$payment_type}}" @if($selected_payment_type == $payment_type) selected @endif > {{ucwords($payment_type)}}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-sm-3 m-b-xs">
                        <label><strong>Filter by Order Type</strong></label>
                        <select class="form-control selectpicker" id="payment_type" data-live-search="true" name="merchant_type" >
                            <option data-tokens="" value="" selected>All</option>
                            @foreach($merchant_types as $merchant_type)
                                <option data-tokens="{{$merchant_type}}" value="{{$merchant_type}}" @if($selected_merchant_type == $merchant_type) selected @endif > {{ucwords($merchant_type)}}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="col-sm-12">
                    <div class="col-sm-3 m-b-xs">
                        <label>Date From</label>
                        <div class='input-group date' id='from'>
                            <input type='text' name="min" id="min" class="form-control" />
                            <span class="input-group-addon">
                            <span class="glyphicon glyphicon-calendar"></span>
                        </span>
                        </div>
                    </div>
                    <div class="col-sm-3 m-b-xs">
                        <label>Date To</label>
                        <div class='input-group date' id='to'>
                            <input type='text' name="max" id="max" class="form-control" />
                            <span class="input-group-addon">
                            <span class="glyphicon glyphicon-calendar"></span>
                        </span>
                        </div>
                    </div>
                    <div class="col-sm-3 m-b-xs">
                        <button type="submit" class="btn btn-lg btn-action">GO</button>
                    </div>
                </div>

            </form>
        </div>
        <div class="table-responsive">
            <table id="table" class="table b-t b-light">
                <thead>
                <tr>
                    <th>Code</th>
                    <th>Type</th>
                    <th>Merchant</th>
                    <th>User Info</th>
                    <th>Status</th>
                    <th>Payment Type</th>
                    <th>Location Info</th>
                    <th>Total Price</th>
                    <th>Delivery Charge</th>
                    <th>Coupon Amount</th>
                    <th>Discount Amount</th>
                    <th>Bring Change</th>
                    <th>With Cutlery</th>
                    <th>Notes</th>
                    {{--<th>Order DateTime</th>--}}
                    <th>Date Added</th>
                    <th></th>
                </tr>
                </thead>
                <tbody>
                @foreach($orders as $order)
                    <tr class="{{$order->status_info->color}}">
                        <td>{{ $order->code }}</td>
                        <td>{{ $order->merchant_type }}</td>
                        <td>
                            @if($order->merchant)
                                {{$order->merchant->name_en}}
                            @endif
                        </td>
                        <td>
                            @if($order->user)
                                First Name: <b>{{$order->user->first_name}}</b><br>
                                Last Name: <b>{{$order->user->last_name}}</b><br>
                                Phone Number: <b>{{$order->user->primary_phone}}</b><br>
                                Email: <b>@if($order->user->email) {{$order->user->email}} @else n/a @endif</b><br>
                                Gender: <b>@if($order->user->gender) {{ ucwords($order->user->gender)}} @else n/a @endif</b><br>
                                DOB: <b>@if($order->user->dob) {{ ucwords($order->user->dob)}} @else n/a @endif</b><br>
                            @else
                                N/A
                            @endif
                        </td>
                        <td>
                            <h4>{{$order->status}}</h4>
                        </td>
                        <td>{{ ucwords($order->payment_type) }}</td>
                        <td>
                            Region: <b>@if($order->region){{$order->region->name_en}}@endif</b><br>
                            City: <b>@if($order->city){{$order->city->name_en}}@endif</b><br>
                            Tag: <b>{{$order->tag}}</b><br>
                            Nickname: <b>{{$order->nickname}}</b><br>
                            Building: <b>{{$order->building}}</b><br>
                            Street: <b>{{$order->street}}</b><br>
                            Floor: <b>{{$order->floor}}</b><br>
                            Address: <b>{{$order->raw_address}}</b><br>
                            @if($order->latitude != 0 && $order->longitude != 0)
                                <span class="label label-info">
                                    <a target="_blank" href="http://maps.google.com/?q={{ $order->latitude }},{{ $order->longitude }}"> <i aria-hidden="true" style="display: inline-block" class="fa fa-street-view"></i>view Location</a>
                                </span>
                            @endif
                        </td>
                        <td>{{ $order->total_price }} {{$order->currency}}</td>
                        <td>@if($order->delivery_charge){{ $order->delivery_charge }} @else 0 @endif {{$order->currency}}</td>
                        <td>@if($order->coupon_amount){{ $order->coupon_amount }} @else 0 @endif {{$order->currency}}</td>
                        <td>@if($order->discount_amount){{ $order->discount_amount }} @else 0 @endif {{$order->currency}}</td>
                        <td>
                            @if($order->bring_change)
                                <h4><span class="label label-success">YES</span></h4>
                            @else
                                <h4><span class="label label-danger">NO</span></h4>
                            @endif
                        </td>
                        <td>
                            @if($order->with_cutlery)
                                <h4><span class="label label-success">YES</span></h4>
                            @else
                                <h4><span class="label label-danger">NO</span></h4>
                            @endif
                        </td>
                        <td>@if($order->notes){{ $order->notes }} @else n/a @endif</td>
{{--                        <td>@if($order->order_date != null){{ \Carbon\Carbon::createFromTimestamp($order->order_date)->format('Y-m-d') }} / {{$order->order_time}}@endif</td>--}}
                        <td>{{ \Carbon\Carbon::createFromTimestamp($order->created_at)->format('Y/m/d H:i:s') }}</td>
                        <td>
                            @if(in_array('publish',auth()->user()->role->hasAccess($section_name)))
                                {{--<a href="{{ $order->tracking_link }}" target="_blank" class="btn btn-xs btn-success">--}}
                                    {{--<i class="fas fa-map">  Track</i>--}}
                                {{--</a>--}}
                                {{--<br>--}}
                                {{--<br>--}}
                                {{--<a href="{{route('admin.orders.invoice',$order->id)}}" class="btn btn-xs btn-danger">--}}
                                    {{--<i class="fas fa-receipt"> Show Invoice</i>--}}
                                {{--</a>--}}
                                {{--@foreach($order_status as $status)--}}
                                    {{--<a href="{{ secure_url('admin/orders/'.$order->id.'/status/'.$status->status) }}" class="btn btn-xs btn-{{$status->color}}">--}}
                                        {{--{{$status->label}}--}}
                                    {{--</a>--}}
                                {{--@endforeach--}}
                            @endif
                            <br>
                            <br>
                            {{--@if(in_array('delete',auth()->user()->role->hasAccess($section_name)))--}}
                            {{--<form action="{{ secure_url('admin/orders/'. $order->id) }}" method="POST" class="pull-left delete" style="margin-right: 3px">--}}
                                {{--{{ csrf_field() }}--}}
                                {{--{{ method_field('DELETE') }}--}}

                                {{--<button type="submit" class="btn btn-xs btn-primary">--}}
                                    {{--<i class="fa fa-trash"></i> Delete--}}
                                {{--</button>--}}
                            {{--</form>--}}
                            {{--@endif--}}
                            <br>
                            <br>
                            <a href="{{ secure_url('admin/orders/'.$order->id.'/items') }}" class="btn btn-xs btn-default"><i class="fa fa-arrows-alt"> View Order Items</i></a>
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
        $(document).ready(function() {
        @if(in_array('export',auth()->user()->role->hasAccess($section_name)))

                var table = $('#table').dataTable({
                    "bPaginate": true,
                    "bLengthChange": true,
                    "pageLength": 50,
                    "bFilter": true,
                    "bInfo": true,
                    "ordering": true,
                    "bAutoWidth": true,
                    "pagingType": "full_numbers",
                    order:[14,'desc'],
                    dom: 'Bfrtip',
                    buttons: [
                        'copy', 'csv', 'excel', 'pdf', 'print'
                    ]
                });
        @else
                var table = $('#table').dataTable({
                    "bPaginate": true,
                    "bLengthChange": true,
                    "pageLength": 50,
                    "bFilter": true,
                    "bInfo": true,
                    "ordering": true,
                    "bAutoWidth": true,
                    "pagingType": "full_numbers",
                    order:[14,'desc'],
                });
        @endif


            // $.ajax({
            //     url: window.location.href + "/get_orders",
            //     success: function (data, textStatus, jqXHR) {
            //         var table_data = JSON.parse(data);
            //         var table = $('.table').DataTable({
            //             "destroy": true,
            //             data: table_data,
            //             order:[9,'desc'],
            //             dom: 'Bfrtip',
            //             buttons: [
            //                 'copy', 'csv', 'excel', 'pdf', 'print'
            //             ]
            //         });
            //
            //     }
            // });

            function fetchdata() {
                $.ajax({
                    url: window.location.href + "/check_new_order",
                    success: function(result){
                        console.log(result);
                        if(result == 'ok'){
                            $('audio').get(0).load();
                            $('audio').get(0).play();
                        }
                    }});
                // $.ajax({
                //     url: window.location.href + "/get_orders",
                //     success: function (data, textStatus, jqXHR) {
                //         var table_data = JSON.parse(data);
                //         var table = $('.table').DataTable({
                //             "destroy": true,
                //             data: table_data,
                //             order:[9,'desc'],
                //             dom: 'Bfrtip',
                //             buttons: [
                //                 'copy', 'csv', 'excel', 'pdf', 'print'
                //             ]
                //         });
                //
                //     }
                // });
            }

            $(document).ready(function(){
                // setInterval(fetchdata,15000);
            });


        } );
        $(".delete").on("submit", function(){
            return confirm("Are you sure you want to delete this?");
        });

    </script>
@endsection
