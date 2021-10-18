<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8"/>
    <title>{{env('APP_NAME')}} | CMS</title>
    <meta name="description" content="{{env('APP_NAME')}} Backend"/>
    <link rel="shortcut icon"  type="image/png" href="{{asset('public/uploads/images/logo.png')}}"/>
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1" />
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <meta name="token" value="{{ csrf_token() }}">
    <link rel="manifest" href="{{secure_url('/manifest.json')}}" />
    {{--<script src="https://cdn.onesignal.com/sdks/OneSignalSDK.js" async=""></script>--}}
    {{--<script>--}}
        {{--var OneSignal = window.OneSignal || [];--}}
        {{--OneSignal.push(function() {--}}
            {{--OneSignal.init({--}}
                {{--appId: "0ec07dbc-9628-4e2e-a4bf-aa32f4f864f4",--}}
            {{--});--}}
        {{--});--}}
    {{--</script>--}}
    {{--Styles--}}
    @section('style')
        @include('vendor.partials.style')
    @show
</head>
<body>
<div class="app app-header-fixed ">

    {{--Header--}}
    @include('vendor.partials.header')

    {{--Menu--}}
    @include('vendor.partials.menu')

    {{--Content--}}
    <div id="content" class="app-content" role="main">
        <div class="app-content-body ">
            <div class="bg-light lter b-b wrapper-md">
                <h1 class="m-n font-thin h3">
                    {{ $content_header or '' }}
                </h1>
            </div>
            <div class="wrapper-md">
                @include('vendor.partials.flash')
                @yield('content')
            </div>
        </div>
    </div>

    {{--Footer--}}
    @include('vendor.partials.footer')
</div>

{{--Scripts--}}
@section('scripts')
    @include('vendor.partials.scripts')
@show

</body>
</html>
