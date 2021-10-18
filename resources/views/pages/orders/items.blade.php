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
            Items of <b>{{$order->code}}</b>
        </div>
        <div class="row wrapper">
            <div class="col-sm-5 m-b-xs">
                <a href="{{ route('admin.orders.index') }}" class="btn m-b-xs btn-sm btn-default btn-addon">
                    <i class="fa fa-arrow-circle-left"></i>Back
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
                    <th>SKU</th>
                    <th>Name</th>
                    <th>Image</th>
                    <th>Quantity</th>
                    <th>Price</th>
                    <th>Discounted Price</th>
                    <th>Customizations Price</th>
                    <th>Customizations</th>
                    <th>Notes</th>
                    <th>Created At</th>
                </tr>
                </thead>
                <tbody>
                    @foreach($items as $item)
                    <tr>
                        <td>@if($item->item){{ $item->item->code }} @else n/a @endif</td>
                        <td>@if($item->item){{ ucwords($item->item->name_en) }} @else n/a @endif</td>
                        <td>
                            @if($item->item && $item->item->getMediaName() != null)
                                <img src="{{$item->item->image}}" width="100px" data-toggle="modal" data-target="#myModal_{{$item->id}}"  style="cursor: pointer;"/>
                                <div id="myModal_{{$item->id}}" class="modal fade" role="dialog">
                                    <div class="modal-dialog">
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                <button type="button" class="close" data-dismiss="modal">&times;</button>
                                            </div>
                                            <div class="modal-body">
                                                <img src="{{$item->item->image}}" width="100%;">
                                            </div>
                                            <div class="modal-footer">
                                                <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                                            </div>
                                        </div>

                                    </div>
                                </div>
                            @endif
                        </td>
{{--                        <td>@if($item->category){{ ucwords($item->category->name) }} @else {{ ucwords(str_replace("_"," ",$item->meta_name)) }}@endif</td>--}}
                        <td><h3>{{ $item->quantity }} Piece(s)</h3></td>
{{--                        <td>@if($item->size_id != null) {{\App\Models\Api\ItemSize::whereId($item->size_id)->first()->name}} @endif</td>--}}
                        <td><h3>{{ $item->price }} {{$order->currency}}</h3></td>
                        <td><h3>@if($item->discounted_price){{ $item->discounted_price }} @else 0 @endif {{$order->currency}}</h3></td>
                        <td><h3>@if($item->customizations_price) {{ $item->customizations_price }} @else 0 @endif {{$order->currency}}</h3></td>
                        <td>
                            @foreach($item->options as $selected_option)
                                <li>{{$selected_option->customization_option->name_en}}: {{$selected_option->price}} {{$order->currency}} (x{{$selected_option->quantity}})</li>
                            @endforeach
                        </td>
                        <td>{{$item->notes}}</td>
                        <td>{{ \Carbon\Carbon::createFromTimestamp($item->created_at)->format('Y-m-d H:i:s') }}</td>
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
                order: [9,'desc'],
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
                order: [9,'desc'],
                "pagingType": "full_numbers",
            });
        } );
        @endif
    </script>
@endsection
