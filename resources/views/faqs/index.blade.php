<!DOCTYPE html>
<html lang="en">
<head>
  <title>{{env('APP_NAME')}}</title>
  <meta charset="UTF-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
  <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=0">

  <!-- Generate Favicon on https://realfavicongenerator.net/ -->

  <link rel="stylesheet" type="text/css" href="{{env('APP_ENV') == 'local' ? asset('public/faqs/assets/libraries/bootstrap-4.3.1/bootstrap.min.css') : secure_asset('public/faqs/assets/libraries/bootstrap-4.3.1/bootstrap.min.css')}}">
  <link rel="stylesheet" type="text/css" href="{{env('APP_ENV') == 'local' ? asset('public/faqs/assets/libraries/slick-1.8.1/slick/slick.css') : secure_asset('public/faqs/assets/libraries/slick-1.8.1/slick/slick.css')}}">
  <link rel="stylesheet" type="text/css" href="{{env('APP_ENV') == 'local' ? asset('public/faqs/assets/libraries/slick-1.8.1/slick/slick-theme.css') : secure_asset('public/faqs/assets/libraries/slick-1.8.1/slick/slick-theme.css')}}">
  <link rel="stylesheet" type="text/css" href="{{env('APP_ENV') == 'local' ? asset('public/faqs/assets/libraries/animate/animate.css') : secure_asset('public/faqs/assets/libraries/animate/animate.css')}}">
  <!-- <link href="https://fonts.googleapis.com/css?family=Montserrat:400,700&display=swap" rel="stylesheet"> -->
  <link rel="stylesheet" type="text/css" href="{{env('APP_ENV') == 'local' ? asset('public/faqs/assets/css/main.css') : secure_asset('public/faqs/assets/css/main.css')}}"/>
</head>
<body>
<div class="py-5">
  <div class="container py-3">
    <ul class="list-style-none faqs-list text-black" @if($lang == 'ar') style="text-align: right" @endif>
      @foreach($faqs as $faq)
        <li class="bg-grey-2 py-3 px-4">
          @if($lang == 'en')
          <b>{{$faq->question_en}}</b>
          <div class="content">
            <div class="pt-3">
              {{$faq->answer_en}}
            </div>
          </div>
          @else
            <b>{{$faq->question_ar}}</b>
            <div class="content">
              <div class="pt-3">
                {{$faq->answer_ar}}
              </div>
            </div>
          @endif
        </li>
      @endforeach
    </ul>
  </div>
</div>
<script type="text/javascript" src="{{env('APP_ENV') == 'local' ? asset('public/faqs/assets/js/jquery-3.1.1.js') : secure_asset('public/faqs/assets/js/jquery-3.1.1.js')}}"></script>
<script type="text/javascript" src="{{env('APP_ENV') == 'local' ? asset('public/faqs/assets/libraries/bootstrap-4.3.1/bootstrap.min.js') : secure_asset('public/faqs/assets/libraries/bootstrap-4.3.1/bootstrap.min.js')}}"></script>
<script type="text/javascript" src="{{env('APP_ENV') == 'local' ? asset('public/faqs/assets/libraries/slick-1.8.1/slick/slick.min.js') : secure_asset('public/faqs/assets/libraries/slick-1.8.1/slick/slick.min.js')}}"></script>
<script type="text/javascript" src="{{env('APP_ENV') == 'local' ? asset('public/faqs/assets/js/main.js') : secure_asset('public/faqs/assets/js/main.js')}}"></script>
</body>
</html>
