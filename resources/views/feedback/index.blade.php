<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8"/>
    <title>{{env('APP_NAME')}} | Feedback</title>
    <meta name="description" content="{{env('APP_NAME')}} Backend"/>
    <link rel="shortcut icon"  type="image/png" href="{{asset('public/uploads/images/logo_new.png')}}"/>
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1" />
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <meta name="token" value="{{ csrf_token() }}">
    <link rel="manifest" href="{{secure_url('/manifest.json')}}" />
    @section('style')
        @include('vendor.partials.style')
    @show
</head>
<body>
<div class="app app-header-fixed ">

    {{--Content--}}
    <div id="content" class="app-content" role="main">
        <div class="app-content-body ">
            <div class="wrapper-md">
                @include('vendor.partials.flash')
                <div class="container py-5">

                    @if(session('status'))
                        <div class="alert alert-success alert-dismissible" role="alert">
                            {{ session('status') }}
                            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                    @endif

                    <h3 class="mb-4"><b>Feedback</b></h3>

                    <form method="POST" action="">
                        <input type="hidden" name="_token" id="_token" value="{{ csrf_token() }}">
                        <div class="form-group">
                            <input type="text" class="form-control" placeholder="Full Name" name="full_name">

                        </div>
                        <div class="form-group">
                            <input type="email" class="form-control" placeholder="Email Address" name="email">

                        </div>
                        <div class="form-group">
                            <input type="number" class="form-control" placeholder="Phone number" name="phone_number">

                        </div>
                        <div class="form-group">
                            <textarea class="form-control" rows="7" placeholder="Message" name="message"></textarea>
                        </div>

                        <button type="submit" class="btn btn-primary">Submit</button>
                    </form>
                </div>

            </div>
        </div>
    </div>
</div>

{{--Scripts--}}
@section('scripts')
    @include('vendor.partials.scripts')
@show

</body>
</html>