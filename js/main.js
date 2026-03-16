/***************************************************
==================== JS INDEX ======================
****************************************************
01. PreLoader Js


****************************************************/

(function ($) {
	"use strict";

	var windowOn = $(window);
	////////////////////////////////////////////////////
	// 01. PreLoader Js
	windowOn.on('load', function () {
		$("#loading").fadeOut(500);
	});

	/////// add tag //////
	$('.tp-main-menu ul li .submenu li a').each(function(){
		$(this).wrapInner("<span></span>");
	  });
	

	////////////////////////////////////////////////////
	// 02. Mobile Menu Js
	$('#mobile-menu').meanmenu({
		meanMenuContainer: '.mobile-menu',
		meanScreenWidth: "991",
		meanExpand: ['<i class="fal fa-plus"></i>'],
	});

	// last child menu
	$('.wp-menu nav > ul > li').slice(-4).addClass('menu-last');


	////////////////////////////////////////////////////
	// 03. Offcanvas Js
	$(".offcanvas-open-btn").on("click", function () {
		$(".offcanvas__area").addClass("offcanvas-opened");
		$(".body-overlay").addClass("opened");
	});
	$(".offcanvas-close-btn").on("click", function () {
		$(".offcanvas__area").removeClass("offcanvas-opened");
		$(".body-overlay").removeClass("opened");
	});


	//mobile menu/////
	$('#tp-mobile-menu').meanmenu({
		meanMenuContainer: '.tp-mobile-menu-pos',
		meanScreenWidth: "1199",
	});

	
	// add class
	$(".tp-toogle").on('click', function () {
		$(".tp-side-info").addClass("tp-side-info-open");
		$(".offcanvas-overlay").addClass("offcanvas-overlay-open");
	});

	$(".tp-side-close").on('click', function () {
		$(".tp-side-info").removeClass("tp-side-info-open");
		$(".offcanvas-overlay").removeClass("offcanvas-overlay-open");
	});

	$(".offcanvas-overlay").on('click', function () {
		$(".tp-side-info").removeClass("tp-side-info-open");
		$(".offcanvas-overlay").removeClass("offcanvas-overlay-open");
	});
	

	////////////////////////////////////////////////////
	// 04. Body overlay Js
	$(".body-overlay").on("click", function () {
		$(".offcanvas__area").removeClass("offcanvas-opened");
		$(".cartmini__area").removeClass("cartmini-opened");
		$(".body-overlay").removeClass("opened");
	});


	$(".cartmini-open-btn").on("click", function () {
		$(".cartmini__area").addClass("cartmini-opened");
		$(".body-overlay").addClass("opened");
	});


	$(".cartmini-close-btn").on("click", function () {
		$(".cartmini__area").removeClass("cartmini-opened");
		$(".body-overlay").removeClass("opened");
	});



	////////////////////////////////////////////////////
	// 06. Sticky Header Js
	windowOn.on('scroll', function () {
		var scroll = $(window).scrollTop();
		if (scroll < 200) {
			$("#header-sticky").removeClass("tp-header-sticky");
		} else {
			$("#header-sticky").addClass("tp-header-sticky");
		}
	});

	if ($('.tp-header-height').length > 0) {
		var headerHeight = document.querySelector(".tp-header-height");
			  
		var setHeaderHeight = headerHeight.offsetHeight;
			  
		$(".tp-header-height").each(function () {
			$(this).css({
				'height' : setHeaderHeight + 'px'
			});
		});
	}


	////////////////////////////////////////////////////
	// 07. Data CSS Js
	$("[data-background").each(function () {
		$(this).css("background-image", "url( " + $(this).attr("data-background") + "  )");
	});

	$("[data-width]").each(function () {
		$(this).css("width", $(this).attr("data-width"));
	});

	$("[data-height]").each(function () {
		$(this).css("height", $(this).attr("data-height"));
	});

	$("[data-bg-color]").each(function () {
		$(this).css("background-color", $(this).attr("data-bg-color"));
	});



	// settings append in body
	function tp_settings_append($x){
		var settings = $('body');
		let dark;
		$x == true ? dark = 'd-block' : dark = 'd-none';
		var settings_html = `<div class="tp-theme-settings-area transition-3">
		<div class="tp-theme-wrapper">
		   <div class="tp-theme-header text-center">
			  <h4 class="tp-theme-header-title">Harry Theme Settings</h4>
		   </div>

		   <!-- THEME TOGGLER -->
		   <div class="tp-theme-toggle mb-20 ${dark}">
			  <label class="tp-theme-toggle-main" for="tp-theme-toggler">
				 <span class="tp-theme-toggle-dark"><i class="fa-light fa-moon"></i> Dark</span>
					<input type="checkbox" id="tp-theme-toggler">
					<i class="tp-theme-toggle-slide"></i>
				 <span class="tp-theme-toggle-light active"><i class="fa-light fa-sun-bright"></i> Light</span>
			  </label>
		   </div>

		   <!--  RTL SETTINGS -->
		   <div class="tp-theme-dir mb-20">
			  <label class="tp-theme-dir-main" for="tp-dir-toggler">
				 <span class="tp-theme-dir-rtl"> RTL</span>
					<input type="checkbox" id="tp-dir-toggler">
					<i class="tp-theme-dir-slide"></i>
				 <span class="tp-theme-dir-ltr active"> LTR</span>
			  </label>
		   </div>

		   <!-- COLOR SETTINGS -->
		   <div class="tp-theme-settings">
			  <div class="tp-theme-settings-wrapper">
				 <div class="tp-theme-settings-open">
					<button class="tp-theme-settings-open-btn">
					   <span class="tp-theme-settings-gear">
						  <i class="fa-light fa-gear"></i>
					   </span>
					   <span class="tp-theme-settings-close">
						  <i class="fa-regular fa-xmark"></i>
					   </span>
					</button>
				 </div>
				 <div class="row row-cols-4 gy-2 gx-2">
					<div class="col">
					   <div class="tp-theme-color-item tp-color-active">
						  <button class="tp-theme-color-btn tp-color-settings-btn d-none" data-color-default="#F50963" type="button" data-color="#F50963"></button>
						  <button class="tp-theme-color-btn tp-color-settings-btn" type="button" data-color="#F50963"></button>
					   </div>
					</div>
					<div class="col">
					   <div class="tp-theme-color-item tp-color-active">
						  <button class="tp-theme-color-btn tp-color-settings-btn" type="button" data-color="#008080"></button>
					   </div>
					</div>
					<div class="col">
					   <div class="tp-theme-color-item tp-color-active">
						  <button class="tp-theme-color-btn tp-color-settings-btn" type="button" data-color="#AB6C56"></button>
					   </div>
					</div>
					<div class="col">
					   <div class="tp-theme-color-item tp-color-active">
						  <button class="tp-theme-color-btn tp-color-settings-btn" type="button" data-color="#3661FC"></button>
					   </div>
					</div>
					<div class="col">
					   <div class="tp-theme-color-item tp-color-active">
						  <button class="tp-theme-color-btn tp-color-settings-btn" type="button" data-color="#2CAE76"></button>
					   </div>
					</div>
					<div class="col">
					   <div class="tp-theme-color-item tp-color-active">
						  <button class="tp-theme-color-btn tp-color-settings-btn" type="button" data-color="#FF5A1B"></button>
					   </div>
					</div>
					<div class="col">
                        <div class="tp-theme-color-item tp-color-active">
                           <button class="tp-theme-color-btn tp-color-settings-btn" type="button" data-color="#03041C"></button>
                        </div>
                     </div>
					<div class="col">
					   <div class="tp-theme-color-item tp-color-active">
						  <button class="tp-theme-color-btn tp-color-settings-btn" type="button" data-color="#ED212C"></button>
					   </div>
					</div>
				 </div>
			  </div>
			  <div class="tp-theme-color-input">
				 <h6>Choose Custom Color</h6>
				 <input type="color" id="tp-color-setings-input" value="#F50963">
				 <label id="tp-theme-color-label" for="tp-color-setings-input"></label>
			  </div>
		   </div>
		</div>
	 </div>`;
	 settings.append(settings_html);
	}
	// tp_settings_append(true); // if want to enable dark light mode then send "true";

	// settings open btn
	$(".tp-theme-settings-open-btn").on("click", function () {
		$(".tp-theme-settings-area").toggleClass("settings-opened");
	});

	// rtl settings
	function tp_rtl_settings() {

		$('#tp-dir-toggler').on("change", function () {
			toggle_rtl();

		});


		// set toggle theme scheme
		function tp_set_scheme(tp_dir) {
			localStorage.setItem('tp_dir', tp_dir);
			document.documentElement.setAttribute("dir", tp_dir);

			if (tp_dir === 'rtl') {
				var list = $("[href='assets/css/bootstrap.css']");
				$(list).attr("href", "assets/css/bootstrap-rtl.css");
			} else {
				var list = $("[href='assets/css/bootstrap.css']");
				$(list).attr("href", "assets/css/bootstrap.css");
			}
		}

		// toogle theme scheme
		function toggle_rtl() {
			if (localStorage.getItem('tp_dir') === 'rtl') {
				tp_set_scheme('ltr');
				var list = $("[href='assets/css/bootstrap-rtl.css']");
				$(list).attr("href", "assets/css/bootstrap.css");
			} else {
				tp_set_scheme('rtl');
				var list = $("[href='assets/css/bootstrap.css']");
				$(list).attr("href", "assets/css/bootstrap-rtl.css");
			}
		}

		// set the first theme scheme
		function tp_init_dir() {
			if (localStorage.getItem('tp_dir') === 'rtl') {
				tp_set_scheme('rtl');
				var list = $("[href='assets/css/bootstrap.css']");
				$(list).attr("href", "assets/css/bootstrap-rtl.css");
				document.getElementById('tp-dir-toggler').checked = true;
			} else {
				tp_set_scheme('ltr');
				document.getElementById('tp-dir-toggler').checked = false;
				var list = $("[href='assets/css/bootstrap.css']");
				$(list).attr("href", "assets/css/bootstrap.css");
			}
		}
		tp_init_dir();
	}
	if ($("#tp-dir-toggler").length > 0) {
		tp_rtl_settings();
	}

	// dark light mode toggler
	function tp_theme_toggler() {

		$('#tp-theme-toggler').on("change", function () {
			toggleTheme();

		});


		// set toggle theme scheme
		function tp_set_scheme(tp_theme) {
			localStorage.setItem('tp_theme_scheme', tp_theme);
			document.documentElement.setAttribute("tp-theme", tp_theme);
		}

		// toogle theme scheme
		function toggleTheme() {
			if (localStorage.getItem('tp_theme_scheme') === 'tp-theme-dark') {
				tp_set_scheme('tp-theme-light');
			} else {
				tp_set_scheme('tp-theme-dark');
			}
		}

		// set the first theme scheme
		function tp_init_theme() {
			if (localStorage.getItem('tp_theme_scheme') === 'tp-theme-dark') {
				tp_set_scheme('tp-theme-dark');
				document.getElementById('tp-theme-toggler').checked = true;
			} else {
				tp_set_scheme('tp-theme-light');
				document.getElementById('tp-theme-toggler').checked = false;
			}
		}
		tp_init_theme();
	}
	if ($("#tp-theme-toggler").length > 0) {
		tp_theme_toggler();
	}


	// color settings
	function tp_color_settings() {

		// set color scheme
		function tp_set_color(tp_color_scheme) {
			localStorage.setItem('tp_color_scheme', tp_color_scheme);
			document.querySelector(':root').style.setProperty('--tp-theme-1', tp_color_scheme);
			document.getElementById("tp-color-setings-input").value = tp_color_scheme;
			document.getElementById("tp-theme-color-label").style.backgroundColor = tp_color_scheme;
		}

		// set color
		function tp_set_input() {
			var color = localStorage.getItem('tp_color_scheme');
			document.getElementById("tp-color-setings-input").value = color;
			document.getElementById("tp-theme-color-label").style.backgroundColor = color;


		}
		tp_set_input();

		function tp_init_color() {
			var defaultColor = $(".tp-color-settings-btn").attr('data-color-default');
			var setColor = localStorage.getItem('tp_color_scheme');

			if (setColor != null) {

			} else {
				setColor = defaultColor;
			}

			if (defaultColor !== setColor) {
				document.querySelector(':root').style.setProperty('--tp-theme-1', setColor);
				document.getElementById("tp-color-setings-input").value = setColor;
				document.getElementById("tp-theme-color-label").style.backgroundColor = setColor;
				tp_set_color(setColor);
			} else {
				document.querySelector(':root').style.setProperty('--tp-theme-1', defaultColor);
				document.getElementById("tp-color-setings-input").value = defaultColor;
				document.getElementById("tp-theme-color-label").style.backgroundColor = defaultColor;
				tp_set_color(defaultColor);
			}
		}
		tp_init_color();


		let themeButtons = document.querySelectorAll('.tp-color-settings-btn');

		themeButtons.forEach(color => {
			color.addEventListener('click', () => {
				let datacolor = color.getAttribute('data-color');
				document.querySelector(':root').style.setProperty('--tp-theme-1', datacolor);
				document.getElementById("tp-theme-color-label").style.backgroundColor = datacolor;
				tp_set_color(datacolor);
			});
		});



		const colorInput = document.querySelector('#tp-color-setings-input');
		const colorVariable = '--tp-theme-1';


		colorInput.addEventListener('change', function (e) {
			var clr = e.target.value;
			document.documentElement.style.setProperty(colorVariable, clr);
			tp_set_color(clr);
			tp_set_check(clr);
		});


		function tp_set_check(clr){
			const arr = Array.from(document.querySelectorAll('[data-color]'));
	
			var a = localStorage.getItem('tp_color_scheme');

			let test =  arr.map(color =>{
				let datacolor = color.getAttribute('data-color');
				
				return datacolor;
			}).filter(color => color == a);
			
			var arrLength = test.length;

			if(arrLength == 0){
				$('.tp-color-active').removeClass('active');
			}else{
				$('.tp-color-active').addClass('active');
			}
		}

		function tp_check_color(){
			var a = localStorage.getItem('tp_color_scheme');

			var list = $(`[data-color="${a}"]`);

			list.parent().addClass('active').parent().siblings().find('.tp-color-active').removeClass('active')		
		}
		tp_check_color();

		$('.tp-color-active').on('click', function () {
			$(this).addClass('active').parent().siblings().find('.tp-color-active').removeClass('active');
		});

	}
	if (($(".tp-color-settings-btn").length > 0) && ($("#tp-color-setings-input").length > 0) && ($("#tp-theme-color-label").length > 0)) {
		tp_color_settings();
	}


	////////////////////////////////////////////////////
	// 07. Nice Select Js
	$('select').niceSelect();
	$('.tp-brands-from select').niceSelect();
	$('.tp-shop-from select').niceSelect();

	////////////////////////////////////////////////////
	// 07. Smooth Scroll Js
	function smoothSctoll() {
		$('.smooth a').on('click', function (event) {
			var target = $(this.getAttribute('href'));
			if (target.length) {
				event.preventDefault();
				$('html, body').stop().animate({
					scrollTop: target.offset().top - 120
				}, 1500);
			}
		});
	}
	smoothSctoll();

	function back_to_top() {
		var btn = $('#back_to_top');
		var btn_wrapper = $('.back-to-top-wrapper');

		windowOn.scroll(function () {
			if (windowOn.scrollTop() > 300) {
				btn_wrapper.addClass('back-to-top-btn-show');
			} else {
				btn_wrapper.removeClass('back-to-top-btn-show');
			}
		});

		btn.on('click', function (e) {
			e.preventDefault();
			$('html, body').animate({ scrollTop: 0 }, '300');
		});
	}
	back_to_top();

	var tp_rtl = localStorage.getItem('tp_dir');
	let rtl_setting = tp_rtl == 'rtl' ? true : false;



	var postboxSlider = new Swiper('.postbox__slider', {
		slidesPerView: 1,
		spaceBetween: 0,
		loop: true,
		rtl: rtl_setting,
		autoplay: {
			delay: 3000,
		},
		// Navigation arrows
		navigation: {
			nextEl: ".postbox-slider-button-next",
			prevEl: ".postbox-slider-button-prev",
		},
		breakpoints: {
			'1200': {
				slidesPerView: 1,
			},
			'992': {
				slidesPerView: 1,
			},
			'768': {
				slidesPerView: 1,
			},
			'576': {
				slidesPerView: 1,
			},
			'0': {
				slidesPerView: 1,
			},
		},
	});

	//////////////////////////////////////////////////
	// 13. Masonary Js
	$('.grid').imagesLoaded(function () {
		// init Isotope
		var $grid = $('.grid').isotope({
			itemSelector: '.grid-item',
			percentPosition: true,
			masonry: {
				// use outer width of grid-sizer for columnWidth
				columnWidth: '.grid-item',
			}
		});

		// Feaure Masonary
		$('.about-masonry').imagesLoaded(function () {
		// init Isotope
		var $grid = $('.about-masonry').isotope({
			itemSelector: '.about-grid',
			percentPosition: true,
			masonry: {
				// use outer width of grid-sizer for columnWidth
				columnWidth: '.about-grid',
			}
		});


		// filter items on button click
		$('.masonary-menu').on('click', 'button', function () {
			var filterValue = $(this).attr('data-filter');
			$grid.isotope({ filter: filterValue });
		});

		//for menu active class
		$('.masonary-menu button').on('click', function (event) {
			$(this).siblings('.active').removeClass('active');
			$(this).addClass('active');
			event.preventDefault();
		});

	});

});

	/* magnificPopup img view */
	$('.popup-image').magnificPopup({
		type: 'image',
		gallery: {
			enabled: true
		}
	});

	/* magnificPopup video view */
	$(".popup-video").magnificPopup({
		type: "iframe",
	});

	////////////////////////////////////////////////////
	// 14. Wow Js
	new WOW().init();

	function tp_ecommerce() {
		$('.tp-cart-minus').on('click', function () {
			var $input = $(this).parent().find('input');
			var count = parseInt($input.val()) - 1;
			count = count < 1 ? 1 : count;
			$input.val(count);
			$input.change();
			return false;
		});
	
		$('.tp-cart-plus').on('click', function () {
			var $input = $(this).parent().find('input');
			$input.val(parseInt($input.val()) + 1);
			$input.change();
			return false;
		});



		$('.cart-minus').on('click', function () {
			var $input = $(this).parent().find('input');
			var count = parseInt($input.val()) - 1;
			count = count < 1 ? 1 : count;
			$input.val(count);
			$input.change();
			return false;
		});
	
		$('.cart-plus').on('click', function () {
			var $input = $(this).parent().find('input');
			$input.val(parseInt($input.val()) + 1);
			$input.change();
			return false;
		});
	
	
		////////////////////////////////////////////////////
		// 17. Show Login Toggle Js
		$('#showlogin').on('click', function () {
			$('#checkout-login').slideToggle(900);
		});
	
		////////////////////////////////////////////////////
		// 18. Show Coupon Toggle Js
		$('#showcoupon').on('click', function () {
			$('#checkout_coupon').slideToggle(900);
		});
	
		////////////////////////////////////////////////////
		// 19. Create An Account Toggle Js
		$('#cbox').on('click', function () {
			$('#cbox_info').slideToggle(900);
		});
	
		////////////////////////////////////////////////////
		// 20. Shipping Box Toggle Js
		$('#ship-box').on('click', function () {
			$('#ship-box-info').slideToggle(1000);
		});
	}
	tp_ecommerce();

	


	////////////////////////////////////////////////////
	// 23. InHover Active Js
	$('.hover__active').on('mouseenter', function () {
		$(this).addClass('active').parent().siblings().find('.hover__active').removeClass('active');
	});

	$('.activebsba').on("click", function () {
		$('#services-item-thumb').removeClass().addClass($(this).attr('rel'));
		$(this).addClass('active').siblings().removeClass('active');
	});

	if ($('#nft-slider').length > 0) {
		var stepsSlider = document.getElementById('nft-slider');
		var input0 = document.getElementById('input-with-keypress-0');
		var input1 = document.getElementById('input-with-keypress-1');
		var inputs = [input0, input1];

		noUiSlider.create(stepsSlider, {
			start: [0, 4],
			connect: true,
			range: {
				'min': [0],
				'max': 10
			}
		});

		stepsSlider.noUiSlider.on('update', function (values, handle) {
			inputs[handle].value = values[handle];
		});
	}

	if ($('.tp-tilt-effect').length > 0) {
		VanillaTilt.init(document.querySelector(".tp-tilt-effect"), {
			reverse: false,  // reverse the tilt direction
			max: 20,     // max tilt rotation (degrees)
			startX: 0,      // the starting tilt on the X axis, in degrees.
			startY: 0,      // the starting tilt on the Y axis, in degrees.
			perspective: 1000,   // Transform perspective, the lower the more extreme the tilt gets.
			scale: 1,      // 2 = 200%, 1.5 = 150%, etc..
			speed: 300,    // Speed of the enter/exit transition
			transition: true,   // Set a transition on enter/exit.
			axis: null,   // What axis should be disabled. Can be X or Y.
			reset: true,   // If the tilt effect has to be reset on exit.
			easing: "cubic-bezier(.03,.98,.52,.99)",    // Easing on enter/exit.
			glare: false,  // if it should have a "glare" effect
			"max-glare": 1,      // the maximum "glare" opacity (1 = 100%, 0.5 = 50%)
			"glare-prerender": false,
			"mouse-event-element": null,   // css-selector or link to HTML-element what will be listen mouse events
			gyroscope: true,   // Boolean to enable/disable device orientation detection,
			gyroscopeMinAngleX: -10,    // This is the bottom limit of the device angle on X axis, meaning that a device rotated at this angle would tilt the element as if the mouse was on the left border of the element;
			gyroscopeMaxAngleX: 10,     // This is the top limit of the device angle on X axis, meaning that a device rotated at this angle would tilt the element as if the mouse was on the right border of the element;
			gyroscopeMinAngleY: -10,    // This is the bottom limit of the device angle on Y axis, meaning that a device rotated at this angle would tilt the element as if the mouse was on the top border of the element;
			gyroscopeMaxAngleY: 10,     // This is the top limit of the device angle on Y axis, meaning that a device rotated at this angle would tilt the element as if the mouse was on the bottom border of the element;
		});
	}

 	
	// hero slider active
	if ($('.hero-active').length > 0) {
		new Splide( '.hero-active', {
			type   : 'fade',
			focus  : 'center',
			pagination: false,
			perPage: 1,
			classes: {
				prev  : 'splide__arrow--prev',
				next  : 'splide__arrow--next',
		  },
		  breakpoints: {
			991: {
				arrows: false,
			},
		  },
		}).mount();
	}


	// hero slider 2 active
	if ($('.hero-2-active').length > 0) {
		new Splide( '.hero-2-active', {
			type   : 'fade',
			focus  : 'center',
			pagination: false,
			perPage: 1,
			classes: {
				prev  : 'splide__arrow--prev',
				next  : 'splide__arrow--next',
		  },
		  breakpoints: {
			1270: {
				arrows: false,
			},
		  },
		}).mount();
	}


	// hero slider 3 active
	if ($('.hero-3-active').length > 0) {
		new Splide( '.hero-3-active', {
			type   : 'fade',
			focus  : 'center',
			pagination: false,
			perPage: 1,
			classes: {
				prev  : 'splide__arrow--prev',
				next  : 'splide__arrow--next',
		  },
		  breakpoints: {
			1270: {
				arrows: false,
			},
		  },
		}).mount();
	}


	// brand slider
	if ($('.slider-active').length > 0) {
	new Splide('.slider-active', {
		type   : 'loop',
		drag   : 'free',
		pagination: false,
		arrows: false,
		perPage: 6,
		gap:'20px',
		autoScroll: {
			speed: 1,
		},
		breakpoints: {
			992: {
				perPage: 4,
			},
			768: {
				perPage: 3,
			},
			576: {
				perPage: 3,
			},
			400: {
				perPage: 2,
			},
		  },
	  }).mount(window.splide.Extensions);
	}


	// service slider
	if ($('.service-active').length > 0) {
	new Splide('.service-active', {
		type   : 'loop',
		drag   : 'free',
		pagination: false,
		arrows: false,
		perPage: 6,
		gap:'36px',
		breakpoints: {
			1800: {
				perPage: 5,
			},
			1500: {
				perPage: 4,
			},
			1200: {
				perPage: 3,
			},
			800: {
				perPage: 2,
			},
			566: {
				perPage: 1,
			},
		  },
	  }).mount();
	}


	// service slider
	if ($('.service-active-2').length > 0) {
	new Splide('.service-active-2', {
		type   : 'loop',
		drag   : 'free',
		pagination: false,
		arrows: true,
		perMove : 1,
		perPage: 4,
		gap:'36px',
		classes: {
			prev  : 'splide__arrow--prev',
			next  : 'splide__arrow--next',
	  	},
		breakpoints: {
			1800: {
				perPage: 4,
			},
			1500: {
				perPage: 4,
			},
			1200: {
				perPage: 3,
			},
			991: {
				perPage: 2,	
			},
			575: {
				perPage: 1,
				arrows:false
			},
		  },
	  }).mount();
	}

	// portfolio slider
	if ($('.portfolio-active').length > 0) {
	var splide = new Splide('.portfolio-active', {
		type   : 'loop',
		pagination: false,
		arrows: true,
		focus: 'center',
		perPage: 1,
		gap:'36px',
		padding: '25%',
		throttle: 300,
		breakpoints: {
			991: {
				padding: '15%'
			},
			765: {
				padding: '12%',
				gap:'25px',
			},
			575: {
				arrows: false,
				padding: '0%',
			},
		  },
	  }).mount();
	  var bar    = splide.root.querySelector( '.my-slider-progress-bar' );
	  // Updates the bar width whenever the carousel moves:
	  splide.on( 'mounted move', function () {
		var end  = splide.Components.Controller.getEnd() + 1;
		var rate = Math.min( ( splide.index + 1 ) / end, 1 );
		bar.style.width = String( 100 * rate ) + '%';
	  } );
	};


	// portfolio slider
	if ($('.portfolio-active-2').length > 0) {
	var splide = new Splide('.portfolio-active-2', {
		type   : 'loop',
		pagination: true,
		arrows: true,
		focus: 'center',
		perPage: 1,
		gap:'36px',
		padding: '25%',
		throttle: 300,
		breakpoints: {
			991: {
				padding: '15%'
			},
			765: {
				padding: '12%',
				gap:'25px',
			},
			575: {
				arrows: false,
				padding: '0%',
			},
		  },
	  }).mount();
	};


	// portfolio slider 3 active
	$('.tp-portfolio-active-3').slick({
		dots: false,
		infinite: true,
		autoplay: false,
		speed: 300,
		arrows:true,
		prevArrow: '<button type="button" class="slick-prev"><i class="fa-regular fa-arrow-left-long"></i></button>',
		nextArrow: '<button type="button" class="slick-next"><i class="fa-regular fa-arrow-right-long"></i></button>',
		slidesToShow: 3,
		slidesToScroll: 1,
		responsive: [
			{
			breakpoint: 1200,
			settings: {
				slidesToShow: 2,
				slidesToScroll: 1,
			}
		},
			{
			breakpoint: 991,
			settings: {
				arrows:false,
				slidesToShow: 2,
				slidesToScroll: 1,
			}
		},
		{
			breakpoint: 768,
			settings: {
				slidesToShow: 1,
				slidesToScroll: 1,
				arrows:false
			}
		}
	]
	});
	$("#pills-tab li button").on("click", function(){
		$('.tp-portfolio-active-3').slick('refresh');
	});



	var slider = new Swiper('.portfolio-slider-active', {
		slidesPerView: 1,
		spaceBetween: 30,
		loop: true,
		rtl: rtl_setting,
		pagination: {
			el: ".testimonial-pagination-6",
			clickable: true,
			renderBullet: function (index, className) {
				return '<span class="' + className + '">' + '<button>' + (index + 1) + '</button>' + "</span>";
			},
		},
		navigation: {
			nextEl: '.swiper-button-next',
			prevEl: '.swiper-button-prev',
		  },
		observer:true,
		observeParents:true,
		breakpoints: {
			'1200': {
				slidesPerView: 3,
			},
			'992': {
				slidesPerView: 2,
			},
			'768': {
				slidesPerView: 2,
			},
			'576': {
				slidesPerView: 1,
			},
			'0': {
				slidesPerView: 1,
			},
		},
	});


	//   testimonial active
	if ($('.testimonial-main-active').length > 0) {
	  var main = new Splide( '.testimonial-main-active', {
		pagination : true,
		arrows     : false,
		type   	   : 'loop',
		breakpoints : {
			576: {
				perPage: 1
			},
		  },
	  } );
	  
	  var thumbnails = new Splide( '.testimonial-navigation-active', {
		gap        : 0,
		focus      : 'center',
		pagination : false,
		arrows     : false,
		type   : 'loop',
		perPage: 3,
		isNavigation    : true,
		breakpoints : {
		  640: {
			fixedWidth  : 66,
			fixedHeight : 38,
		  },
		  639: {
			fixedWidth  : 66,
			fixedHeight : 105,
		  },
		},
	  } );
	  
	  main.sync( thumbnails );
	  main.mount();
	  thumbnails.mount();
	}

	// testimonial slider
	if ($('.testimonial-active-2').length > 0) {
		new Splide('.testimonial-active-2', {
			type   : 'loop',
			drag   : 'free',
			pagination: false,
			arrows: true,
			perMove    : 1,
			perPage: 2,
			gap:'36px',
			classes: {
				prev  : 'splide__arrow--prev',
				next  : 'splide__arrow--next',
				},
			breakpoints: {
				800: {
					perPage: 2,
				},
				767: {
					perPage: 1,
				},
				575:{
					arrows: false,
				}
				},
			}).mount();
		}


	// testimonial slider
	if ($('.testimonial-active-3').length > 0) {
		new Splide('.testimonial-active-3', {
			type   : 'loop',
			drag   : 'free',
			pagination: false,
			arrows: true,
			perMove    : 1,
			perPage: 1,
			gap: '30px',
			classes: {
				prev  : 'splide__arrow--prev',
				next  : 'splide__arrow--next',
				},
			breakpoints: {
				800: {
					perPage: 1,
				},
				767: {
					perPage: 1,
				},
				575:{
					arrows: false,
				}
				},
			}).mount();
		}


	// blog slider
	if ($('.blog-active-2').length > 0) {
		var splide = new Splide('.blog-active-2', {
			type   : 'loop',
			pagination: false,
			arrows: false,
			perMove    : 1,
			perPage: 2,
			throttle: 300,
			gap:'36px',
			breakpoints: {
				575: {
					perPage: 1,
				},
				},
			}).mount();
			var bar  = splide.root.querySelector( '.blog-slider-progress-bar' );
			// Updates the bar width whenever the carousel moves:
			splide.on( 'mounted move', function () {
				var end  = splide.Components.Controller.getEnd() + 1;
				var rate = Math.min( ( splide.index + 1 ) / end, 1 );
				bar.style.width = String( 100 * rate ) + '%';
			} );
		}


	//   knob progress bar
	  if (typeof ($.fn.knob) != 'undefined') {
		$('.knob').each(function () {
		var $this = $(this),
		knobVal = $this.attr('data-rel');

		$this.knob({
		'draw': function () {
			$(this.i).val(this.cv + '%')
		}
		});

		$this.appear(function () {
		$({
			value: 0
		}).animate({
			value: knobVal
		}, {
			duration: 2000,
			easing: 'swing',
			step: function () {
			$this.val(Math.ceil(this.value)).trigger('change');
			}
		});
		}, {
		accX: 0,
		accY: -150
		});
	});
	}

	////////////////////////////////////////////////////
	// 21. Counter Js
	new PureCounter();
	new PureCounter({
		filesizing: true,
		selector: ".filesizecount",
		pulse: 2,
	});

	///////////////////////////////////////////////////
	//// tab line
	if ($('#marker').length > 0) {
		function tp_tab_line(){
			var marker = document.querySelector('#marker');
			var item = document.querySelectorAll('.tp-tab-menu button');
			var itemActive = document.querySelector('.tp-tab-menu .nav-link.active');

			// rtl settings
			var tp_rtl = localStorage.getItem('tp_dir');
			let rtl_setting = tp_rtl == 'rtl' ? 'right' : 'left';

			function indicator(e){
				marker.style.left = e.offsetLeft+"px";
				marker.style.width = e.offsetWidth+"px";
			}
				
		
			item.forEach(link => {
				link.addEventListener('click', (e)=>{
					indicator(e.target);
				});
			});
			
			var activeNav = $('.nav-link.active');
			var activewidth = $(activeNav).width();
			var activePadLeft = parseFloat($(activeNav).css('padding-left'));
			var activePadRight = parseFloat($(activeNav).css('padding-right'));
			var totalWidth = activewidth + activePadLeft + activePadRight;
			
			var precedingAnchorWidth = anchorWidthCounter();
		
		
			$(marker).css('display','block');
			
			$(marker).css('width', totalWidth);
		
			function anchorWidthCounter() {
				var anchorWidths = 0;
				var a;
				var aWidth;
				var aPadLeft;
				var aPadRight;
				var aTotalWidth;
				$('.tp-tab-menu button').each(function(index, elem) {
					var activeTest = $(elem).hasClass('active');
					marker.style.left = elem.offsetLeft+"px";
					if(activeTest) {
					// Break out of the each function.
					return false;
					}
			
					a = $(elem).find('button');
					aWidth = a.width();
					aPadLeft = parseFloat(a.css('padding-left'));
					aPadRight = parseFloat(a.css('padding-right'));
					aTotalWidth = aWidth + aPadLeft + aPadRight;
			
					anchorWidths = anchorWidths + aTotalWidth;
	
				});
		
				return anchorWidths;
			}
		}
		tp_tab_line();
	}

	if ($('[data-toggle="datepicker"]').length > 0) {
	$('[data-toggle="datepicker"]').datepicker();
	}



	//////////// mouse move js////////////
	function bubbleMove(n, m){
		if ((n).length > 0) {
			var mousee = document.getElementById(m); 
			
			mousee?.addEventListener('mousemove', function(e) {
			// Get dimensions of mousemove element
			var r = this.getBoundingClientRect();
			
			// Set x and y values prop values based on center of mousemove element
			this.style.setProperty('--x', e.clientX - (r.left + Math.floor(r.width / 2)))
			this.style.setProperty('--y', e.clientY - (r.top + Math.floor(r.height / 2)))
			});
			
			mousee?.addEventListener('mouseleave', function() {
			// Reset x and y prop values when no longer hovering
			this.style.setProperty('--x', 1)
			this.style.setProperty('--y', 1) 
			});
		}
	}

	if (('#mousemove').length > 0) {
		bubbleMove('mousemove', 'mousemove');
	}

	if (('#serviceBubble').length > 0) {
		bubbleMove('serviceBubble', 'serviceBubble');
	}

	if (('#teamBubble').length > 0) {
		bubbleMove('teamBubble', 'teamBubble');
	}

	if (('#testimonialBubble').length > 0) {
		bubbleMove('testimonialBubble', 'testimonialBubble');
	}

	if (('#blogBubble').length > 0) {
		bubbleMove('blogBubble', 'blogBubble');
	}

	if (('#featureItem').length > 0) {
		bubbleMove('featureItem', 'featureItem');
	}

	if (('#footerTwo').length > 0) {
		bubbleMove('footerTwo', 'footerTwo');
	}

	
	/////// bubble effects////////
	var $bubbles = $('.bubbles');

	function bubbles() {
	  // Settings
	  var min_bubble_count = 20, // Minimum number of bubbles
		  max_bubble_count = 60, // Maximum number of bubbles
		  min_bubble_size = 3, // Smallest possible bubble diameter (px)
		  max_bubble_size = 12; // Maximum bubble blur amount (px)
	  
	  // Calculate a random number of bubbles based on our min/max
	  var bubbleCount = min_bubble_count + Math.floor(Math.random() * (max_bubble_count + 1));
	  
	  // Create the bubbles
	  for (var i = 0; i < bubbleCount; i++) {
		$bubbles.append('<div class="bubble-container"><div class="bubble"></div></div>');
	  }
	  
	  // Now randomise the various bubble elements
	  $bubbles.find('.bubble-container').each(function(){
		
		// Randomise the bubble positions (0 - 100%)
		var pos_rand = Math.floor(Math.random() * 101);
		
		// Randomise their size
		var size_rand = min_bubble_size + Math.floor(Math.random() * (max_bubble_size + 15));
		
		// Randomise the time they start rising (0-15s)
		var delay_rand = Math.floor(Math.random() * 16);
		
		// Randomise their speed (3-8s)
		var speed_rand = 3 + Math.floor(Math.random() * 9);
		
		// Random blur
		var blur_rand = Math.floor(Math.random() * 3);
		
		// Cache the this selector
		var $this = $(this);
		
		// Apply the new styles
		$this.css({
		  'left' : pos_rand + '%',
		  
		  '-webkit-animation-duration' : speed_rand + 's',
		  '-moz-animation-duration' : speed_rand + 's',
		  '-ms-animation-duration' : speed_rand + 's',
		  'animation-duration' : speed_rand + 's',
		  
		  '-webkit-animation-delay' : delay_rand + 's',
		  '-moz-animation-delay' : delay_rand + 's',
		  '-ms-animation-delay' : delay_rand + 's',
		  'animation-delay' : delay_rand + 's',
		  
		  '-webkit-filter' : 'blur(' + blur_rand  + 'px)',
		  '-moz-filter' : 'blur(' + blur_rand  + 'px)',
		  '-ms-filter' : 'blur(' + blur_rand  + 'px)',
		  'filter' : 'blur(' + blur_rand  + 'px)',
		});
		
		$this.children('.bubble').css({
		  'width' : size_rand + 'px',
		  'height' : size_rand + 'px'
		});
		
	  });
	}
	bubbles();
	
})(jQuery);