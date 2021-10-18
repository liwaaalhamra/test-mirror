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
                @if(in_array('add',auth()->user()->role->hasAccess($section_name)))
                <a href="{{ route('admin.user_rating.create') }}" class="btn m-b-xs btn-sm btn-default btn-addon">
                    <i class="fa fa-plus"></i>Add {{$content_header}}
                </a>
                @endif
            </div>
            <div class="col-sm-3">
            </div>
        </div>
        <div class="table-responsive">
            <table id="table" class="table table-hover b-t b-light">
                <thead>
                <tr>
                    <th>Order</th>
                    <th>Type</th>
                    <th>Full Name</th>
                    <th>Mobile Number</th>
                    <th>Email</th>
                    <th>Restaurant</th>
                    <th>Description</th>
                    <th>Stars</th>
                    <th>Like Tags</th>
                    <th>Dislike Tags</th>
                    <th>Number of Likes</th>
                    <th>Image</th>
                    <th>Status</th>
                    <th>Date Added</th>
                    <th></th>
                </tr>
                </thead>
                <tbody>
                @foreach($items as $item)
                    <tr >
                        <td>#{{ $item->item_order }}</td>

                        <td>{{ $item->type }}</td>
                        <td>@if($item->user) {{$item->user->first_name}} {{$item->user->last_name}} @endif</td>
                        <td>@if($item->user) {{$item->user->country_code}}{{$item->user->primary_phone}} @endif</td>
                        <td>@if($item->user) {{$item->user->email}} @endif</td>
                        <td>@if($item->restaurant) {{$item->restaurant->name_en}} @endif</td>
                        <td>{{ $item->description }}</td>
                        <td>
                            <p style="white-space: nowrap">
                            @for($i=0;$i<$item->stars;$i++)
                                <i class="fas fa-star"></i>
                            @endfor
                            @for($i=$item->stars;$i<5;$i++)
                                <i class="far fa-star"></i>
                            @endfor
                            </p>
                        </td>
                        <td>
                            <ul>
                            @foreach($item->like_list as $like_item)
                                <li>
                                    {{$like_item->name_en}}
                                </li>
                            @endforeach
                            </ul>
                        </td>
                        <td>
                            <ul>
                                @foreach($item->dislike_list as $dislike_item)
                                    <li>
                                        {{$dislike_item->name_en}}
                                    </li>
                                @endforeach
                            </ul>
                        </td>
                        <td>{{$item->nb_likes}}</td>
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
                            @if($item->is_approved)
                                <h4><span class="label label-success">Approved</span></h4>
                            @else
                                <h4><span class="label label-warning">Not Approved</span></h4>
                            @endif
                        </td>
                        <td>{{ \Carbon\Carbon::createFromTimestamp($item->created_at)->format('Y-m-d H:i:s') }}</td>
                        <td>
                            @if(in_array('publish',auth()->user()->role->hasAccess($section_name)))
                                    {{--<a href="{{ route('admin.services.public', $item->id) }}" class="btn btn-md btn-warning">--}}
                                        {{--Show To Public--}}
                                    {{--</a>--}}
                                    {{--<a href="{{ route('admin.services.private', $item->id) }}" class="btn btn-md btn-default">--}}
                                        {{--Show To Private--}}
                                    {{--</a>--}}
                                    {{--<a href="{{ route('admin.services.both', $item->id) }}" class="btn btn-md btn-primary">--}}
                                        {{--Show To Both--}}
                                    {{--</a>--}}
                                @if($item->is_approved)
                                    <a href="{{ route('admin.user_rating.publish', $item->id) }}" class="btn btn-md btn-warning">
                                        UnPublish
                                    </a>
                                @else
                                    <a href="{{ route('admin.user_rating.publish', $item->id) }}" class="btn btn-md btn-success">
                                        Publish
                                    </a>
                                @endif
                            @endif
                            @if(in_array('edit',auth()->user()->role->hasAccess($section_name)))
                            <a href="{{ route('admin.user_rating.edit', $item->id) }}" class="btn btn-md btn-info"><i class="fa fa-edit"> Edit</i></a>
                            @endif
                            @if(in_array('delete',auth()->user()->role->hasAccess($section_name)))
                            <form action="{{ secure_url('admin/user_rating/'. $item->id) }}" method="POST" class="pull-left delete" style="margin-right: 3px">
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

