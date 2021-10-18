@extends('vendor.layout')

@section('content')
    <div class="row">
        <div class="col-sm-12">
            <div class="panel panel-default">
                <div class="panel-heading font-bold">Price List of {{ucwords($gift_card->name)}}</div>
                <div class="panel-body">
                    <form id="submit_form" action="{{ secure_url('admin/gift_cards/'.$gift_card->id.'/price_list') }}" method="post" enctype="multipart/form-data">
                        <input type="hidden" name="_token" id="_token" value="{{ csrf_token() }}">
                        <div class="row">
                            <div class="form-group col-sm-4">
                                <label>Price</label>
                                <input type="text" name="price" id="price" class="form-control" required placeholder="Price" value="{{ old('price') }}">
                            </div>
                        </div>


                        <button type="submit" id="submit" class="btn btn-sm btn-primary">Save</button>
                        <a href="{{ route('admin.gift_cards.price_list.index',$gift_card->id) }}" class="btn btn-sm btn-dark">Back</a>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
@section('scripts')
    @parent

@endsection
