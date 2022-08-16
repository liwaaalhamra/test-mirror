@extends('vendor.layout')

@section('content')
    <div class="row">
        <div class="col-sm-12">
            <div class="panel panel-default">
                <div class="panel-heading font-bold">{{$content_header}}</div>
                <div class="panel-body">
                    <form action="{{ route('admin.user_rating.store') }}" method="post" enctype="multipart/form-data">
                        {{ csrf_field() }}
                        <div class="row">
                            <div class="form-group col-sm-4">
                                <label>Choose Type</label>
                                <select class="form-control selectpicker" title="Choose..."  data-live-search="true" name="type" required>
                                    @foreach($types as $type)
                                        <option data-tokens="{{$type}}" value="{{ $type }}"> {{$type}}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="form-group col-sm-4">
                                <label>Choose Restaurant</label>
                                <select class="form-control selectpicker" title="Choose..."  data-live-search="true" name="item_id" required>
                                    @foreach($restaurants as $restaurant)
                                        <option data-tokens="{{$restaurant->name_en}}" value="{{ $restaurant->id }}">{{$restaurant->name_en}}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="form-group col-sm-4">
                                <label>Choose User</label>
                                <select class="form-control selectpicker" title="Choose..."  data-live-search="true" name="user_id" required>
                                    @foreach($users as $user)
                                        <option data-tokens="{{$user->first_name}} {{$user->last_name}}" value="{{ $user->id }}">{{$user->first_name}} {{$user->last_name}} @if($user->primary_phone) [{{$user->country_code}}{{$user->primary_phone}}] @endif @if($user->email) [{{$user->email}}] @endif</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <div class="row">
                            <div class="form-group col-sm-4">
                                <label>Choose Star(s)</label>
                                <select class="form-control selectpicker" title="Choose..."  data-live-search="true" name="stars" required>
                                    @foreach($stars as $star)
                                        <option data-tokens="{{$star}}" value="{{ $star }}"> {{$star}} star(s)</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="form-group col-sm-4">
                                <label>Choose Like Tags</label>
                                <select class="form-control selectpicker" title="Choose..."  data-live-search="true" name="like_ids[]" multiple required>
                                    @foreach($like_tags as $like_tag)
                                        <option data-tokens="{{$like_tag->name_en}}" value="{{ $like_tag->id }}">{{$like_tag->name_en}}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="form-group col-sm-4">
                                <label>Choose Dislike Tags</label>
                                <select class="form-control selectpicker" title="Choose..."  data-live-search="true" name="dislike_ids[]" multiple required>
                                    @foreach($dislike_tags as $dislike_tag)
                                        <option data-tokens="{{$dislike_tag->name_en}}" value="{{ $dislike_tag->id }}">{{$dislike_tag->name_en}}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="form-group col-sm-4">
                                <label>Description</label>
                                <textarea name="description" class="form-control"></textarea>
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

                        <button type="submit" class="btn btn-sm btn-success">Save</button>
                        <a href="{{ route('admin.user_rating.index') }}" class="btn btn-sm btn-dark">Back</a>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
@section('scripts')
    @parent
    <script>
        var $imageupload = $('.imageupload');
        $imageupload.imageupload({
//            allowedFormats: [ "pdf" ],
//            previewWidth: 250,
//            previewHeight: 250,
            maxFileSizeKb: 10240
        });
    </script>
@endsection
