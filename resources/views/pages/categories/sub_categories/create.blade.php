@extends('vendor.layout')

@section('content')
    <div class="row">
        <div class="col-sm-12">
            <div class="panel panel-default">
                <div class="panel-heading font-bold">Items of {{ucwords($category->name)}}</div>
                <div class="panel-body">
                    <form id="submit_form" action="{{ secure_url('admin/categories/'.$category->id.'/sub_categories') }}" method="post" enctype="multipart/form-data">
                        <input type="hidden" name="_token" id="_token" value="{{ csrf_token() }}">
                        <div class="row">
                            <div class="form-group col-md-4">
                                <label>Is Featured?</label>
                                <input name="is_featured" type="checkbox" data-toggle="toggle" data-on="YES" data-off="NO" data-onstyle="success" data-offstyle="danger" data-size="large">
                            </div>
                            <div class="form-group col-sm-4">
                                <label>Name English</label>
                                <input type="text" name="name_en" required id="name_en" class="form-control" placeholder="Name English" value="{{ old('name_en') }}">
                            </div>
                            <div class="form-group col-sm-4">
                                <label>Name Arabic</label>
                                <input type="text" name="name_ar" required id="name_ar" class="form-control" placeholder="Name Arabic" value="{{ old('name_ar') }}">
                            </div>
                        </div>
                        <div class="row">
                            <div class="form-group col-sm-6">
                                <label>Description English</label>
                                <textarea name="description_en" id="desc_en" rows="10" class="form-control">{{old('description_en')}}</textarea>
                            </div>
                            <div class="form-group col-sm-6">
                                <label>Description Arabic</label>
                                <textarea name="description_ar" id="desc_ar" rows="10" class="form-control">{{old('description_ar')}}</textarea>
                            </div>
                        </div>
                        <div class="row">
                            <div class="form-group col-sm-4">
                                <div class="imageupload panel panel-default">
                                    <div class="panel-heading clearfix">
                                        <h3 class="panel-title pull-left">Image</h3>
                                        <div class="btn-group pull-right">
                                            <button type="button" class="btn btn-default active">File</button>
                                        </div>
                                    </div>
                                    <div class="file-tab panel-body">
                                        <label class="btn btn-default btn-file">
                                            <span>Browse</span>
                                            <input type="file" name="image" id="image">
                                        </label>
                                        <button type="button" class="btn btn-default">Remove</button>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <button type="submit" id="submit" class="btn btn-sm btn-primary">Save</button>
                        <a href="{{ route('admin.categories.sub_categories.index',$category->id) }}" class="btn btn-sm btn-dark">Back</a>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
@section('scripts')
    @parent

    <script>
        CKEDITOR.replace( 'desc_en' );
        CKEDITOR.replace( 'desc_ar' );
        var $imageupload = $('.imageupload');
        $imageupload.imageupload({
            allowedFormats: ['jpg', 'jpeg', 'png', 'gif'],
//            previewWidth: 250,
//            previewHeight: 250,
            maxFileSizeKb: 10240
        });
    </script>
@endsection
