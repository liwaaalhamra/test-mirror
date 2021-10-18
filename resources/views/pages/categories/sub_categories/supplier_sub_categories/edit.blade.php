@extends('vendor.layout')

@section('content')
    <div class="row">
        <form id="submit_form" action="{{ route('admin.sub_categories.supplier_sub_categories.update',[$category->id,$item->id]) }}" method="post" enctype="multipart/form-data">
            <input type="hidden" value="PUT" name="_method">
            <input type="hidden" name="_token" id="_token" value="{{ csrf_token() }}">
            <div class="col-sm-12">
                <div class="panel panel-default">
                    <div class="panel-heading font-bold">Update Sub Category of {{ucwords($category->name_en)}}</div>
                    <div class="panel-body">
                        <div class="row">
                            <div class="form-group col-sm-4">
                                <label>Name English</label>
                                <input type="text" name="name_en" id="name_en" class="form-control" required placeholder="Name English" value="{{ $item->name_en }}">
                            </div>
                            <div class="form-group col-sm-4">
                                <label>Name Arabic</label>
                                <input type="text" name="name_ar" id="name_ar" class="form-control" required placeholder="Name Arabic" value="{{ $item->name_ar }}">
                            </div>
                            <div class="form-group col-sm-4">
                                <label>Order</label>
                                <input type="text" name="item_order" id="item_order" required class="form-control" placeholder="Order" value="{{ $item->item_order }}">
                            </div>
                        </div>
                        <div class="row">
                            <div class="form-group col-sm-6">
                                <label>Description English</label>
                                <textarea name="description_en" id="description_en" class="form-control">{{$item->description_en}}</textarea>
                            </div>
                            <div class="form-group col-sm-6">
                                <label>Description Arabic</label>
                                <textarea name="description_ar" id="description_ar" class="form-control">{{$item->description_ar}}</textarea>
                            </div>
                        </div>

                        <div class="form-group col-sm-12">
                            <button type="submit" id="submit" class="btn btn-sm btn-primary">Save</button>
                            <a href="{{ route('admin.sub_categories.supplier_sub_categories.index',$category->id) }}" class="btn btn-sm btn-dark">Back</a>
                        </div>

                    </div>
                </div>
            </div>
        </form>
    </div>
@endsection

@section('scripts')
    @parent
    <script>
        CKEDITOR.replace( 'description_en' );
        CKEDITOR.replace( 'description_ar' );
    </script>
@endsection
