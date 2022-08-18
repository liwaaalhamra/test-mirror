@extends('vendor.layout')

@section('content')
    <div class="row">
        <div class="col-sm-12">
            <div class="panel panel-default">
                <div class="panel-heading font-bold">{{$content_header}}</div>
                <div class="panel-body">
                    <form action="{{ secure_url('admin/cms_users') }}" method="post">
                        {{ csrf_field() }}
                        <div class="row">
                            <div class="form-group col-sm-4">
                                <label>Choose Role</label>
                                <select class="form-control selectpicker" title="Choose..."  data-live-search="true" name="role_id" required>
                                    @foreach($roles as $item)
                                        <option data-tokens="{{$item->name}}" value="{{ $item->id }}">{{ ucwords($item->name) }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        {{--<div class="row">--}}
                            {{--<div class="form-group col-sm-4">--}}
                                {{--<label>Cost Center</label><small style="color: red">"all" for all cost center types</small>--}}
                                {{--<input type="text" name="cost_center" class="form-control" placeholder="Cost Center" value="{{ old('cost_center') }}">--}}
                            {{--</div>--}}
                        {{--</div>--}}
                        <div class="row">

                            <div class="form-group col-sm-4">
                                <label>First Name</label>
                                <input type="text" name="first_name" class="form-control" required placeholder="User's First Name" value="{{ old('first_name') }}">
                            </div>
                            <div class="form-group col-sm-4">
                                <label>Last Name</label>
                                <input type="text" name="last_name" class="form-control" required placeholder="User's Last Name" value="{{ old('last_name') }}">
                            </div>
                            <div class="form-group col-sm-4">
                                <label>Email</label>
                                <input type="text" name="email" class="form-control" required placeholder="User's Email" value="{{ old('email') }}">
                            </div>
                        </div>
                        <div class="row">
                            <div class="form-group col-sm-6">
                                <label>Password</label>
                                <input type="password" id="password" name="password" class="form-control" placeholder="The password must be at least 6 characters." required>
                            </div>
                            <div class="form-group col-sm-6">
                                <label>Confirm Password</label>
                                <input type="password" id="password_confirmation" name="password_confirmation" class="form-control" required>
                            </div>
                        </div>
                        <div class="row">
                            <div class="form-group col-sm-6">
                                <button type="submit" class="btn btn-sm btn-success">Save</button>
                                <a href="{{ route('admin.cms_users.index') }}" class="btn btn-sm btn-dark">Back</a>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    @parent
    <script>

    var password = document.getElementById("password")
            , password_confirmation = document.getElementById("password_confirmation");

    function validatePassword(){
        if(password.value != password_confirmation.value) {
            password_confirmation.setCustomValidity("Passwords Don't Match");
        } else {
            password_confirmation.setCustomValidity('');
        }
    }

    password.onchange = validatePassword;
    password_confirmation.onkeyup = validatePassword;
    </script>
@endsection
