<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="shortcut icon"  type="image/png" href="{{env('APP_ENV') == 'local' ? asset('images/logo.png') : secure_asset('images/logo.png')}}"/>
    <title>{{env('APP_NAME')}}</title>
    <link href="https://fonts.googleapis.com/css?family=Open+Sans" rel="stylesheet">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"></script>

    {{--<link rel="stylesheet" type="text/css" href="{{secure_url('public/stripe/assets/payment/assets/css/styles.css')}}">--}}
    {{--<link rel="stylesheet" type="text/css" href="{{secure_url('public/stripe/assets/payment/assets/css/demo.css')}}">--}}
    <link href="https://maxcdn.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css" rel="stylesheet" integrity="sha384-wvfXpqpZZVQGK6TAh5PVlGOfQNHSoD2xbE+QkPxCAFlNEevoEH3Sl0sibVcOQVnN" crossorigin="anonymous">
    <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.0.13/css/all.css" integrity="sha384-DNOHZ68U8hZfKXOrtjWvjxusGo9WQnrNx2sqG0tfsghAvtVlRW3tvkXWZh58N9jp" crossorigin="anonymous">
</head>

<body>
<div class="app app-header-fixed ">
    <div style="text-align: center">
        <h4><img style="width: 100%" src="{{env('APP_ENV') == 'local' ? asset('resources/assets/images/logo_new.png') : secure_asset('resources/assets/images/logo_new.png')}}" /></h4>
    </div>
    <div class="panel panel-default" style="text-align: center">
        <div class="panel-heading" style="background-color: #4EBC97;color: white;">
            <h3>{{env('APP_NAME')}} Payment</h3>
        </div>
        @if(!$data['is_test'])
            @if(!$data['is_new'])
                <div class="row wrapper" style="min-height: 20px;">
                    <form action="{{secure_url('/api/stripe_payment_response')}}" method="post" id="paymentForm">
                        <input type="hidden" name="user_id" value="{{$data['user_id']}}">
                        <input type="hidden" name="order_id" value="{{$data['order_id']}}">
                        <input type="hidden" name="post_id" value="{{$data['post_id']}}">
                        <input type="hidden" name="type" value="{{$data['is_test']}}">
                        <input type="hidden" name="token" value="{{$data['token']}}">
                        <button type="submit" class="btn btn-lg btn-danger" >
                            <i class="fas fa-id-card"></i> Already A Customer
                        </button>
                    </form>
                </div>
            @endif
            <div class="row wrapper " style="min-height: 100px;margin-top: 10%;">
                <form action="{{secure_url('/api/stripe_payment_response')}}" method="post" id="paymentForm">
                    <input type="hidden" name="user_id" value="{{$data['user_id']}}">
                    <input type="hidden" name="order_id" value="{{$data['order_id']}}">
                    <input type="hidden" name="post_id" value="{{$data['post_id']}}">
                    <input type="hidden" name="type" value="{{$data['is_test']}}">
                    <script src="https://checkout.stripe.com/checkout.js" class="stripe-button"
                            data-key="{{env('STRIPE_TEST_PUBLISHABLE_KEY')}}"
                            data-description="Charge 1 Post"
                            data-zip-code="true"
                            data-image="https://stripe.com/img/documentation/checkout/marketplace.png"
                            data-name="{{env('APP_NAME')}}, LLC"
                            data-amount="{{$data['amount']}}"
                            data-currency="{{env('APP_CURRENCY')}}"
                            data-locale="auto"></script>
                </form>
            </div>
        @else
            @if(!$data['is_new'])
                <div class="row wrapper" style="min-height: 20px;">
                    <form action="{{secure_url('/api/stripe_payment_response')}}" method="post" id="paymentForm">
                        <input type="hidden" name="user_id" value="{{$data['user_id']}}">
                        <input type="hidden" name="order_id" value="{{$data['order_id']}}">
                        <input type="hidden" name="post_id" value="{{$data['post_id']}}">
                        <input type="hidden" name="type" value="{{$data['is_test']}}">
                        <input type="hidden" name="token" value="{{$data['token']}}">
                        <button type="submit" class="btn btn-lg btn-danger" >
                            <i class="fas fa-id-card"></i> Already A Customer
                        </button>
                    </form>
                </div>
            @endif
            <div class="row wrapper " style="min-height: 100px;margin-top: 10%;">
                <form action="{{secure_url('/api/stripe_payment_response')}}" method="post" id="paymentForm">
                    <input type="hidden" name="user_id" value="{{$data['user_id']}}">
                    <input type="hidden" name="order_id" value="{{$data['order_id']}}">
                    <input type="hidden" name="post_id" value="{{$data['post_id']}}">
                    <input type="hidden" name="type" value="{{$data['is_test']}}">
                    <script src="https://checkout.stripe.com/checkout.js" class="stripe-button"
                            data-key="{{env('STRIPE_TEST_PUBLISHABLE_KEY')}}"
                            data-description="Charge 1 Post"
                            data-zip-code="true"
                            data-image="https://stripe.com/img/documentation/checkout/marketplace.png"
                            data-name="{{env('APP_NAME')}}, LLC"
                            data-amount="{{$data['amount']}}"
                            data-currency="{{env('APP_CURRENCY')}}"
                            data-locale="auto"></script>
                </form>
            </div>
        @endif

    </div>
    {{--Footer--}}
</div>
<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.12.4/jquery.min.js"></script>
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"></script>
{{--<script src="{{secure_url('public/stripe/assets/payment/assets/js/jquery.payform.min.js')}}" charset="utf-8"></script>--}}
{{--<script src="{{secure_url('public/stripe/assets/payment/assets/js/script.js')}}"></script>--}}
</body>

</html>