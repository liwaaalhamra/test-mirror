//Disable Image Draging
window.ondragstart = function() { return false; }
//Disable Right Click On Images
$("img").bind('contextmenu', function(e) { return false; });

$(window).on("load", function () {

	$(".bar").each(function(){
		var percent = $(this).data("percentage");
		$(this).find("span").css("width", percent + "%");
	});

});

$(window).scroll(function () {
	var scroll = $(window).scrollTop();
	if (scroll > 0) {
		$(".top-bar").addClass("onScroll");
	} else {
		$(".top-bar").removeClass("onScroll");
	}
});

$(document).ready(function(){

	// Home Header Slick and Animation
	$('.slick-animation').on('beforeChange', function(e, slick, currentSlide, nextSlide) {
		var $animatingElements = $('div.custom-slider[data-slick-index="' + nextSlide + '"]').find('[data-animation]');
		doAnimations($animatingElements);
	});
	// Custom Slick
	$('.custom-slick').slick({
		arrows: false,
		dots: true,
		infinite: false,
		autoplay: true,
		autoplaySpeed: 3000,
		pauseOnHover: false
	});

	// Success Story Slick
	$('.success-story-slick').slick({
		arrows: false,
		dots: true,
		autoplay: false,
		autoplaySpeed: 5000,
		pauseOnHover: false,
		infinite: false,
		slidesToShow: 2.5,
		slidesToScroll: 1,
		responsive: [
		{
			breakpoint: 991,
			settings: {
				slidesToShow: 2,
				slidesToScroll: 2
			}
		},
		{
			breakpoint: 767,
			settings: {
				slidesToShow: 2,
				slidesToScroll: 2
			}
		},{
			breakpoint: 576,
			settings: {
				slidesToShow: 1,
				slidesToScroll: 1
			}
		}
		]
	});

	// Our History Slick
	$('.our-history-slick').slick({
		arrows: true,
		dots: false,
		autoplay: false,
		infinite: false,
		slidesToShow: 4,
		slidesToScroll: 1,
		vertical: true,
		verticalSwiping: false,
		swipe: false
	});

	$(".faqs-list li").click(function(){
		$(this).find(".content").slideToggle();
	});

	// Custom Select Box
	$('.custom-select-wrapper input[readonly]').on('click', function(){
		$(this).closest('.custom-select-wrapper').find('ul').toggleClass('d-block');
	});
	$('.custom-select-wrapper ul li').on('click', function(){
		var $this = $(this);
		$this.closest('.custom-select-wrapper').find('input[type="hidden"]').val($this.data('value'));
		$this.closest('.custom-select-wrapper').find('input[readonly]').val($this.text());
		$this.closest('.custom-select-wrapper').find('ul').removeClass('d-block');
	});
	$(document).bind('click',function(e) {
		if ($(e.target).closest('.custom-select-wrapper').length) return;
		$('.custom-select-wrapper ul').removeClass('d-block');
	});

	// Custome Type File
	$("input[type='file']").change(function(){
		var filePath = $(this).val().replace("C:\\fakepath\\", "");
		$(this).next().text(filePath);
	});

});

// Home Header Slick and Animation
function doAnimations(elements) {
	var animationEndEvents = 'webkitAnimationEnd mozAnimationEnd MSAnimationEnd oanimationend animationend';
	elements.each(function() {
		var $this = $(this);
		var $animationDelay = $this.data('delay');
		var $animationType = 'animated ' + $this.data('animation');
		$this.css({
			'animation-delay': $animationDelay,
			'-webkit-animation-delay': $animationDelay
		});
		$this.addClass($animationType).one(animationEndEvents, function() {
			$this.removeClass($animationType);
		});
	});
}
