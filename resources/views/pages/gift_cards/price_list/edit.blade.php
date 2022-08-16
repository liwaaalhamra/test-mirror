@extends('vendor.layout')

@section('content')
    <div class="row">
        <form id="submit_form" action="{{ secure_url('admin/gift_cards/'.$gift_card->id.'/price_list/'.$item->id) }}" method="post" enctype="multipart/form-data">
            <input type="hidden" value="PUT" name="_method">
            <input type="hidden" name="_token" id="_token" value="{{ csrf_token() }}">
            <div class="col-sm-12">
                <div class="panel panel-default">
                    <div class="panel-heading font-bold">Update Price List of {{ucwords($gift_card->name)}}</div>
                    <div class="panel-body">
                        <div class="row">
                            <div class="form-group col-sm-4">
                                <label>Price</label>
                                <input type="text" name="price" id="price" required class="form-control" placeholder="Price" value="{{ $item->price }}">
                            </div>
                        </div>

                        <div class="form-group col-sm-12">
                            <button type="submit" id="submit" class="btn btn-sm btn-success">Save</button>
                            <a href="{{ route('admin.gift_cards.price_list.index',$gift_card->id) }}" class="btn btn-sm btn-dark">Back</a>
                        </div>

                    </div>
                </div>
            </div>
        </form>
    </div>
@endsection

@section('scripts')
    @parent
@endsection
