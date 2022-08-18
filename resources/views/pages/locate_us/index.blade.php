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
        <div class="row wrapper">
            <div class="col-sm-5 m-b-xs">
                <a href="{{ route('admin.locate_us.create') }}" class="btn m-b-xs btn-sm btn-default btn-addon">
                    <i class="fa fa-plus"></i>Add {{$content_header}}
                </a>
            </div>
            <div class="col-sm-3">
            </div>
        </div>
        <div class="table-responsive">
            <table id="table" class="table table-hover b-t b-light">
                <thead>
                <tr>
                    <th>Title</th>
                    <th>Address</th>
                    <th>Image</th>
                    {{--<th>In Pickup List</th>--}}
                    <th>Date Added</th>
                    <th></th>
                </tr>
                </thead>
                <tbody>
                @foreach($locations as $item)
                    <tr >
                        <td>{{ ucwords($item->title) }}</td>
                        <td>
                            {{ ucwords($item->address) }} <br>
                            @if($item->latitude != 0 && $item->longitude != 0)
                                <span class="label label-info">
                                    <a target="_blank" href="http://maps.google.com/?q={{ $item->latitude }},{{ $item->longitude }}"> <i aria-hidden="true" style="display: inline-block" class="fa fa-street-view"></i>View Location</a>
                                </span>
                            @endif
                        </td>
                        <td>
                            @if($item->image != null)
                                <img src="{{secure_url('public/uploads/images/locate_us')}}{{DIRECTORY_SEPARATOR}}{{$item->image}}" width="100px" data-toggle="modal" data-target="#myModal_{{$item->id}}"  style="cursor: pointer;"/>
                                <div id="myModal_{{$item->id}}" class="modal fade" role="dialog">
                                    <div class="modal-dialog">
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                <button type="button" class="close" data-dismiss="modal">&times;</button>
                                            </div>
                                            <div class="modal-body">
                                                <img src="{{secure_url('public/uploads/images/locate_us')}}{{DIRECTORY_SEPARATOR}}{{$item->image}}" width="100%;">
                                            </div>
                                            <div class="modal-footer">
                                                <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                                            </div>
                                        </div>

                                    </div>
                                </div>
                            @endif
                        </td>
                        {{--<td>--}}
                            {{--@if($item->in_pickup_list)--}}
                                {{--<span class="label label-success">YES</span>--}}
                            {{--@else--}}
                                {{--<span class="label label-warning">NO</span>--}}
                            {{--@endif--}}
                        {{--</td>--}}
                        <td>{{ \Carbon\Carbon::createFromTimestamp($item->created_at)->format('Y-m-d H:i:s') }}</td>
                        <td>
                            {{--@if($item->in_pickup_list)--}}
                                {{--<a href="{{ route('admin.locate_us.pickup', $item->id) }}" class="btn btn-xs btn-danger">--}}
                                    {{--Remove From Pickup List--}}
                                {{--</a>--}}
                            {{--@else--}}
                                {{--<a href="{{ route('admin.locate_us.pickup', $item->id) }}" class="btn btn-xs btn-success">--}}
                                    {{--Add To Pickup List--}}
                                {{--</a>--}}
                            {{--@endif--}}
                            <a href="{{ route('admin.locate_us.edit', $item->id) }}" class="btn btn-xs btn-action"><i class="fa fa-edit"> Edit</i></a>
                            <form action="{{ secure_url('admin/locate_us/'. $item->id) }}" method="POST" class="pull-left delete" style="margin-right: 3px">
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
                "ordering": false,
                "bAutoWidth": true,
                "pagingType": "full_numbers"
            });
        } );
    </script>
@endsection

