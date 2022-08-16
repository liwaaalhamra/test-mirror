@extends('vendor.layout')

@section('content')
    <div class="row">
        <div class="col-sm-12">
            <div class="panel panel-default">
                <div class="panel-heading font-bold">Sub Category of {{ucwords($category->name)}}</div>
                <div class="panel-body">
                    <form id="submit_form" action="{{ secure_url('admin/stores_shops/'.$category->id.'/sub_categories') }}" method="post" enctype="multipart/form-data">
                        <input type="hidden" name="_token" id="_token" value="{{ csrf_token() }}">
                        <div class="row">
                            <div class="form-group col-sm-4">
                                <label>Name</label>
                                <input type="text" name="name" id="name" class="form-control" required placeholder="Name" value="{{ old('name') }}">
                            </div>
                        </div>


                        <button type="submit" id="submit" class="btn btn-sm btn-success">Save</button>
                        <a href="{{ route('admin.stores_shops.sub_categories.index',$category->id) }}" class="btn btn-sm btn-dark">Back</a>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
@section('scripts')
    @parent

@endsection
