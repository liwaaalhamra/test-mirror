@extends('vendor.layout')

@section('content')
    <div class="row">
        <div class="col-sm-6">
            <div class="panel panel-default">
                <div class="panel-heading font-bold">Single Push</div>
                <div class="panel-body">
                    <form action="{{ route('admin.notification.SinglePush.store') }}" method="post">
                        {{ csrf_field() }}
                        <div class="form-group">
                            <label class="control-label" >Users</label>
                            <div class="form-group">
                                <select class="form-control selectpicker" id="user" data-live-search="true" name="user" required>
                                    <option data-tokens="" value="" selected>Choose User</option>
                                    @foreach($users as $user)
                                        <option data-tokens="{{$user->first_name}} {{$user->last_name}}  {{$user->email}}" value="{{$user->id}}" > {{$user->first_name}} {{$user->last_name}} - {{$user->email}}</option>
                                    @endforeach
                                </select>
                            </div>
                            <span class="help-block"></span>
                        </div>
                        <div class="form-group">
                            <input name="title" class="form-control" required placeholder="Title"/>
                        </div>
                        <div class="form-group">
                            <textarea name="message" class="form-control" required placeholder="Message"></textarea>
                        </div>
                        <button type="submit" class="btn btn-sm btn-primary">Send</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
