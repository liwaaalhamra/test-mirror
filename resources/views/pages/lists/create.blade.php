@extends('vendor.layout')

@section('content')
    <div class="row">
        <form action="{{ secure_url('admin/lists') }}" method="post">
            {{ csrf_field() }}
            <div class="col-sm-12">
                <div class="panel panel-default">
                    <div class="panel-heading font-bold">{{$content_header}}</div>
                    <div class="panel-body">
                        <div class="row">
                            <div class="form-group col-sm-4">
                                <label>New Field Name</label>
                                <input type="text" name="field_name" class="form-control" placeholder="Field Name" value="">
                            </div>
                            <div class="form-group col-sm-4">
                                <label>Existing Field Name</label>
                                <select class="form-control" name="field_name_list">
                                    <option value="">Select Name</option>
                                    @foreach($refs as $ref)
                                        <option value="{{$ref->field_name}}">{{$ref->field_name}}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="form-group col-sm-4">
                                <label>Reference Field</label>
                                <select class="form-control" name="parent_id">
                                    <option value="">Select Reference</option>
                                    @foreach($refs as $ref)
                                           <option value="{{$ref->id}}">{{$ref->field_name}}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="form-group col-sm-6">
                                <label>Name in English</label>
                                <input type="text" name="name_en" class="form-control" placeholder="Name in English" value="" required>
                            </div>
                            <div class="form-group col-sm-6">
                                <label>Name in Arabic</label>
                                <input type="text" name="name_ar" class="form-control" placeholder="Name in Arabic" value="" required>
                            </div>
                        </div>
                        <button type="submit" class="btn btn-sm btn-success">Save</button>
                        <a href="{{ route('admin.lists.index') }}" class="btn btn-sm btn-dark">Back</a>
                    </div>
                </div>
            </div>
        </form>
    </div>
@endsection
