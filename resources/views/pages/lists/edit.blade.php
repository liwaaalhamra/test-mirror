@extends('vendor.layout')

@section('content')
    <div class="row">
        <form action="{{ secure_url('admin/lists/'.$item->id) }}" method="post">
            <input type="hidden" value="PUT" name="_method">
            {{ csrf_field() }}
            <div class="col-sm-12">
                <div class="panel panel-default">
                    <div class="panel-heading font-bold">{{$content_header}}</div>
                    <div class="panel-body">
                        @foreach($items as $data)
                        <input type="hidden" name="id[]" value="{{$data->id}}"/>
                            <div class="row">
                                <div class="form-group col-sm-3">
                                    <label>Field Name</label>
                                    <input type="text" name="field_name[]" class="form-control" placeholder="Field Name" value="{{$data->field_name}}" required>
                                </div>
                                <div class="form-group col-sm-2">
                                    <label>Reference Name</label>
                                    <select class="form-control" name="parent_id[]">
                                        <option value="">Select Reference</option>
                                        @foreach($refs as $ref)
                                            <option value="{{$ref->id}}" @if($ref->id == $data->parent_id)  selected  @endif>{{$ref->field_name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="form-group col-sm-3">
                                    <label>Name in English</label>
                                    <input type="text" name="name_en[]" class="form-control" placeholder="Name in English" value="{{$data->name_en}}" required>
                                </div>
                                <div class="form-group col-sm-3">
                                    <label>Name in Arabic</label>
                                    <input type="text" name="name_ar[]" class="form-control" placeholder="Name in Arabic" value="{{$data->name_ar}}" required>
                                </div>
                                <div class="form-group col-sm-1">
                                    <label>Delete Item</label>
                                    <button id="delete-button" type="button" class="btn btn-md btn-danger delete-button" data-id="{{$data->id}}">
                                        <i class="fa fa-trash"></i> Delete
                                    </button>
                                </div>
                            </div>
                        @endforeach
                        <button type="submit" class="btn btn-sm btn-success">Save</button>
                        <a href="{{ route('admin.lists.index') }}" class="btn btn-sm btn-dark">Back</a>
                    </div>
                </div>
            </div>
        </form>
    </div>
@endsection
@section('scripts')
    @parent
    <script type="text/javascript">
        $(document).ready(function() {
            $('.delete-button').on('click', function() {
                var id = $(this).data('id');

                $.ajax({
                    url: '{{ secure_url('admin/dropdown_lists') }}' + '/' + id,
                    type: 'POST',
                    data: {
                        _method: 'DELETE',
                        _token: $('meta[name="token"]').attr('content')
                    },
                    success: function(response) {
                        location.reload();
                    }
                    
                });
            });
        });
    </script>
@endsection