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
                <a href="{{ route('admin.events.create') }}" class="btn m-b-xs btn-sm btn-default btn-addon">
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
                    <th>Admin</th>
                    <th>Caption</th>
                    <th>Description</th>
                    <th>Image</th>
                    <th>Youtube</th>
                    <th>Address</th>
                    <th>Date From</th>
                    <th>Date To</th>
                    <th>Link</th>
                    <th>Status</th>
                    <th>Date Added</th>
                    <th></th>
                </tr>
                </thead>
                <tbody>
                @foreach($items as $item)
                    <tr >
                        <td>
                            @if($item->admin)
                                {{$item->admin->first_name}} {{$item->admin->last_name}}
                            @else
                                N/A
                            @endif
                        </td>
                        <td>{{ ucwords($item->caption) }}</td>
                        <td>
                            {!!  $item->description !!}<br>
                        </td>
                        <td>
                            @if($item->media_type == 'image' && $item->getMediaName() != null)
                                <img src="{{$item->media}}" width="100px" data-toggle="modal" data-target="#myModal_{{$item->id}}"  style="cursor: pointer;"/>
                                <div id="myModal_{{$item->id}}" class="modal fade" role="dialog">
                                    <div class="modal-dialog">
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                <button type="button" class="close" data-dismiss="modal">&times;</button>
                                            </div>
                                            <div class="modal-body">
                                                <img src="{{$item->media}}" width="100%;">
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
                            @if($item->youtube_id)
                                <span class="label label-info">
                                <a target="_blank" href="{{ $item->media }}"> <i aria-hidden="true" style="display: inline-block" class="fab fa-youtube"></i> Play Video</a>
                            </span>
                            @endif
                        </td>
                        <td>
                            @if($item->latitude != 0 && $item->longitude != 0)
                                <span class="label label-info">
                                    <a target="_blank" href="http://maps.google.com/?q={{ $item->latitude }},{{ $item->longitude }}"> <i aria-hidden="true" style="display: inline-block" class="fa fa-street-view"></i> View Location</a>
                                </span>
                            @endif
                        </td>
                        <td>@if($item->event_date_from){{ \Carbon\Carbon::createFromTimestamp($item->event_date_from)->format('Y-m-d H:i:s') }} @endif</td>
                        <td>@if($item->event_date_to){{ \Carbon\Carbon::createFromTimestamp($item->event_date_to)->format('Y-m-d H:i:s') }} @endif</td>
                        <td>{{ $item->link }}</td>
                        <td>
                            @if($item->is_published)
                                <h4><span class="label label-success">Online</span></h4>
                            @else
                                <h4><span class="label label-warning">Offline</span></h4>
                            @endif
                        </td>
                        <td>{{ \Carbon\Carbon::createFromTimestamp($item->created_at)->format('Y-m-d H:i:s') }}</td>
                        <td>
                            @if(strpos($accessible_sections,'all') !== false)
                                @if($item->is_published)
                                    <a href="{{ route('admin.events.publish', $item->id) }}" class="btn btn-md btn-warning">
                                        UnPublish
                                    </a>
                                @else
                                    <a href="{{ route('admin.events.publish', $item->id) }}" class="btn btn-md btn-success">
                                        Publish
                                    </a>
                                @endif
                            @endif
                            <a href="{{ route('admin.events.edit', $item->id) }}" class="btn btn-md btn-info"><i class="fa fa-edit"> Edit</i></a>
                            <form action="{{ secure_url('admin/events/'. $item->id) }}" method="POST" class="pull-left delete" style="margin-right: 3px">
                                {{ csrf_field() }}
                                {{ method_field('DELETE') }}

                                <button type="submit" class="btn btn-md btn-danger">
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

