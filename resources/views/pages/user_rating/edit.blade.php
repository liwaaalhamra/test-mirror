@extends('vendor.layout')

@section('content')
    <div class="row">
        <form action="{{ route('admin.user_rating.update',$item->id) }}" method="post" enctype="multipart/form-data">
            <input type="hidden" value="PUT" name="_method">
            {{ csrf_field() }}
            <div class="col-sm-12">
                <div class="panel panel-default">
                    <div class="panel-heading font-bold">{{$content_header}}</div>
                    <div class="panel-body">
                        <div class="row">
                            <div class="form-group col-sm-4">
                                <label>Choose Type</label>
                                <select class="form-control selectpicker" title="Choose..."  data-live-search="true" name="type" required>
                                    @foreach($types as $type)
                                        <option data-tokens="{{$type}}" @if($item->type == $type) selected @endif value="{{ $type }}"> {{$type}}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="form-group col-sm-4">
                                <label>Choose Restaurant</label>
                                <select class="form-control selectpicker" title="Choose..."  data-live-search="true" name="item_id" required>
                                    @foreach($restaurants as $restaurant)
                                        <option data-tokens="{{$restaurant->name_en}}" @if($restaurant->id == $item->item_id) selected @endif value="{{ $restaurant->id }}">{{$restaurant->name_en}}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="form-group col-sm-4">
                                <label>Choose User</label>
                                <select class="form-control selectpicker" title="Choose..."  data-live-search="true" name="user_id" required>
                                    @foreach($users as $user)
                                        <option data-tokens="{{$user->first_name}} {{$user->last_name}}" @if($user->id == $item->user_id) selected @endif value="{{ $user->id }}">{{$user->first_name}} {{$user->last_name}} @if($user->primary_phone) [{{$user->country_code}}{{$user->primary_phone}}] @endif @if($user->email) [{{$user->email}}] @endif</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="row">
                            <div class="form-group col-sm-4">
                                <label>Choose Star(s)</label>
                                <select class="form-control selectpicker" title="Choose..."  data-live-search="true" name="stars" required>
                                    @foreach($stars as $star)
                                        <option data-tokens="{{$star}}" @if($item->stars == $star) selected @endif value="{{ $star }}"> {{$star}} star(s)</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="form-group col-sm-4">
                                <label>Choose Like Tags</label>
                                <select class="form-control selectpicker" title="Choose..."  data-live-search="true" name="like_ids[]" multiple required>
                                    @foreach($like_tags as $like_tag)
                                        <option data-tokens="{{$like_tag->name_en}}" @if(in_array($like_tag->id,$item->tags_like)) selected @endif value="{{ $like_tag->id }}">{{$like_tag->name_en}}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="form-group col-sm-4">
                                <label>Choose Dislike Tags</label>
                                <select class="form-control selectpicker" title="Choose..."  data-live-search="true" name="dislike_ids[]" multiple required>
                                    @foreach($dislike_tags as $dislike_tag)
                                        <option data-tokens="{{$dislike_tag->name_en}}" @if(in_array($dislike_tag->id,$item->tags_dislike)) selected @endif value="{{ $dislike_tag->id }}">{{$dislike_tag->name_en}}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="form-group col-sm-4">
                                <label>Order</label>
                                <input type="text" name="item_order" required id="item_order" class="form-control" placeholder="Order" value="{{ $item->item_order }}">
                            </div>
                            <div class="form-group col-sm-4">
                                <label>Description</label>
                                <textarea name="description" class="form-control">{{$item->description}}</textarea>
                            </div>
                        </div>
                        <div class="row">
                            <div class="form-group col-sm-4">
                                <div class="imageupload panel panel-default">

                                    <div class="file-tab panel-body">
                                        <label class="btn btn-default btn-file">
                                            <span>Browse</span>
                                            <input type="file" name="image" id="image">
                                        </label>
                                        <button type="button" class="btn btn-default">Remove</button>
                                        @if(!empty($item) && $item->getMediaName() != null)
                                            <a href="{{route('admin.user_rating.delete_single',$item->id)}}" class="btn btn-sm btn-danger pull-right delete">Delete Existing Image</a>
                                        @endif
                                    </div>
                                    <div class="panel-heading clearfix" style="text-align: center">
                                        <h3 class="panel-title pull-left">Image</h3>
                                        <div class="btn-group">
                                            @if(!empty($item) && $item->getMediaName() != null)
                                                <img  alt="Image Preview"  src="{{ $item->image }}" class="thumbnail" data-toggle="modal" data-target="#logo"  style="cursor: pointer;max-width:200px;max-height: 200px;margin-bottom: 0px"/>
                                                <div id="logo" class="modal fade" role="dialog">
                                                    <div class="modal-dialog">
                                                        <div class="modal-content">
                                                            <div class="modal-header">
                                                                <button type="button" class="close" data-dismiss="modal">&times;</button>
                                                                <h3 class="modal-title">Image</h3>
                                                            </div>
                                                            <div class="modal-body">
                                                                <img src="{{ $item->image }}" width="100%;">
                                                            </div>
                                                            <div class="modal-footer">
                                                                <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                                                            </div>
                                                        </div>

                                                    </div>
                                                </div>
                                                <br><br>
                                            @endif
                                        </div>
                                        <div class="btn-group pull-right">
                                            <button type="button" class="btn btn-default active">File</button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <button type="submit" class="btn btn-sm btn-primary">Save</button>
                        <a href="{{ route('admin.user_rating.index') }}" class="btn btn-sm btn-dark">Back</a>
                    </div>
                </div>
            </div>
        </form>
    </div>
@endsection

@section('scripts')
    @parent
    <script>
        var $imageupload = $('.imageupload');
        $imageupload.imageupload();
    </script>
@endsection
