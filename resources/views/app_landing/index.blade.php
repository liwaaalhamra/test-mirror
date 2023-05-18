<!DOCTYPE html>
<html lang="zxx" class="no-js">

<head>
	<!-- Mobile Specific Meta -->
	<meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
	<!-- Favicon-->
	<link rel="shortcut icon" href="{{env('APP_ENV') == 'local' ? asset('resources/assets/images/logo_new.png') : secure_asset('resources/assets/images/logo_new.png')}}">
	<!-- Author Meta -->
	<meta name="author" content="codepixer">
	<!-- Meta Description -->
	<meta name="description" content="">
	<!-- Meta Keyword -->
	<meta name="keywords" content="">
	<!-- meta character set -->
	<meta charset="UTF-8">
	<!-- Site Title -->
	<title>{{env('APP_NAME')}}</title>

	<link href="https://fonts.googleapis.com/css?family=Poppins:400,600|Roboto:400,400i,500" rel="stylesheet">
    <link href="https://maxcdn.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css" rel="stylesheet" integrity="sha384-wvfXpqpZZVQGK6TAh5PVlGOfQNHSoD2xbE+QkPxCAFlNEevoEH3Sl0sibVcOQVnN" crossorigin="anonymous">
    <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.0.13/css/all.css" integrity="sha384-DNOHZ68U8hZfKXOrtjWvjxusGo9WQnrNx2sqG0tfsghAvtVlRW3tvkXWZh58N9jp" crossorigin="anonymous">
	<!--
			CSS
			============================================= -->
	<link rel="stylesheet" href="{{env('APP_ENV') == 'local' ? asset('public/app_landing/css/linearicons.css') : secure_asset('public/app_landing/css/linearicons.css')}}">
	<link rel="stylesheet" href="{{env('APP_ENV') == 'local' ? asset('public/app_landing/css/font-awesome.min.css') : secure_asset('public/app_landing/css/font-awesome.min.css')}}">
	<link rel="stylesheet" href="{{env('APP_ENV') == 'local' ? asset('public/app_landing/css/bootstrap.css') : secure_asset('public/app_landing/css/bootstrap.css')}}">
	<link rel="stylesheet" href="{{env('APP_ENV') == 'local' ? asset('public/app_landing/css/magnific-popup.css') : secure_asset('public/app_landing/css/magnific-popup.css')}}">
	<link rel="stylesheet" href="{{env('APP_ENV') == 'local' ? asset('public/app_landing/css/nice-select.css') : secure_asset('public/app_landing/css/nice-select.css')}}">
	<link rel="stylesheet" href="{{env('APP_ENV') == 'local' ? asset('public/app_landing/css/hexagons.min.css') : secure_asset('public/app_landing/css/hexagons.min.css')}}">
	<link rel="stylesheet" href="{{env('APP_ENV') == 'local' ? asset('public/app_landing/css/owl.carousel.css') : secure_asset('public/app_landing/css/owl.carousel.css')}}">
	<link rel="stylesheet" href="{{env('APP_ENV') == 'local' ? asset('public/app_landing/css/main.css') : secure_asset('public/app_landing/css/main.css')}}">

	<style>

		.footer-area .single-footer-widget textarea{
			height: 100px;
			border: none;
			width: 80% !important;
			font-weight: 300;
			background: #f9f9ff;
			color: #222222;
			padding-left: 20px;
			border-radius: 0;
			font-size: 14px;
			padding: 10px;
			margin-top: 20px;
		}

		.footer-area .single-footer-widget ul li{
			display: inline-block;
			margin: 0 20px;
		}

		.footer-area .footer-bottom{
			text-align: center;
			margin-top: 12px !important;
		}

		.section-gap{
			padding: 100px 0;
		}

		.container::after{
			display: none;
		}

		.footer-area.section-gap{
			margin-top: 100px;
		}

		@media screen and (max-width: 600px) {
			.footer-area .single-footer-widget ul li {
				display: inline-block;
				margin: 0px;
				width: 100%;
				text-align: center;
			}


		}
	</style>
</head>

<body>
<!-- start header Area -->
<header id="header">
<div class="container main-menu">
<div class="row align-items-center justify-content-between d-flex">
<div id="logo">
<a href="index.html"><img src="{{env('APP_ENV') == 'local' ? asset('public/app_landing/img/bob.png?') : secure_asset('public/app_landing/img/bob.png?')}}" alt="" title="" /></a>
</div>
{{--<nav id="nav-menu-container">--}}
{{--<ul class="nav-menu">--}}
{{--<li class="menu-active"><a href="index.html">Home</a></li>--}}
{{--<li><a href="about.html">About</a></li>--}}
{{--<li><a href="pricing.html">Pricing</a></li>--}}
{{--<li class="menu-has-children"><a href="">Pages</a>--}}
{{--<ul>--}}
{{--<li><a href="elements.html">Elements</a></li>--}}
{{--</ul>--}}
{{--</li>--}}
{{--<li class="menu-has-children"><a href="">Blog</a>--}}
{{--<ul>--}}
{{--<li><a href="blog-home.html">Blog Home</a></li>--}}
{{--<li><a href="blog-single.html">Blog Single</a></li>--}}
{{--</ul>--}}
{{--</li>--}}
{{--<li><a href="contact.html">Contact</a></li>--}}
{{--</ul>--}}
{{--</nav>--}}
</div>
</div>
</header>
<!-- end header Area -->

<!-- start banner Area -->
<section class="home-banner-area" style="padding: 0">
	<div class="container">
		<div class="row fullscreen d-flex align-items-center justify-content-between">
			<div class="home-banner-content col-lg-6 col-md-6">
				<h1>
					BOB Community App
				</h1>
				<p>With BoB Community, you will be able to join events, share your thoughts and recommendations, and stay in the loop of all the BoB Community’s activities, but most importantly, have fun!</p>
				<div class="download-button d-flex flex-row justify-content-start">
					<div class="buttons flex-row d-flex">
						<i class="fa fa-apple" aria-hidden="true"></i>
						<div class="desc">
							<a href="https://apple.co/33f95ur">
								<p>
									<span>Available</span> <br>
									on App Store
								</p>
							</a>
						</div>
					</div>
					<div class="buttons dark flex-row d-flex">
						<i class="fa fa-android" aria-hidden="true"></i>
						<div class="desc">
							<a href="https://play.google.com/store/apps/details?id=com.tedmob.bobcommunity">
								<p>
									<span>Available</span> <br>
									on Play Store
								</p>
							</a>
						</div>
					</div>
					
				</div>
			</div>
			<div class="banner-img col-lg-4 col-md-6">
				<img class="img-fluid" src="{{env('APP_ENV') == 'local' ? asset('public/app_landing/img/app_banner.png') : secure_asset('public/app_landing/img/app_banner.png')}}" alt="">
			</div>
		</div>
	</div>
</section>
<!-- End banner Area -->

<!-- Start fact Area -->
<!--<section class="fact-area">-->
<!--<div class="container">-->
<!--<div class="fact-box">-->
<!--<div class="row align-items-center">-->
<!--<div class="col single-fact">-->
<!--<h2>100K+</h2>-->
<!--<p>Total Downloads</p>-->
<!--</div>-->
<!--<div class="col single-fact">-->
<!--<h2>10K+</h2>-->
<!--<p>Positive Reviews</p>-->
<!--</div>-->
<!--<div class="col single-fact">-->
<!--<h2>50K+</h2>-->
<!--<p>Daily Visitors</p>-->
<!--</div>-->
<!--<div class="col single-fact">-->
<!--<h2>0.02%</h2>-->
<!--<p>Uninstallation Rate</p>-->
<!--</div>-->
<!--<div class="col single-fact">-->
<!--<h2>15K+</h2>-->
<!--<p>Pro User</p>-->
<!--</div>-->
<!--</div>-->
<!--</div>-->
<!--</div>-->
<!--</section>-->
<!-- End fact Area -->

<!-- Start feature Area -->
<!--<section class="feature-area section-gap-top">-->
<!--<div class="container">-->
<!--<div class="row d-flex justify-content-center">-->
<!--<div class="col-lg-6">-->
<!--<div class="section-title text-center">-->
<!--<h2>Unique Features</h2>-->
<!--<p>Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod tempor incididunt ut labore et dolore-->
<!--magna aliqua.-->
<!--</p>-->
<!--</div>-->
<!--</div>-->
<!--</div>-->
<!--<div class="row">-->
<!--<div class="col-lg-4 col-md-6">-->
<!--<div class="single-feature">-->
<!--<a href="#" class="title">-->
<!--<span class="lnr lnr-book"></span>-->
<!--<h3>Expert Technicians</h3>-->
<!--</a>-->
<!--<p>-->
<!--Usage of the Internet is becoming more common due to rapid advancement of technology and power.-->
<!--</p>-->
<!--</div>-->
<!--</div>-->
<!--<div class="col-lg-4 col-md-6">-->
<!--<div class="single-feature">-->
<!--<a href="#" class="title">-->
<!--<span class="lnr lnr-book"></span>-->
<!--<h3>Professional Service</h3>-->
<!--</a>-->
<!--<p>-->
<!--Usage of the Internet is becoming more common due to rapid advancement of technology and power.-->
<!--</p>-->
<!--</div>-->
<!--</div>-->
<!--<div class="col-lg-4 col-md-6">-->
<!--<div class="single-feature">-->
<!--<a href="#" class="title">-->
<!--<span class="lnr lnr-book"></span>-->
<!--<h3>Great Support</h3>-->
<!--</a>-->
<!--<p>-->
<!--Usage of the Internet is becoming more common due to rapid advancement of technology and power.-->
<!--</p>-->
<!--</div>-->
<!--</div>-->

<!--</div>-->
<!--</div>-->
<!--</section>-->
<!-- End feature Area -->

<!-- Start about Area -->
<!--<section class="about-area" style="padding: 0">-->
<!--<div class="container">-->
<!--<div class="row align-items-center">-->
<!--<div class="col-lg-5 home-about-left">-->
<!--<img class="img-fluid" src="img/iphone.png" alt="">-->
<!--</div>-->
<!--<div class="offset-lg-1 col-lg-5 home-about-right">-->
<!--<h1>-->
<!--We Believe that <br>-->
<!--Interior beautifies the <br>-->
<!--Total Architecture-->
<!--</h1>-->
<!--<p>-->
<!--Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod tempor incididunt ut labore et dolore-->
<!--magna aliqua.Ut-->
<!--enim ad minim. Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod tempor.-->
<!--</p>-->
<!--<a class="primary-btn text-uppercase" href="#">Get Details</a>-->
<!--</div>-->
<!--<div class="col-lg-6 home-about-right home-about-right2">-->
<!--<h1>-->
<!--We Believe that <br>-->
<!--Interior beautifies the <br>-->
<!--Total Architecture-->
<!--</h1>-->
<!--<p>-->
<!--Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod tempor incididunt ut labore et dolore-->
<!--magna aliqua.Ut-->
<!--enim ad minim. Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod tempor.-->
<!--</p>-->
<!--<div class="download-button d-flex flex-row justify-content-start">-->
<!--<div class="buttons flex-row d-flex">-->
<!--<i class="fa fa-apple" aria-hidden="true"></i>-->
<!--<div class="desc">-->
<!--<a href="#">-->
<!--<p>-->
<!--<span>Available</span> <br>-->
<!--on App Store-->
<!--</p>-->
<!--</a>-->
<!--</div>-->
<!--</div>-->
<!--<div class="buttons dark flex-row d-flex">-->
<!--<i class="fa fa-android" aria-hidden="true"></i>-->
<!--<div class="desc">-->
<!--<a href="#">-->
<!--<p>-->
<!--<span>Available</span> <br>-->
<!--on Play Store-->
<!--</p>-->
<!--</a>-->
<!--</div>-->
<!--</div>-->
<!--</div>-->
<!--</div>-->
<!--<div class="col-lg-5 home-about-left">-->
<!--<img class="img-fluid" src="img/iphone.png" alt="">-->
<!--</div>-->
<!--</div>-->
<!--</div>-->
<!--</section>-->
<!-- End about Area -->

<!-- Start about-video Area -->
<!--<section class="about-video-area section-gap">-->
<!--<div class="vdo-bg">-->
<!--<div class="container">-->
<!--<div class="row align-items-center justify-content-center">-->
<!--<div class="col-lg-12 about-video-right justify-content-center align-items-center d-flex relative">-->
<!--<div class="overlay overlay-bg"></div>-->
<!--<a class="play-btn" href="https://www.youtube.com/watch?v=ARA0AxrnHdM"><img class="img-fluid mx-auto" src="img/play-btn.png"-->
<!--alt=""></a>-->
<!--</div>-->
<!--</div>-->
<!--</div>-->
<!--</div>-->
<!--</section>-->
<!-- End about-video Area -->

<!-- Start Testimonials Area -->
<!--<section class="testimonials-area section-gap-top">-->
<!--<div class="container">-->
<!--<div class="testi-slider owl-carousel" data-slider-id="1">-->
<!--<div class="item">-->
<!--<div class="testi-item">-->
<!--<img src="img/quote.png" alt="">-->
<!--<h4>Fanny Spencer</h4>-->
<!--<ul class="list">-->
<!--<li><a href="#"><i class="fa fa-star"></i></a></li>-->
<!--<li><a href="#"><i class="fa fa-star"></i></a></li>-->
<!--<li><a href="#"><i class="fa fa-star"></i></a></li>-->
<!--<li><a href="#"><i class="fa fa-star"></i></a></li>-->
<!--<li><a href="#"><i class="fa fa-star"></i></a></li>-->
<!--</ul>-->
<!--<div class="wow fadeIn" data-wow-duration="1s">-->
<!--<p>-->
<!--As conscious traveling Paup ers we must always be oncerned about our dear Mother Earth. If you think about it,-->
<!--you travel-->
<!--across her face <br> and She is the host to your journey.-->
<!--</p>-->
<!--</div>-->
<!--</div>-->
<!--</div>-->
<!--<div class="item">-->
<!--<div class="testi-item">-->
<!--<img src="img/quote.png" alt="">-->
<!--<h4>Fanny Spencer</h4>-->
<!--<ul class="list">-->
<!--<li><a href="#"><i class="fa fa-star"></i></a></li>-->
<!--<li><a href="#"><i class="fa fa-star"></i></a></li>-->
<!--<li><a href="#"><i class="fa fa-star"></i></a></li>-->
<!--<li><a href="#"><i class="fa fa-star"></i></a></li>-->
<!--<li><a href="#"><i class="fa fa-star"></i></a></li>-->
<!--</ul>-->
<!--<div class="wow fadeIn" data-wow-duration="1s">-->
<!--<p>-->
<!--As conscious traveling Paup ers we must always be oncerned about our dear Mother Earth. If you think about it,-->
<!--you travel-->
<!--across her face <br> and She is the host to your journey.-->
<!--</p>-->
<!--</div>-->
<!--</div>-->
<!--</div>-->
<!--<div class="item">-->
<!--<div class="testi-item">-->
<!--<img src="img/quote.png" alt="">-->
<!--<h4>Fanny Spencer</h4>-->
<!--<ul class="list">-->
<!--<li><a href="#"><i class="fa fa-star"></i></a></li>-->
<!--<li><a href="#"><i class="fa fa-star"></i></a></li>-->
<!--<li><a href="#"><i class="fa fa-star"></i></a></li>-->
<!--<li><a href="#"><i class="fa fa-star"></i></a></li>-->
<!--<li><a href="#"><i class="fa fa-star"></i></a></li>-->
<!--</ul>-->
<!--<div class="wow fadeIn" data-wow-duration="1s">-->
<!--<p>-->
<!--As conscious traveling Paup ers we must always be oncerned about our dear Mother Earth. If you think about it,-->
<!--you travel-->
<!--across her face <br> and She is the host to your journey.-->
<!--</p>-->
<!--</div>-->
<!--</div>-->
<!--</div>-->
<!--<div class="item">-->
<!--<div class="testi-item">-->
<!--<img src="img/quote.png" alt="">-->
<!--<h4>Fanny Spencer</h4>-->
<!--<ul class="list">-->
<!--<li><a href="#"><i class="fa fa-star"></i></a></li>-->
<!--<li><a href="#"><i class="fa fa-star"></i></a></li>-->
<!--<li><a href="#"><i class="fa fa-star"></i></a></li>-->
<!--<li><a href="#"><i class="fa fa-star"></i></a></li>-->
<!--<li><a href="#"><i class="fa fa-star"></i></a></li>-->
<!--</ul>-->
<!--<div class="wow fadeIn" data-wow-duration="1s">-->
<!--<p>-->
<!--As conscious traveling Paup ers we must always be oncerned about our dear Mother Earth. If you think about it,-->
<!--you travel-->
<!--across her face <br> and She is the host to your journey.-->
<!--</p>-->
<!--</div>-->
<!--</div>-->
<!--</div>-->
<!--</div>-->
<!--<div class="owl-thumbs d-flex justify-content-center" data-slider-id="1">-->
<!--<div class="owl-thumb-item">-->
<!--<div>-->
<!--<img class="img-fluid" src="img/testimonial/t1.jpg" alt="">-->
<!--</div>-->
<!--<div class="overlay overlay-grad"></div>-->
<!--</div>-->
<!--<div class="owl-thumb-item">-->
<!--<div>-->
<!--<img class="img-fluid" src="img/testimonial/t2.jpg" alt="">-->
<!--</div>-->
<!--<div class="overlay overlay-grad"></div>-->
<!--</div>-->
<!--<div class="owl-thumb-item">-->
<!--<div>-->
<!--<img class="img-fluid" src="img/testimonial/t3.jpg" alt="">-->
<!--</div>-->
<!--<div class="overlay overlay-grad"></div>-->
<!--</div>-->
<!--<div class="owl-thumb-item">-->
<!--<div>-->
<!--<img class="img-fluid" src="img/testimonial/t4.jpg" alt="">-->
<!--</div>-->
<!--<div class="overlay overlay-grad"></div>-->
<!--</div>-->
<!--</div>-->
<!--</div>-->
<!--</section>-->
<!-- End Testimonials Area -->

<!-- Start Screenshot Area -->
<!--
<section class="screenshot-area section-gap-top" style="padding: 0">
	<div class="container">
		<div class="row d-flex justify-content-center">
			<div class="col-lg-6">
				<div class="section-title text-center">
					<h2>Featured Screens</h2>
					<p>941 app is a digital platform for booking and paying for your reservations. Choose your options: either the preset available reservations or select the booking service.
					</p>
				</div>
			</div>
		</div>
		<div class="row">
			<div class="owl-carousel owl-screenshot">
				<div class="single-screenshot">
					<img class="img-fluid" src="{{env('APP_ENV') == 'local' ? asset('public/app_landing/img/screenshots/1.png') : secure_asset('public/app_landing/img/screenshots/1.png')}}" alt="">
				</div>
				<div class="single-screenshot">
					<img class="img-fluid" src="{{env('APP_ENV') == 'local' ? asset('public/app_landing/img/screenshots/2.png') : secure_asset('public/app_landing/img/screenshots/2.png')}}" alt="">
				</div>
			</div>
		</div>
	</div>
</section>
-->
<!-- End Screenshot Area -->

<!-- Start Pricing Area -->
<!--<section class="pricing-area">-->
<!--<div class="container">-->
<!--<div class="row d-flex justify-content-center">-->
<!--<div class="col-lg-6">-->
<!--<div class="section-title text-center">-->
<!--<h2>Suitable Pricing Plans</h2>-->
<!--<p>Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod tempor incididunt ut labore et dolore-->
<!--magna aliqua.-->
<!--</p>-->
<!--</div>-->
<!--</div>-->
<!--</div>-->

<!--<div class="row">-->
<!--<div class="col-lg-4 col-md-6">-->
<!--<div class="single-price">-->
<!--<div class="top-sec d-flex justify-content-between">-->
<!--<div class="top-left">-->
<!--<h4>Standard</h4>-->
<!--<p>For the <br>individuals</p>-->
<!--</div>-->
<!--<div class="top-right">-->
<!--<h1>£199</h1>-->
<!--</div>-->
<!--</div>-->
<!--<div class="end-sec">-->
<!--<ul>-->
<!--<li>2.5 GB Free Photos</li>-->
<!--<li>Secure Online Transfer Indeed</li>-->
<!--<li>Unlimited Styles for interface</li>-->
<!--<li>Reliable Customer Service</li>-->
<!--<li>Manual Backup Provided</li>-->
<!--</ul>-->
<!--<button class="primary-btn price-btn mt-20">Purchase Plan</button>-->
<!--</div>-->
<!--</div>-->
<!--</div>-->
<!--<div class="col-lg-4 col-md-6">-->
<!--<div class="single-price">-->
<!--<div class="top-sec d-flex justify-content-between">-->
<!--<div class="top-left">-->
<!--<h4>Business</h4>-->
<!--<p>For <br>small Company</p>-->
<!--</div>-->
<!--<div class="top-right">-->
<!--<h1>£399</h1>-->
<!--</div>-->
<!--</div>-->
<!--<div class="end-sec">-->
<!--<ul>-->
<!--<li>2.5 GB Free Photos</li>-->
<!--<li>Secure Online Transfer Indeed</li>-->
<!--<li>Unlimited Styles for interface</li>-->
<!--<li>Reliable Customer Service</li>-->
<!--<li>Manual Backup Provided</li>-->
<!--</ul>-->
<!--<button class="primary-btn price-btn mt-20">Purchase Plan</button>-->
<!--</div>-->
<!--</div>-->
<!--</div>-->
<!--<div class="col-lg-4 col-md-6">-->
<!--<div class="single-price">-->
<!--<div class="top-sec d-flex justify-content-between">-->
<!--<div class="top-left">-->
<!--<h4>Ultimate</h4>-->
<!--<p>For <br>large Company</p>-->
<!--</div>-->
<!--<div class="top-right">-->
<!--<h1>£499</h1>-->
<!--</div>-->
<!--</div>-->
<!--<div class="end-sec">-->
<!--<ul>-->
<!--<li>2.5 GB Free Photos</li>-->
<!--<li>Secure Online Transfer Indeed</li>-->
<!--<li>Unlimited Styles for interface</li>-->
<!--<li>Reliable Customer Service</li>-->
<!--<li>Manual Backup Provided</li>-->
<!--</ul>-->
<!--<button class="primary-btn price-btn mt-20">Purchase Plan</button>-->
<!--</div>-->
<!--</div>-->
<!--</div>-->

<!--</div>-->
<!--</div>-->
<!--</section>-->
<!-- End Pricing Area -->

<!-- Start Footer Area -->
<footer class="footer-area section-gap">
	<div class="container">
		<div class="row">
			<div class="col-md-12 single-footer-widget">

				<ul style="display: table; margin: 0 auto;">
					<li><a href="{{secure_url('api/website_terms')}}"><h4>Terms of Service</h4></a></li>
                    {{--<li><a href="{{secure_url('api/website_privacy')}}"><h4>Privacy</h4></a></li>--}}
                    {{--<li><a href="{{secure_url('api/website_about_us')}}"><h4>About US</h4></a></li>--}}
					<li><a href="mailto:digitalmedia@bankofbeirut.com.lb"><h4>Contact Us</h4></a> </li>
				</ul>
			</div>


			<!--
			<div class="col-md-4 single-footer-widget">
			<h4>Newsletter</h4>
				<p>You can trust us. we only send promo offers,</p>
				<div class="form-wrap" id="mc_embed_signup">
				<form target="_blank" action="https://spondonit.us12.list-manage.com/subscribe/post?u=1462626880ade1ac87bd9c93a&amp;id=92a4423d01" method="get" class="form-inline">
					<input class="form-control" name="EMAIL" placeholder="Your Email Address" onfocus="this.placeholder = ''" onblur="this.placeholder = 'Your Email Address '" required="" type="email">
				<button class="click-btn btn btn-default"><span class="lnr lnr-arrow-right"></span></button>
				<div style="position: absolute; left: -5000px;">
				<input name="b_36c4fd991d266f23781ded980_aefe40901a" tabindex="-1" value="" type="text">
				</div>

				<div class="info"></div>
				</form>
				</div>
			</div>
			-->
			<!--
			<div class="col-md-4 single-footer-widget">
				<h4>Contact Us</h4>
				<div class="form-wrap" id="mc_embed_signup">
					<form target="_blank" action="https://spondonit.us12.list-manage.com/subscribe/post?u=1462626880ade1ac87bd9c93a&amp;id=92a4423d01" method="get" class="form-inline">
						<input class="form-control" name="EMAIL" placeholder="Your Email Address" required="" type="email">

						<textarea name="text" class="form-control" placeholder="Your Message">

						</textarea>
					</form>
				</div>
			</div>
			-->
		</div>
		<div class="footer-bottom row align-items-center">
			<p class="footer-text m-0 col-lg-6 col-md-12"><!-- Link back to Colorlib can't be removed. Template is licensed under CC BY 3.0. -->
				Copyright &copy;<script>document.write(new Date().getFullYear());</script> All rights reserved <br> Developed by <a>TEDMOB</a>
				<!-- Link back to Colorlib can't be removed. Template is licensed under CC BY 3.0. --></p>
			<div class="col-lg-6 col-md-6 social-link">
				{{--<div class="download-button d-flex flex-row justify-content-end">--}}
					{{--<img src="{{env('APP_ENV') == 'local' ? asset('public/app_landing/img/visa-mastercard-logo.jpg') : secure_asset('public/app_landing/img/visa-mastercard-logo.jpg')}}" style="width: 20%;margin: auto" alt="" title="" />--}}
				{{--</div>--}}
			</div>
		</div>
	</div>
</footer>
<!-- End Footer Area -->

<script src="{{env('APP_ENV') == 'local' ? asset('public/app_landing/js/vendor/jquery-2.2.4.min.js') : secure_asset('public/app_landing/js/vendor/jquery-2.2.4.min.js')}}"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.12.9/umd/popper.min.js" integrity="sha384-ApNbgh9B+Y1QKtv3Rn7W3mgPxhU9K/ScQsAP7hUibX39j7fakFPskvXusvfa0b4Q"
		crossorigin="anonymous"></script>
<script src="{{env('APP_ENV') == 'local' ? asset('public/app_landing/js/tilt.jquery.min.js') : secure_asset('public/app_landing/js/tilt.jquery.min.js')}}"></script>
<script src="{{env('APP_ENV') == 'local' ? asset('public/app_landing/js/vendor/bootstrap.min.js') : secure_asset('public/app_landing/js/vendor/bootstrap.min.js')}}"></script>
<script type="text/javascript" src="https://maps.googleapis.com/maps/api/js?key=AIzaSyBhOdIF3Y9382fqJYt5I_sswSrEw5eihAA"></script>
<script src="{{env('APP_ENV') == 'local' ? asset('public/app_landing/js/easing.min.js') : secure_asset('public/app_landing/js/easing.min.js')}}"></script>
<script src="{{env('APP_ENV') == 'local' ? asset('public/app_landing/js/hoverIntent.js') : secure_asset('public/app_landing/js/hoverIntent.js')}}"></script>
<script src="{{env('APP_ENV') == 'local' ? asset('public/app_landing/js/superfish.min.js') : secure_asset('public/app_landing/js/superfish.min.js')}}"></script>
<script src="{{env('APP_ENV') == 'local' ? asset('public/app_landing/js/jquery.ajaxchimp.min.js') : secure_asset('public/app_landing/js/jquery.ajaxchimp.min.js')}}"></script>
<script src="{{env('APP_ENV') == 'local' ? asset('public/app_landing/js/jquery.magnific-popup.min.js') : secure_asset('public/app_landing/js/jquery.magnific-popup.min.js')}}"></script>
<script src="{{env('APP_ENV') == 'local' ? asset('public/app_landing/js/owl.carousel.min.js') : secure_asset('public/app_landing/js/owl.carousel.min.js')}}"></script>
<script src="{{env('APP_ENV') == 'local' ? asset('public/app_landing/js/owl-carousel-thumb.min.js') : secure_asset('public/app_landing/js/owl-carousel-thumb.min.js')}}"></script>
<script src="{{env('APP_ENV') == 'local' ? asset('public/app_landing/js/hexagons.min.js') : secure_asset('public/app_landing/js/hexagons.min.js')}}"></script>
<script src="{{env('APP_ENV') == 'local' ? asset('public/app_landing/js/jquery.nice-select.min.js') : secure_asset('public/app_landing/js/jquery.nice-select.min.js')}}"></script>
<script src="{{env('APP_ENV') == 'local' ? asset('public/app_landing/js/waypoints.min.js') : secure_asset('public/app_landing/js/waypoints.min.js')}}"></script>
<script src="{{env('APP_ENV') == 'local' ? asset('public/app_landing/js/mail-script.js') : secure_asset('public/app_landing/js/mail-script.js')}}"></script>
<script src="{{env('APP_ENV') == 'local' ? asset('public/app_landing/js/main.js') : secure_asset('public/app_landing/js/main.js')}}"></script>
</body>

</html>