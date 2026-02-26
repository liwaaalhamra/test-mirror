@extends('vendor.layout')

@section('content')
    <div class="row">
        @if(in_array('bulk',auth()->user()->role->hasAccess($section_name)))
        <div class="col-sm-6">
            <div class="panel panel-default">
                <div class="panel-heading font-bold">Bulk Push</div>
                <div class="panel-body">

                    <form action="{{ route('admin.notification.send_push') }}" method="post" enctype="multipart/form-data">
                        {{ csrf_field() }}
                        <div class="row">
                            <div class="form-group col-sm-12">
                                <input name="title" class="form-control" required placeholder="Title" value="{{env('APP_NAME')}}"/>
                            </div>

                        </div>
                        <div class="row">
                            <div class="form-group col-sm-12">
                                <textarea name="message" class="form-control" required placeholder="Message">Welcome To {{env('APP_NAME')}} App!</textarea>
                            </div>
                        </div>
                        <div class="row">
                            <div class="form-group col-sm-12">
                                <div class="imageupload panel panel-default">
                                    <div class="panel-heading clearfix">
                                        <h3 class="panel-title pull-left">Push Image</h3>
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

                        <input type="hidden" name="segments" value="All" />
                        <button type="submit" class="btn btn-sm btn-primary">Send</button>
                    </form>
                </div>
            </div>
        </div>
        @endif
        @if(in_array('targeted',auth()->user()->role->hasAccess($section_name)))
        <div class="col-sm-6">
            <div class="panel panel-default">
                <div class="panel-heading font-bold">Targeted Push</div>
                <div class="panel-body">
                    <form action="{{ route('admin.notification.send_single_push') }}" method="post" enctype="multipart/form-data">
                        {{ csrf_field() }}
                        <div class="row">
                            <div class="form-group col-sm-12">
                                <label class="control-label" >Users</label>
                                <div class="form-group ">
                                    <select class="form-control selectpicker" id="users" data-live-search="true" multiple name="users[]" required data-actions-box="true">
                                        @foreach($users as $user)
                                                @if($user->full_name) 
                                                    <option value="{{$user->id}}" > 
                                                        {{$user->full_name}}
                                                    </option>
                                                @else 
                                                    <option value="{{$user->id}}" > 
                                                        {{$user->email}}
                                                    </option>
                                                @endif
                                        @endforeach
                                    </select>
                                </div>
                                <span class="help-block"></span>
                            </div>
                        </div>
                        <div class="row">
                            <div class="form-group col-sm-12">
                                <input name="title" class="form-control" required placeholder="Title" value="{{env('APP_NAME')}}"/>
                            </div>
                        </div>
                        <div class="row">
                            <div class="form-group col-sm-12">
                                <textarea name="message" class="form-control" required placeholder="Message">Welcome To {{env('APP_NAME')}} App!</textarea>
                            </div>
                        </div>
                        <div class="row">
                            <div class="form-group col-sm-12">
                                <div class="imageupload panel panel-default">
                                    <div class="panel-heading clearfix">
                                        <h3 class="panel-title pull-left">Push Image</h3>
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
                        <button type="submit" class="btn btn-sm btn-primary">Send</button>
                    </form>
                </div>
            </div>
        </div>
        @endif
    </div>
    <div class="row">
        @if(in_array('targeted',auth()->user()->role->hasAccess($section_name)))
            <div class="col-sm-6">
                <div class="panel panel-default">
                    <div class="panel-heading font-bold">Categories Push</div>
                    <div class="panel-body">
                        <form action="{{ route('admin.notification.send_push') }}" method="post" enctype="multipart/form-data">
                            {{ csrf_field() }}
                            <div class="row">
                                <div class="form-group col-sm-12">
                                    <label class="control-label" >Categories</label>
                                    <div class="form-group ">
                                        <select class="form-control selectpicker" id="product_id" data-live-search="true" name="info[data][item_id]" required data-actions-box="true">
                                            <option data-tokens="" value="" selected >Choose Category...</option>
                                            @foreach($products as $product)
                                                    <option data-tokens="{{$product->name_en}} @if($product->category){{$product->category->name_en}} @endif" value="{{$product->id}}" >@if($product->category){{$product->category->name_en}} / @endif {{$product->name_en}}</option>
                                            @endforeach
                                            </select>
                                    </div>
                                    <span class="help-block"></span>
                                </div>
                            </div>
                            <div class="row">
                                <div class="form-group col-sm-12">
                                    <input name="title" class="form-control" required placeholder="Title" value="{{env('APP_NAME')}}"/>
                                </div>
                            </div>
                            <div class="row">
                                <div class="form-group col-sm-12">
                                    <textarea name="message" class="form-control" required placeholder="Message">Welcome To {{env('APP_NAME')}} App!</textarea>
                                </div>
                            </div>
                            <div class="row">
                                <div class="form-group col-sm-12">
                                    <div class="imageupload panel panel-default">
                                        <div class="panel-heading clearfix">
                                            <h3 class="panel-title pull-left">Push Image</h3>
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
                            <input type="hidden" name="info[data][meta_type]" value="categories" />
                            <input type="hidden" name="segments" value="All" />
                            <button type="submit" class="btn btn-sm btn-primary">Send</button>
                        </form>
                    </div>
                </div>
            </div>
        @endif
    </div>
@endsection
@section('scripts')
    @parent
    <script>
        var $imageupload = $('.imageupload');
        $imageupload.imageupload();

        //        $('#imageupload-disable').on('click', function() {
        //            $imageupload.imageupload('disable');
        //            $(this).blur();
        //        })
        //
        //        $('#imageupload-enable').on('click', function() {
        //            $imageupload.imageupload('enable');
        //            $(this).blur();
        //        })
        //
        //        $('#imageupload-reset').on('click', function() {
        //            $imageupload.imageupload('reset');
        //            $(this).blur();
        //        });
        //    tinymce.init({
        //        selector: 'textarea',
        //        height: 500,
        //        menubar: true,
        //        plugins: [
        //            'advlist autolink lists link image charmap print preview anchor',
        //            'searchreplace visualblocks code fullscreen',
        //            'insertdatetime media table contextmenu paste code'
        //        ],
        //        toolbar: 'undo redo | insert | styleselect | bold italic | alignleft aligncenter alignright alignjustify | bullist numlist outdent indent | link image',
        //        content_css: '//www.tinymce.com/css/codepen.min.css'
        //    });
        //        tinymce.init({
        //            selector: 'textarea',
        //            height: 200,
        //            menubar: false,
        //            plugins: [
        //            ],
        //            toolbar: 'undo redo  | styleselect | bold italic | bullist numlist',
        //            content_css: '//www.tinymce.com/css/codepen.min.css'
        //        });
    </script>
@endsection

