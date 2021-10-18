<html>
<head>
    <script src="{{env('PAYMENT_CHECKOUT_BASE_URL')}}"
            data-error="errorCallback"
            data-cancel="cancelCallback"
            data-complete="completeCallback"
    >
    </script>
    <script>
        // or if you want to provide a URL:
        errorCallback = "{{$data['return_url']}}"
        cancelCallback = "{{$data['return_url']}}"
        completeCallback = "{{$data['return_url']}}"
    </script>
    <script type="text/javascript">
        // function errorCallback(error) {
        //     console.log(JSON.stringify(error));
        // }
        // function cancelCallback() {
        //     console.log('Payment cancelled');
        // }

        Checkout.configure({
            session: {
                id: "{{$data['session_id']}}"
            },
            merchant: '{{env('PAYMENT_MERCHANT_NUMBER')}}',
            order: {
                amount: '{{$data['amount']}}',
                currency: '{{$data['currency']}}',
                description: '{{$data['description']}}',
                //id: '<unique_order_id>'
                id: '{{strtotime('now')}}_{{$data['user_id']}}_{{$data['order_id']}}'
                {{--id: '{{strtotime('now')}}'--}}
            },
            transaction:{
                reference: '{{$data['user_id']}}_{{$data['order_id']}}'
            },
            interaction: {
                merchant: {
                    name: '{{env('APP_NAME')}} App',
                    address: {
                        line1: '',
                        line2: ''
                    },
                    logo: '{{secure_url('public/uploads/images/logo_payment.png')}}',
                },
                displayControl: {
                    billingAddress: 'HIDE',
                    customerEmail: 'HIDE',
                    shipping: 'HIDE'
                }
            }
        });
    </script>
</head>
<body>
<div class="SizeHandler">
    redirecting, please be patient
</div>
{{--<input type="button" value="Pay with Lightbox" onclick="Checkout.showLightbox();" />--}}
{{--<input type="button" value="Pay with Payment Page" onclick="Checkout.showPaymentPage();" />--}}
<script language="javascript">Checkout.showPaymentPage()</script>

</body>
</html>