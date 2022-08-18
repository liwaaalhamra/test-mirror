@extends('vendor.layout')

@section('content')
    <div class="row">
        <div class="col-sm-12">
            <div class="panel panel-default">
                <div class="panel-heading font-bold">Customizations of {{ucwords($category->name_en)}}</div>
                <div class="panel-body">
                    <form id="submit_form" action="{{ route('admin.supplier_items.supplier_customizations.store',$category->id) }}" method="post" enctype="multipart/form-data">
                        <input type="hidden" name="_token" id="_token" value="{{ csrf_token() }}">
                        <div class="row">
                            <div class="form-group col-sm-4">
                                <label>Choose Type</label>
                                <select class="form-control selectpicker" title="Choose..."  data-live-search="true" name="type" required>
                                    @foreach($types as $type)
                                        <option data-tokens="{{$type}}" value="{{ $type }}"> {{$type}} Choice</option>
                                    @endforeach
                                </select>
                            </div>
                            {{--<div class="form-group col-sm-4">--}}
                                {{--<label>Max Selection Count</label>--}}
                                {{--<input type="text" name="max_selection_count" id="max_selection_count" class="form-control" required placeholder="Max Selection Count" value="{{ old('max_selection_count') }}">--}}
                            {{--</div>--}}
                        </div>
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


                        <button type="submit" id="submit" class="btn btn-sm btn-success">Save</button>
                        <a href="{{ route('admin.supplier_items.supplier_customizations.index',$category->id) }}" class="btn btn-sm btn-dark">Back</a>
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
