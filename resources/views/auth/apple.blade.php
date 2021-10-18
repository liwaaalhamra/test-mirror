<html>
<head>
</head>
<body>
	<div id="appleid-signin" data-color="black" data-border="true" data-type="sign in" style="display: none;"></div>

	{{--<script type="text/javascript" src="{{ asset('assets_cms/libraries/jQuery/jquery-3.1.1.js') }}"></script>--}}
	<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js" integrity="sha512-894YE6QWD5I59HgZOGReFYm4dnWc1Qt5NtvYSaNcOP+u1T9qYdvdihz0PPSiiqn/+/3e7Jo4EaG7TubfWGUrMQ==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
	<script type="text/javascript" src="https://appleid.cdn-apple.com/appleauth/static/jsapi/appleid/1/en_US/appleid.auth.js"></script>
	<script type="text/javascript">
		$(document).ready(function(){
			$("#appleid-signin").trigger('click');
		});
		$("#appleid-signin").click(function(){
			AppleID.auth.init({
				clientId : "{{ env('APPLE_CLIENT_ID') }}",
				scope : 'name email',
				redirectURI : "{{ $redirect_url }}",
				state : "{{ env('APPLE_STATE') }}",
				response_type : "form_post",
                // nonce : '[NONCE]',
                usePopup : false //or false defaults to false
            });
		});
	</script>
</body>
</html>