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
                {{--<a href="{{ route('admin.gifts.create') }}" class="btn m-b-xs btn-sm btn-default btn-addon">--}}
                    {{--<i class="fa fa-plus"></i>Add {{$content_header}}--}}
                {{--</a>--}}
            </div>
            <div class="col-sm-3">
            </div>
        </div>
        <div class="table-responsive">
            <table id="table" class="table table-hover b-t b-light">
                <thead>
                <tr>
                    <th>Title</th>
                    <th>Description</th>
                    <th>Image</th>
                    <th>Points</th>
                    <th>Link</th>
                    <th>Status</th>
                    <th>Date Added</th>
                    <th></th>
                </tr>
                </thead>
                <tbody>
                @foreach($gifts as $item)
                    <tr >
                        <td>{{ ucwords($item->title) }}</td>
                        <td>
                            {{ ucwords($item->description) }} <br>
                        </td>
                        <td>
                            @if($item->image != null)
                                <img src="{{secure_url('uploads/images/gifts')}}{{DIRECTORY_SEPARATOR}}{{$item->image}}" width="100px" data-toggle="modal" data-target="#myModal_{{$item->id}}"  style="cursor: pointer;"/>
                                <div id="myModal_{{$item->id}}" class="modal fade" role="dialog">
                                    <div class="modal-dialog">
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                <button type="button" class="close" data-dismiss="modal">&times;</button>
                                            </div>
                                            <div class="modal-body">
                                                <img src="{{secure_url('uploads/images/gifts')}}{{DIRECTORY_SEPARATOR}}{{$item->image}}" width="100%;">
                                            </div>
                                            <div class="modal-footer">
                                                <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                                            </div>
                                        </div>

                                    </div>
                                </div>
                            @endif
                        </td>
                        <td>{{ $item->points }} pts</td>
                        <td>{{ $item->link }}</td>
                        <td>
                            @if($item->is_activated)
                                <span class="label label-success">Enabled</span>
                            @else
                                <span class="label label-warning">Disabled</span>
                            @endif
                        </td>
                        <td>{{ \Carbon\Carbon::createFromTimestamp($item->created_at)->format('Y-m-d H:i:s') }}</td>
                        <td>
                            @if($item->is_activated)
                                <a href="{{ route('admin.gifts.lock', $item->id) }}" class="btn btn-xs btn-danger">
                                    Disable
                                </a>
                            @else
                                <a href="{{ route('admin.gifts.lock', $item->id) }}" class="btn btn-xs btn-success">
                                    Enable
                                </a>
                            @endif
                            <a href="{{ route('admin.gifts.edit', $item->id) }}" class="btn btn-xs btn-action"><i class="fa fa-edit"> Edit</i></a>
                            {{--<form action="{{ secure_url('admin/gifts/'. $item->id) }}" method="POST" class="pull-left delete" style="margin-right: 3px">--}}
                                {{--{{ csrf_field() }}--}}
                                {{--{{ method_field('DELETE') }}--}}

                                {{--<button type="submit" class="btn btn-xs btn-danger">--}}
                                    {{--<i class="fa fa-trash"></i> Delete--}}
                                {{--</button>--}}
                            {{--</form>--}}
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

