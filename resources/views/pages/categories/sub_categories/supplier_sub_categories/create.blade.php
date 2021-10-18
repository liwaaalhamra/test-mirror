@extends('vendor.layout')

@section('content')
    <div class="row">
        <div class="col-sm-12">
            <div class="panel panel-default">
                <div class="panel-heading font-bold">Sub Categories of {{ucwords($category->name_en)}}</div>
                <div class="panel-body">
                    <form id="submit_form" action="{{ route('admin.sub_categories.supplier_sub_categories.store',$category->id) }}" method="post" enctype="multipart/form-data">
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
                        </div>
                        <div class="row">
                            <div class="form-group col-sm-4">
                                <label>Description English</label>
                                <textarea name="description_en" id="description_en" class="form-control"></textarea>
                            </div>
                            <div class="form-group col-sm-4">
                                <label>Description Arabic</label>
                                <textarea name="description_ar" id="description_ar" class="form-control"></textarea>
                            </div>
                        </div>


                        <button type="submit" id="submit" class="btn btn-sm btn-primary">Save</button>
                        <a href="{{ route('admin.sub_categories.supplier_sub_categories.index',$category->id) }}" class="btn btn-sm btn-dark">Back</a>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
@section('scripts')
    @parent
    <script>
        CKEDITOR.replace( 'description_en' );
        CKEDITOR.replace( 'description_ar' );
    </script>
@endsection
