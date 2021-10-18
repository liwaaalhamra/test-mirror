@extends('vendor.layout')

@section('content')
    <div class="row">
        <form action="{{ secure_url('admin/cms_users/'. $user->id) }}" method="post">
            <input type="hidden" value="PUT" name="_method">
            {{ csrf_field() }}
            <div class="col-sm-12">
                <div class="panel panel-default">
                    <div class="panel-heading font-bold">{{$content_header}}</div>
                    <div class="panel-body">
                        <div class="row">
                            <div class="form-group col-sm-6">
                                <label>Choose Role</label>
                                <select class="form-control selectpicker" title="Choose..."  data-live-search="true" name="role_id" required>
                                    @foreach($roles as $item)
                                        <option data-tokens="{{$item->name}}" @if($user->role_id == $item->id) selected @endif value="{{ $item->id }}">{{ ucwords($item->name) }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        {{--<div class="row">--}}
                            {{--<div class="form-group col-sm-4">--}}
                                {{--<label>Cost Center</label><small style="color: red">"all" for all cost center types</small>--}}
                                {{--<input type="text" name="cost_center" class="form-control" placeholder="Cost Center" value="{{ $user->cost_center }}">--}}
                            {{--</div>--}}
                        {{--</div>--}}
                        <div class="row">
                            <div class="form-group col-sm-4">
                                <label>First Name</label>
                                <input type="text" name="first_name" class="form-control" required placeholder="User's First Name" value="{{ $user->first_name }}">
                            </div>
                            <div class="form-group col-sm-4">
                                <label>Last Name</label>
                                <input type="text" name="last_name" class="form-control" required placeholder="User's Last Name" value="{{ $user->last_name }}">
                            </div>
                            <div class="form-group col-sm-4">
                                <label>Email</label>
                                <input type="text" name="email" class="form-control" required placeholder="User's Email" value="{{ $user->email }}">
                            </div>
                        </div>
                        <div class="row">
                            <div class="form-group col-sm-6">
                                <button type="submit" class="btn btn-sm btn-primary">Save</button>
                                <a href="{{ route('admin.cms_users.index') }}" class="btn btn-sm btn-dark">Back</a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-sm-6">
                <div class="panel panel-default">
                    <div class="panel-heading font-bold"><i class="fa fa-unlock-alt" aria-hidden="true"></i> Change Password</div>
                    <div class="panel-body">
                        <small style="color: red">leave it empty if you don't want to change the password</small>
                        <div class="form-group">
                            <label>Old Password</label>
                            <input type="password" id="old_password" name="old_password" class="form-control">
                        </div>
                        <div class="form-group">
                            <label>New Password</label>
                            <input type="password" id="password" name="password" class="form-control" placeholder="The password must be at least 6 characters.">
                        </div>
                        <div class="form-group">
                            <label>Confirm Password</label>
                            <input type="password" id="password_confirmation" name="password_confirmation" class="form-control">
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
