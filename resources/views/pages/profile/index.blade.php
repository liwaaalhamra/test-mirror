@extends('vendor.layout')

@section('content')
    <div class="row">
        <div class="col-sm-4">
            <div class="panel panel-default">
                <div class="panel-heading font-bold">Change Admin Password</div>
                <div class="panel-body">
                    <form action="{{ route('admin.profile.changepassword') }}" method="post">
                        {{ csrf_field() }}
                        <div class="form-group">
                            <input type="password" name="password" class="form-control" placeholder="New Password" required>
                        </div>
                        <div class="form-group">
                            <input type="password" name="password_confirmation" class="form-control" placeholder="Confirm Password" required>
                        </div>
                        <button type="submit" class="btn btn-sm btn-success">Save</button>
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
        $imageupload.imageupload();
    </script>
@endsection
