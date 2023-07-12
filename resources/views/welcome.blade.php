<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <title>{{ env('APP_NAME') }}</title>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=0">
    {{-- Fonts --}}
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;700&display=swap" rel="stylesheet">
    {{-- Libraries --}}
    <link rel="stylesheet" type="text/css" href="{{ env('APP_ENV') == 'local' ? asset('theme/css/bootstrap.min.css') : secure_asset('public/theme/css/bootstrap.min.css') }}">
    {{-- Main CSS --}}
    <style type="text/css">
        html,
        body{
            font-family: 'Roboto', sans-serif;
            overflow-x: hidden;
            height: 100vh;
        }
        *{
            outline: none !important;
        }
        a{
            color: inherit !important;
            text-decoration: none !important;
        }
        b{
            font-weight: 700;
        }
        img{
            width: 100%;
        }
        .logo{
            max-width: 250px;
        }
    </style>
</head>
<body>

<div class="d-table w-100 h-100">
    <div class="d-table-cell align-middle">
        <div class="container">
            <img class="logo d-block mx-auto mb-5" src="{{ env('APP_ENV') == 'local' ? asset('resources/assets/images/logo_new.png') : secure_asset('public/resources/assets/images/logo_new.png') }}">

            <h3 class="text-uppercase text-center mb-4"><b>Download {{ env('APP_NAME') }} App Now!</b></h3>

            <div class="row justify-content-center">
                <div class="col-sm-10 col-md-8 col-lg-5">
                    <div class="row">
                        <div class="col">
                            <a href="{{ env('GOOGLE_PLAY_STORE_APP_LINK') }}" target="_blank">
                                <img class="shadow" src="{{ env('APP_ENV') == 'local' ? asset('uploads/images/google_play.svg') : secure_asset('public/uploads/images/google_play.svg') }}">
                            </a>
                        </div>
                        <div class="col">
                            <a href="{{ env('APPLE_STORE_APP_LINK') }}" target="_blank">
                                <img class="shadow" src="{{ env('APP_ENV') == 'local' ? asset('uploads/images/app_store.svg') : secure_asset('public/uploads/images/app_store.svg') }}">
                            </a>
                        </div>
                    </div>
                </div>
            </div>

            <div class="text-center mt-5">
                <a href="{{ route('feedback') }}"><b>Feedback</b></a>
                <span>|</span>
                <a href="{{ route('privacy.show') }}"><b>Privacy policy</b></a>
            </div>

            {{-- <footer class="text-center mt-5">
                Powered by <a href="{{ env('POWERED_BY_LINK') }}" target="_blank"><b>{{ env('POWERED_BY') }}</b></a>
            </footer> --}}
        </div>
    </div>
</div>

</body>
</html>