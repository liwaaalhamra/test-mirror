@extends('vendor.layout')

@section('content')
    <div class="row">
        <div class="col-sm-12">
            <div class="panel panel-default">
                <div class="panel-heading font-bold">List of {{ucwords($category->name_en)}}</div>
                <div class="panel-body">
                    <form id="submit_form" action="{{ route('admin.supplier_customizations.supplier_options.store',$category->id) }}" method="post" enctype="multipart/form-data">
                        <input type="hidden" name="_token" id="_token" value="{{ csrf_token() }}">
                        <div class="row">
                            <div class="form-group col-sm-4">
                                <label>Name English</label>
                                <input type="text" name="name_en" id="name_en" class="form-control" required placeholder="Name English" value="{{ old('name_en') }}">
                            </div>
                            <div class="form-group col-sm-4">
                                <label>Name Arabic</label>
                                <input type="text" name="name_ar" id="name_ar" class="form-control" required placeholder="Name Arabic" value="{{ old('name_ar') }}">
                            </div>
                            <div class="form-group col-sm-4">
                                <label>Price</label><small style="color: red">(Leave it empty if Free)</small>
                                <input type="number" name="price" id="price" class="form-control" placeholder="Price" value="{{ old('price') }}">
                            </div>
                        </div>

                        <button type="submit" id="submit" class="btn btn-sm btn-primary">Save</button>
                        <a href="{{ route('admin.supplier_customizations.supplier_options.index',$category->id) }}" class="btn btn-sm btn-dark">Back</a>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
@section('scripts')
    @parent
    <script>
    </script>
@endsection
