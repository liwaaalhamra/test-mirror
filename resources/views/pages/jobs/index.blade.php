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
        <div class="col-sm-12">
            <div class="panel panel-default">
                <div class="panel-heading font-bold">Introduction</div>
                <div class="panel-body">
                    <form action="{{ secure_url('admin/jobs_introduction') }}" method="post" enctype="multipart/form-data">
                        {{ csrf_field() }}
                        <div class="row">
                            <div class="form-group col-sm-6">
                                <label>Career Title</label>
                                <input type="text" name="career_title" id="career_title" class="form-control" required placeholder="Career Title" value="{{ $intro->career_title }}">
                            </div>
                            <div class="form-group col-sm-6">
                                <label>Career Description</label>
                                <textarea rows="3" name="career_description" class="form-control" placeholder="Career Description">{{ $intro->career_description }}</textarea>
                            </div>
                        </div>
                        <div class="row">
                            <div class="form-group col-sm-6">
                                <label>Internship Title</label>
                                <input type="text" name="intern_title" id="intern_title" class="form-control" required placeholder="Internship Title" value="{{ $intro->intern_title }}">
                            </div>
                            <div class="form-group col-sm-6">
                                <label>Internship Description</label>
                                <textarea rows="3" name="intern_description" class="form-control" placeholder="Internship Description">{{ $intro->intern_description }}</textarea>
                            </div>
                        </div>
                        <button type="submit" class="btn btn-sm btn-primary">Save</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <div class="panel panel-default">
        <div class="panel-heading">
            {{$content_header}}
        </div>
        <div class="row wrapper">
            <div class="col-sm-5 m-b-xs">
                <a href="{{ route('admin.jobs.create') }}" class="btn m-b-xs btn-sm btn-default btn-addon">
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
                    <th>Type</th>
                    <th>Caption</th>
                    <th>Description</th>
                    <th>Job Date</th>
                    <th>Image</th>
                    {{--<th>Link</th>--}}
                    <th>Status</th>
                    <th>Date Added</th>
                    <th></th>
                </tr>
                </thead>
                <tbody>
                @foreach($items as $item)
                    <tr >
                        <td>{{ ucwords($item->type) }}</td>
                        <td>{{ ucwords($item->caption) }}</td>
                        <td>
                            {!!  $item->description !!}<br>
                        </td>
                        <td>@if($item->job_datetime){{ \Carbon\Carbon::createFromTimestamp($item->job_datetime)->format('Y-m-d H:i:s') }} @endif</td>
                        <td>
                            @if($item->image != null)
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
                        {{--<td>{{ $item->link }}</td>--}}
                        <td>
                            @if($item->is_published)
                                <h4><span class="label label-success">Online</span></h4>
                            @else
                                <h4><span class="label label-warning">Offline</span></h4>
                            @endif
                        </td>
                        <td>{{ \Carbon\Carbon::createFromTimestamp($item->created_at)->format('Y-m-d H:i:s') }}</td>
                        <td>
                            @if($item->is_published)
                                <a href="{{ route('admin.jobs.publish', $item->id) }}" class="btn btn-md btn-warning">
                                    UnPublish
                                </a>
                            @else
                                <a href="{{ route('admin.jobs.publish', $item->id) }}" class="btn btn-md btn-success">
                                    Publish
                                </a>
                            @endif
                            <a href="{{ route('admin.jobs.edit', $item->id) }}" class="btn btn-md btn-info"><i class="fa fa-edit"> Edit</i></a>
                            <form action="{{ secure_url('admin/jobs/'. $item->id) }}" method="POST" class="pull-left delete" style="margin-right: 3px">
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

