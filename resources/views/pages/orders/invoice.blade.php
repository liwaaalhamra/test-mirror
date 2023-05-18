<!DOCTYPE html>
<html>

<head>

    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>{{env('APP_NAME')}} | Invoice</title>

    <link href="{{env('APP_ENV') == 'local' ? asset('public/invoice')}}/css/bootstrap.min.css : secure_asset('public/invoice')}}/css/bootstrap.min.css" rel="stylesheet">
    <link href="{{env('APP_ENV') == 'local' ? asset('public/invoice')}}/font-awesome/css/font-awesome.css : secure_asset('public/invoice')}}/font-awesome/css/font-awesome.css" rel="stylesheet">

    <link href="{{env('APP_ENV') == 'local' ? asset('public/invoice')}}/css/animate.css : secure_asset('public/invoice')}}/css/animate.css" rel="stylesheet">
    <link href="{{env('APP_ENV') == 'local' ? asset('public/invoice')}}/css/style1.css : secure_asset('public/invoice')}}/css/style1.css" rel="stylesheet">

</head>

<body>

    <div id="wrapper">
        <div id="page-wrapper" class="gray-bg">

        <div class="row wrapper border-bottom white-bg page-heading ">
                <div class="col-lg-12" style="text-align: center">
                    {{--<h2 style="color: #034759;font-weight: bolder">{{$payment_company}}</h2>--}}
                    <h3>Order INVOICE</h3>
                </div>
                <!--<div class="col-lg-4">-->
                    <!--<div class="title-action">-->
                        <!--<a href="invoice_print.html" target="_blank" class="btn btn-primary"><i class="fa fa-print"></i> Print Invoice </a>-->
                    <!--</div>-->
                <!--</div>-->
        </div>
        <div class="row">
            <div class="ibox-content  wrapper wrapper-content animated fadeInRight" >
                <div class="table-responsive">
                    <table class="table invoice-table">
                        <thead>
                        <tr>
                            <th>VAT #: {{$vat}}</th>
                        </tr>
                        </thead>
                        <tbody>
                        {{--@foreach($services as $service)--}}
                        {{--<tr>--}}
                        {{--<td>{{$service->service_group}} / {{$service->service_type}}</td>--}}
                        {{--<td>$ {{$service->gross_amount}}</td>--}}
                        {{--</tr>--}}
                        {{--@endforeach--}}
                        </tbody>
                    </table>
                </div><!-- /table-responsive -->
                <div class="row">
                    <div class="col-sm-8 col-xs-8">
                        <h4><strong>Invoice #:</strong> <span class="text-navy">{{$invoice_number}}</span></h4>
                        <h4 ><strong>Date Issue:</strong> <span class="text-navy">{{$order_date}}</span></h4>
                        <h4 ><strong>Delivery Date:</strong> <span class="text-navy">{{$delivery_date}}</span></h4>
                        <h4 ><strong>Pickup Date:</strong> <span class="text-navy">{{$pickup_date}}</span></h4>
                        <h4 ><strong>Arrived at partner Date:</strong> <span class="text-navy">{{$arrived_at_partner_date}}</span></h4>
                        <h4 ><strong>Status:</strong> <span class="text-navy">{{$order->status}}</span></h4>
                        <h4 ><strong>Delivery Status:</strong> <span class="text-navy">{{$order->delivery_status}}</span></h4>
                        <h4 ><strong>Payment Type:</strong> <span class="text-navy">{{$order->payment_type}}</span></h4>
                    </div>

                    <div class="col-sm-4 col-xs-4 text-right">
                        <div class="table-responsive">
                            <table class="table invoice-table table-bordered">
                                <thead>
                                <tr>
                                    <th style="text-align: left" colspan="2">Customer Name: <span class="text-navy">@if($user){{$user->first_name}} {{$user->last_name}}@endif</span></th>
                                </tr>
                                <tr>
                                    <th style="text-align: left" colspan="2">Customer Email: <span class="text-navy">@if($user){{$user->email}}@endif</span></th>
                                </tr>
                                <tr>
                                    <th style="text-align: left" colspan="2">Customer Phone Number: <span class="text-navy">@if($user){{$user->country_code}} {{$user->primary_phone}}@endif</span></th>
                                </tr>
                                <tr>
                                    <th style="text-align: left" colspan="2">
                                    Region: <span class="text-navy">@if($order->region) {{$order->region->name_en}} @endif</span><br>
                                    Street: <span class="text-navy">{{$order->street}}</span><br>
                                    Avenue: <span class="text-navy">{{$order->avenue}}</span><br>
                                    Block: <span class="text-navy">{{$order->block}}</span><br>
                                    Apartment: <span class="text-navy">{{$order->apartment}}</span><br>
                                    Building: <span class="text-navy">{{$order->building}}</span><br>
                                    Floor: <span class="text-navy">{{$order->floor}}</span>
                                    </th><br>
                                </tr>
                                </thead>
                                <tbody>
                                </tbody>
                            </table>
                        </div><!-- /table-responsive -->
                    </div>
                </div>
                <div class="row">
                    <br>
                    <br>
                    <div class="col-sm-12 col-xs-12">
                        <div style="font-size: 20px">
                            {!! $message !!}
                        </div>
                    </div>
                </div>
                <div class="table-responsive m-t">
                    <table class="table invoice-table">
                        <thead>
                        <tr>
                            <th style="text-align: center">Item</th>
                            <th style="text-align: center">Description</th>
                            <th style="text-align: center">Category</th>
                            <th style="text-align: center">Brand</th>
                            <th style="text-align: center">Product Type</th>
                            <th style="text-align: center">Cost</th>
                            <th style="text-align: center">Qty</th>
                            <th style="text-align: center">Price</th>

                        </tr>
                        </thead>
                        <tbody style="border-bottom: 1px solid #DDDDDD">
                        @foreach($items as $item)
                            <tr>
                                <td style="text-align: center">{{$item->name_en}}</td>
                                <td style="text-align: center">{{$item->unit_value}} {{$item->unit_type}}</td>
                                <td style="text-align: center">@if($item->category) {{$item->category->name_en}} @endif</td>
                                <td style="text-align: center">@if($item->brand) {{$item->brand->name_en}} @endif</td>
                                <td style="text-align: center">@if($item->product_type) {{$item->product_type->name_en}} @endif</td>
                                <td style="text-align: center">@if($item->discounted_price){{$item->discounted_price_formatted}} @else {{$item->price_formatted}} @endif</td>
                                <td style="text-align: center"> {{$item->quantity_in_cart}}</td>
                                <td>{{$item->final_price_formatted}}</td>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                </div><!-- /table-responsive -->

                <table class="table invoice-total">
                    <tbody>
                    <tr>
                        <td><strong> Sub-Total :</strong></td>
                        <td>{{$sub_total_amount}}</td>
                    </tr>
                    <tr>
                        <td><strong>Discount :</strong></td>
                        <td>{{$discount_amount}}</td>
                    </tr>
                    <tr>
                        <td><strong>Delivery Fees :</strong></td>
                        <td>{{$delivery_charge}}</td>
                    </tr>
                    <tr>
                        <td><strong>TOTAL TTC :</strong></td>
                        <td><h3>{{$total_amount}}</h3></td>
                    </tr>
                    </tbody>
                </table>
                <h3 style="text-align: right">(the sum of {{$total_amount_letters}})</h3>
                <div class="text-right">
                    <a href="{{route('admin.orders.print_invoice',$order->id)}}"  class="btn btn-primary"><i class="fa fa-print"></i> Print Invoice </a>
                </div>
                <br>
                <br>
                <br>
                <br>
                <div class="row">
                    <div class="col-sm-12 col-xs-12">
                        <h4 >Thank you for your Payment</h4>
                        <br>
                        {{--<h5>Stamps duty paid as per receipt No. 18-84-11116337</h5>--}}
                        {{--<small style="color: lightgrey">THE LEBANESE COMPANY FOR THE DEVELOPMENT AND RECONSTRUCTION OF BEIRUT CENTRAL DISTRICT S.A.L | CAPITAL USD 1,650,000,000 FULLY PAID</small>--}}
                        {{--<br>--}}
                        {{--<small style="color: red">COMMERCIAL REGISTER 67000 BEIRUT | BLD 149 | SAAD ZAGHLOUL ST | PO BOX 119493 | BEIRUT 20127305 | LEB. | T 01 980650 | F 01 980661 | SOLIDERE.COM</small>--}}
                    </div>
                </div>
                <div class="row">
                    <div class="col-sm-6 col-xs-6">
                        &nbsp;
                    </div>
                    <div class="col-sm-6 col-xs-6">
                        <table class="table table-bordered table-responsive">
                            <thead>
                            <tr>
                                <th colspan="2" style="text-align: center">RECEIVED</th>
                            </tr>
                            </thead>
                            <tbody>
                            <tr>
                                <td colspan="2"><strong>By:</strong>    ...............................................................</td>
                            </tr>
                            <tr>
                                <td colspan="2"><strong>Date:</strong>    ...............................................................</td>
                            </tr>
                            <tr>
                                <td colspan="2"><strong>Signature:</strong>    ...............................................................</td>
                            </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="row">
                    <div class="col-sm-12 col-xs-12">
                        <h5 ><strong>Sincere Regards</strong></h5>
                        <h5><span class="text">{{$admin_username}}</span></h5>
                        <h5><span class="text">{{$signature_department}}</span></h5>
                        <h5><span class="text">{{$signature_company}}</span></h5>
                        <br>
                        {{--<h5>Stamps duty paid as per receipt No. 18-84-11116337</h5>--}}
                        {{--<small style="color: lightgrey">THE LEBANESE COMPANY FOR THE DEVELOPMENT AND RECONSTRUCTION OF BEIRUT CENTRAL DISTRICT S.A.L | CAPITAL USD 1,650,000,000 FULLY PAID</small>--}}
                        {{--<br>--}}
                        {{--<small style="color: red">COMMERCIAL REGISTER 67000 BEIRUT | BLD 149 | SAAD ZAGHLOUL ST | PO BOX 119493 | BEIRUT 20127305 | LEB. | T 01 980650 | F 01 980661 | SOLIDERE.COM</small>--}}
                    </div>
                </div>

            </div>

        </div>


        </div>
        </div>

    <!-- Mainly scripts -->
    <script src="{{env('APP_ENV') == 'local' ? asset('public/invoice')}}/js/jquery-2.1.1.js : secure_asset('public/invoice')}}/js/jquery-2.1.1.js"></script>
    <script src="{{env('APP_ENV') == 'local' ? asset('public/invoice')}}/js/bootstrap.min.js : secure_asset('public/invoice')}}/js/bootstrap.min.js"></script>
    <script src="{{env('APP_ENV') == 'local' ? asset('public/invoice')}}/js/plugins/metisMenu/jquery.metisMenu.js : secure_asset('public/invoice')}}/js/plugins/metisMenu/jquery.metisMenu.js"></script>
    <script src="{{env('APP_ENV') == 'local' ? asset('public/invoice')}}/js/plugins/slimscroll/jquery.slimscroll.min.js : secure_asset('public/invoice')}}/js/plugins/slimscroll/jquery.slimscroll.min.js"></script>

    <!-- Custom and plugin javascript -->
    <script src="{{env('APP_ENV') == 'local' ? asset('public/invoice')}}/js/inspinia.js : secure_asset('public/invoice')}}/js/inspinia.js"></script>
    <script src="{{env('APP_ENV') == 'local' ? asset('public/invoice')}}/js/plugins/pace/pace.min.js : secure_asset('public/invoice')}}/js/plugins/pace/pace.min.js"></script>


</body>

</html>
