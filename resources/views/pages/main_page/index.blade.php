@extends('vendor.layout')

@section('content')
    <div class="row">
        <div class="col-sm-12">
            <div class="panel panel-default">
                <div class="panel-heading font-bold">{{$content_header}}</div>
                <div class="panel-body">
                    <form action="{{ secure_url('admin/main_page') }}" method="post" enctype="multipart/form-data">
                        {{ csrf_field() }}
                        <div class="row">
                            <div class="form-group col-sm-6">
                                <label>Title</label>
                                <input type="text" name="stores_title" id="stores_title" class="form-control" placeholder="Title" value="{{ $stores->title }}">
                            </div>
                            <div class="form-group col-sm-6">
                                <div class="imageupload stores_category panel panel-default">
                                    <div class="file-tab panel-body">
                                        <label class="btn btn-default btn-file">
                                            <span>Browse</span>
                                            <input type="file" name="stores_shops" id="stores_shops">
                                        </label>
                                        <button type="button" class="btn btn-default">Remove</button>
                                        @if(!empty($stores) && $stores->getMediaName() != null)
                                            <a href="{{secure_url('admin/main_page/stores_shops/delete_single')}}" class="btn btn-sm btn-danger pull-right delete">Delete Existing Stores & Shops Image</a>
                                        @endif
                                    </div>
                                    <div class="panel-heading clearfix" style="text-align: center">
                                        <h3 class="panel-title pull-left">Stores & Shops Image</h3>
                                        <div class="btn-group">
                                            @if(!empty($stores) && $stores->getMediaName() != null)
                                                <img  alt="Image Preview"  src="{{ $stores->image }}" class="thumbnail" data-toggle="modal" data-target="#stores_category"  style="cursor: pointer;max-width:200px;max-height: 200px;margin-bottom: 0px"/>
                                                <div id="stores_category" class="modal fade" role="dialog">
                                                    <div class="modal-dialog">
                                                        <div class="modal-content">
                                                            <div class="modal-header">
                                                                <button type="button" class="close" data-dismiss="modal">&times;</button>
                                                                <h3 class="modal-title">Stores & Shops Image</h3>
                                                            </div>
                                                            <div class="modal-body">
                                                                <img src="{{ $stores->image }}" width="100%;">
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
                        <div class="row">
                            <div class="form-group col-sm-6">
                                <label>Title</label>
                                <input type="text" name="restaurants_title" id="restaurants_title" class="form-control" placeholder="Title" value="{{ $restaurants->title }}">
                            </div>
                            <div class="form-group col-sm-6">
                                <div class="imageupload restaurants_category panel panel-default">
                                    <div class="file-tab panel-body">
                                        <label class="btn btn-default btn-file">
                                            <span>Browse</span>
                                            <input type="file" name="restaurants_cafes" id="restaurants_cafes">
                                        </label>
                                        <button type="button" class="btn btn-default">Remove</button>
                                        @if(!empty($restaurants) && $restaurants->getMediaName() != null)
                                            <a href="{{secure_url('admin/main_page/restaurants_cafes/delete_single')}}" class="btn btn-sm btn-danger pull-right delete">Delete Existing Restaurants & Cafes Image</a>
                                        @endif
                                    </div>
                                    <div class="panel-heading clearfix" style="text-align: center">
                                        <h3 class="panel-title pull-left">Restaurants & Cafes Image</h3>
                                        <div class="btn-group">
                                            @if(!empty($restaurants) && $restaurants->getMediaName() != null)
                                                <img  alt="Image Preview"  src="{{ $restaurants->image }}" class="thumbnail" data-toggle="modal" data-target="#restaurants_category"  style="cursor: pointer;max-width:200px;max-height: 200px;margin-bottom: 0px"/>
                                                <div id="restaurants_category" class="modal fade" role="dialog">
                                                    <div class="modal-dialog">
                                                        <div class="modal-content">
                                                            <div class="modal-header">
                                                                <button type="button" class="close" data-dismiss="modal">&times;</button>
                                                                <h3 class="modal-title">Restaurants & Cafes Image</h3>
                                                            </div>
                                                            <div class="modal-body">
                                                                <img src="{{ $restaurants->image }}" width="100%;">
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
                        <div class="row">
                            <div class="form-group col-sm-6">
                                <label>Title</label>
                                <input type="text" name="entertainment_title" id="entertainment_title" class="form-control" placeholder="Title" value="{{ $entertainment->title }}">
                            </div>
                            <div class="form-group col-sm-6">
                                <div class="imageupload entertainment_category panel panel-default">
                                    <div class="file-tab panel-body">
                                        <label class="btn btn-default btn-file">
                                            <span>Browse</span>
                                            <input type="file" name="entertainment" id="entertainment">
                                        </label>
                                        <button type="button" class="btn btn-default">Remove</button>
                                        @if(!empty($entertainment) && $entertainment->getMediaName() != null)
                                            <a href="{{secure_url('admin/main_page/entertainment/delete_single')}}" class="btn btn-sm btn-danger pull-right delete">Delete Existing Entertainment Image</a>
                                        @endif
                                    </div>
                                    <div class="panel-heading clearfix" style="text-align: center">
                                        <h3 class="panel-title pull-left">Entertainment Image</h3>
                                        <div class="btn-group">
                                            @if(!empty($entertainment) && $entertainment->getMediaName() != null)
                                                <img  alt="Image Preview"  src="{{ $entertainment->image }}" class="thumbnail" data-toggle="modal" data-target="#entertainment_category"  style="cursor: pointer;max-width:200px;max-height: 200px;margin-bottom: 0px"/>
                                                <div id="entertainment_category" class="modal fade" role="dialog">
                                                    <div class="modal-dialog">
                                                        <div class="modal-content">
                                                            <div class="modal-header">
                                                                <button type="button" class="close" data-dismiss="modal">&times;</button>
                                                                <h3 class="modal-title">Entertainment Image</h3>
                                                            </div>
                                                            <div class="modal-body">
                                                                <img src="{{ $entertainment->image }}" width="100%;">
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
                        <div class="row">
                            <div class="form-group col-sm-6">
                                <label>Title</label>
                                <input type="text" name="news_title" id="news_title" class="form-control" placeholder="Title" value="{{ $news->title }}">
                            </div>
                            <div class="form-group col-sm-6">
                                <div class="imageupload news_category panel panel-default">
                                    <div class="file-tab panel-body">
                                        <label class="btn btn-default btn-file">
                                            <span>Browse</span>
                                            <input type="file" name="news_events" id="news_events">
                                        </label>
                                        <button type="button" class="btn btn-default">Remove</button>
                                        @if(!empty($news) && $news->getMediaName() != null)
                                            <a href="{{secure_url('admin/main_page/news_events/delete_single')}}" class="btn btn-sm btn-danger pull-right delete">Delete Existing News & Events Image</a>
                                        @endif
                                    </div>
                                    <div class="panel-heading clearfix" style="text-align: center">
                                        <h3 class="panel-title pull-left">News & Events Image</h3>
                                        <div class="btn-group">
                                            @if(!empty($news) && $news->getMediaName() != null)
                                                <img  alt="Image Preview"  src="{{ $news->image }}" class="thumbnail" data-toggle="modal" data-target="#news_category"  style="cursor: pointer;max-width:200px;max-height: 200px;margin-bottom: 0px"/>
                                                <div id="news_category" class="modal fade" role="dialog">
                                                    <div class="modal-dialog">
                                                        <div class="modal-content">
                                                            <div class="modal-header">
                                                                <button type="button" class="close" data-dismiss="modal">&times;</button>
                                                                <h3 class="modal-title">News & Events Image</h3>
                                                            </div>
                                                            <div class="modal-body">
                                                                <img src="{{ $news->image }}" width="100%;">
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
                        <div class="row">
                            <div class="form-group col-sm-6">
                                <label>Title</label>
                                <input type="text" name="offers_title" id="offers_title" class="form-control" placeholder="Title" value="{{ $offers->title }}">
                            </div>
                            <div class="form-group col-sm-6">
                                <div class="imageupload offers_category panel panel-default">
                                    <div class="file-tab panel-body">
                                        <label class="btn btn-default btn-file">
                                            <span>Browse</span>
                                            <input type="file" name="offers_promotions" id="offers_promotions">
                                        </label>
                                        <button type="button" class="btn btn-default">Remove</button>
                                        @if(!empty($offers) && $offers->getMediaName() != null)
                                            <a href="{{secure_url('admin/main_page/offers_promotions/delete_single')}}" class="btn btn-sm btn-danger pull-right delete">Delete Existing Offers & Promotions Image</a>
                                        @endif
                                    </div>
                                    <div class="panel-heading clearfix" style="text-align: center">
                                        <h3 class="panel-title pull-left">Offers & Promotions Image</h3>
                                        <div class="btn-group">
                                            @if(!empty($offers) && $offers->getMediaName() != null)
                                                <img  alt="Image Preview"  src="{{ $offers->image }}" class="thumbnail" data-toggle="modal" data-target="#offers_category"  style="cursor: pointer;max-width:200px;max-height: 200px;margin-bottom: 0px"/>
                                                <div id="offers_category" class="modal fade" role="dialog">
                                                    <div class="modal-dialog">
                                                        <div class="modal-content">
                                                            <div class="modal-header">
                                                                <button type="button" class="close" data-dismiss="modal">&times;</button>
                                                                <h3 class="modal-title">Offers & Promotions Image</h3>
                                                            </div>
                                                            <div class="modal-body">
                                                                <img src="{{ $offers->image }}" width="100%;">
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
                        <div class="row">
                            <div class="form-group col-sm-6">
                                <label>Title</label>
                                <input type="text" name="cinema_title" id="cinema_title" class="form-control" placeholder="Title" value="{{ $cinema->title }}">
                            </div>
                            <div class="form-group col-sm-6">
                                <div class="imageupload cinema_category panel panel-default">
                                    <div class="file-tab panel-body">
                                        <label class="btn btn-default btn-file">
                                            <span>Browse</span>
                                            <input type="file" name="cinema" id="cinema">
                                        </label>
                                        <button type="button" class="btn btn-default">Remove</button>
                                        @if(!empty($cinema) && $cinema->getMediaName() != null)
                                            <a href="{{secure_url('admin/main_page/cinema/delete_single')}}" class="btn btn-sm btn-danger pull-right delete">Delete Existing Cinema Image</a>
                                        @endif
                                    </div>
                                    <div class="panel-heading clearfix" style="text-align: center">
                                        <h3 class="panel-title pull-left">Cinema Image</h3>
                                        <div class="btn-group">
                                            @if(!empty($cinema) && $cinema->getMediaName() != null)
                                                <img  alt="Image Preview"  src="{{ $cinema->image }}" class="thumbnail" data-toggle="modal" data-target="#cinema_category"  style="cursor: pointer;max-width:200px;max-height: 200px;margin-bottom: 0px"/>
                                                <div id="cinema_category" class="modal fade" role="dialog">
                                                    <div class="modal-dialog">
                                                        <div class="modal-content">
                                                            <div class="modal-header">
                                                                <button type="button" class="close" data-dismiss="modal">&times;</button>
                                                                <h3 class="modal-title">Cinema Image</h3>
                                                            </div>
                                                            <div class="modal-body">
                                                                <img src="{{ $cinema->image }}" width="100%;">
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
                        <div class="row">
                            <div class="form-group col-sm-6">
                                <label>Title</label>
                                <input type="text" name="directory_title" id="directory_title" class="form-control" placeholder="Title" value="{{ $directory->title }}">
                            </div>
                            <div class="form-group col-sm-6">
                                <div class="imageupload directory_category panel panel-default">
                                    <div class="file-tab panel-body">
                                        <label class="btn btn-default btn-file">
                                            <span>Browse</span>
                                            <input type="file" name="directory" id="directory">
                                        </label>
                                        <button type="button" class="btn btn-default">Remove</button>
                                        @if(!empty($directory) && $directory->getMediaName() != null)
                                            <a href="{{secure_url('admin/main_page/directory/delete_single')}}" class="btn btn-sm btn-danger pull-right delete">Delete Existing Directory Image</a>
                                        @endif
                                    </div>
                                    <div class="panel-heading clearfix" style="text-align: center">
                                        <h3 class="panel-title pull-left">Directory Image</h3>
                                        <div class="btn-group">
                                            @if(!empty($directory) && $directory->getMediaName() != null)
                                                <img  alt="Image Preview"  src="{{ $directory->image }}" class="thumbnail" data-toggle="modal" data-target="#directory_category"  style="cursor: pointer;max-width:200px;max-height: 200px;margin-bottom: 0px"/>
                                                <div id="directory_category" class="modal fade" role="dialog">
                                                    <div class="modal-dialog">
                                                        <div class="modal-content">
                                                            <div class="modal-header">
                                                                <button type="button" class="close" data-dismiss="modal">&times;</button>
                                                                <h3 class="modal-title">Directory Image</h3>
                                                            </div>
                                                            <div class="modal-body">
                                                                <img src="{{ $directory->image }}" width="100%;">
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
                        <div class="row">
                            <div class="form-group col-sm-6">
                                <label>Title</label>
                                <input type="text" name="gift_card_title" id="gift_card_title" class="form-control" placeholder="Title" value="{{ $gift_card->title }}">
                            </div>
                            <div class="form-group col-sm-6">
                                <div class="imageupload gift_card_category panel panel-default">
                                    <div class="file-tab panel-body">
                                        <label class="btn btn-default btn-file">
                                            <span>Browse</span>
                                            <input type="file" name="gift_card" id="gift_card">
                                        </label>
                                        <button type="button" class="btn btn-default">Remove</button>
                                        @if(!empty($gift_card) && $gift_card->getMediaName() != null)
                                            <a href="{{secure_url('admin/main_page/gift_card/delete_single')}}" class="btn btn-sm btn-danger pull-right delete">Delete Existing Gift Card Image</a>
                                        @endif
                                    </div>
                                    <div class="panel-heading clearfix" style="text-align: center">
                                        <h3 class="panel-title pull-left">Gift Card Image</h3>
                                        <div class="btn-group">
                                            @if(!empty($gift_card) && $gift_card->getMediaName() != null)
                                                <img  alt="Image Preview"  src="{{ $gift_card->image }}" class="thumbnail" data-toggle="modal" data-target="#gift_card_category"  style="cursor: pointer;max-width:200px;max-height: 200px;margin-bottom: 0px"/>
                                                <div id="gift_card_category" class="modal fade" role="dialog">
                                                    <div class="modal-dialog">
                                                        <div class="modal-content">
                                                            <div class="modal-header">
                                                                <button type="button" class="close" data-dismiss="modal">&times;</button>
                                                                <h3 class="modal-title">Gift Card Image</h3>
                                                            </div>
                                                            <div class="modal-body">
                                                                <img src="{{ $gift_card->image }}" width="100%;">
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
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
@section('scripts')
    @parent
    <script>
        var $restaurants_category = $('.restaurants_category');
        var $stores_category = $('.stores_category');
        var $entertainment_category = $('.entertainment_category');
        var $news_category = $('.news_category');
        var $offers_category = $('.offers_category');
        var $cinema_category = $('.cinema_category');
        var $directory_category = $('.directory_category');
        var $gift_card_category = $('.gift_card_category');

        $restaurants_category.imageupload();
        $stores_category.imageupload();
        $entertainment_category.imageupload();
        $news_category.imageupload();
        $offers_category.imageupload();
        $cinema_category.imageupload();
        $directory_category.imageupload();
        $gift_card_category.imageupload();

    </script>
@endsection