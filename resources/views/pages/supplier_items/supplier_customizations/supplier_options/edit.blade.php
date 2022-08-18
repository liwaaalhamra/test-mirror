@extends('vendor.layout')

@section('content')
    <div class="row">
        <form id="submit_form" action="{{ route('admin.supplier_customizations.supplier_options.update',[$category->id,$item->id]) }}" method="post" enctype="multipart/form-data">
            <input type="hidden" value="PUT" name="_method">
            <input type="hidden" name="_token" id="_token" value="{{ csrf_token() }}">
            <div class="col-sm-12">
                <div class="panel panel-default">
                    <div class="panel-heading font-bold">Update List of {{ucwords($category->name_en)}}</div>
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

                        </div>
                        <div class="row">
                            <div class="form-group col-sm-4">
                                <label>Price</label><small style="color: red">(Leave it empty if Free)</small>
                                <input type="number" name="price" id="price" class="form-control" placeholder="Price" value="{{ $item->price }}">
                            </div>
                            <div class="form-group col-sm-4">
                                <label>Order</label>
                                <input type="number" name="item_order" id="item_order" required class="form-control" placeholder="Order" value="{{ $item->item_order }}">
                            </div>
                        </div>

                        <div class="form-group col-sm-12">
                            <button type="submit" id="submit" class="btn btn-sm btn-success">Save</button>
                            <a href="{{ route('admin.supplier_customizations.supplier_options.index',$category->id) }}" class="btn btn-sm btn-dark">Back</a>
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

    </script>
@endsection
