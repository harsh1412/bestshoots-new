$(document).ready(function(){

	
	jPagesFun(); // навигация
	windowSize(); // при загрузке
	$(window).resize(windowSize); // при изменении размеров
	
	//сбрасываем текст в текстовых полей
	$('.input__field').each(function(){
		$(this).val('');
	});
	
	var $js_file, //кнопка загрузки картинки
		$d, //$js_file.data()
		$width_img, //ширина текущей картинкии
		$height_img,
		$ratio, //сколько процентов имеет блок CSS  по отношению к текущей картинке
		$photo_url, //URL текущей картинкии
		_URL = window.URL || window.webkitURL; //нужно чтобы узнать размер картинки
		
	$("#photoupload").change(function (e) {
		var file, img;
		if ((file = this.files[0])) {
			img = new Image();
			img.onload = function () {
				$width_img = this.width;
				$height_img = this.height;
			};
			img.src = _URL.createObjectURL(file);
		}
	});
	
	$('.js-file').on('click', function(){
		$js_file = $(this);
		$d = $js_file.data();
        $('#photoupload').click();
    });

    $('#upload').fileupload({
        add: function (e, data) {
			data.formData = {min_height: $d.minHeight, min_width: $d.minWidth, max_size: $d.maxSize, dir: $d.dir};
			// Automatically upload the file once it is added to the queue
			var jqXHR = data.submit();
		},
		success: function (result, textStatus, jqXHR) {
			
			var $result = $.parseJSON(result);
			
			switch ($result.code) {
				case "3" : 
					popupInfo('Maximum file size: '+ $d.maxSize +' MB', true); 
					break;
				case "2" : 
					popupInfo('This file type is not supported (allowed to JPG, PNG or GIF)', true);
					break;
				case "1" : 
					popupInfo('Minimum image size: '+ $d.minWidth +' x '+ $d.minHeight, true);
					break;
				case "4" : 
					$('#prize-send').data('imgSrc', $result.target);
					$('#js-add-prize').data('imgSrc', $result.target);
					popupInfo('Image added');
					break;
				default :
					good($result.target);
			}
		}
    });
	
	function good(result) {
		var $css_width = 670, //ширина блока CSS для картинки
			$crop_w, //ширина минимальной области выделения
			$crop_h;	
		
		$photo_url = 'img/temp/' + result;
		//RESET JCROP
		$('#photo').replaceWith('<img id="photo" src="' + $photo_url + '"/>');
		$('.jcrop-holder').replaceWith('');		
		$('#js-crop-modal').show().removeClass('bounceOutDown').addClass('bounceInUp');
		$('#page').addClass('form-open');

		if ($d.dir == 'contests/users_photo') {
			$('#js-photo-modal').hide().removeClass('bounceInUp').addClass('bounceOutDown');
		}
		
		if ($width_img < 670) {
			$css_width = $width_img;
		}
		//сколько процентов имеет блок CSS  по отношению к текущей картинке
		$ratio = $css_width * 100 / $width_img;
		$crop_w = $d.minWidth / 100 * $ratio;
		$crop_h = $d.minHeight / 100 * $ratio;
		
		$('#photo').Jcrop({
				onChange: updatePreview,
				onSelect: updatePreview,
				sideHandles: false,
				setSelect:   [ 0, 0, 0, 0],
				aspectRatio: $d.minWidth / $d.minHeight,
				minSize: [$crop_w, $crop_h]
			});
	}
	
	function updatePreview(c) {
		$('#x').val(c.x);
		$('#y').val(c.y);
		$('#w').val(c.w);
		$('#h').val(c.h);
	}
	
	$('#upload-send').on('click', function(){
		var $modal = $('#js-crop-modal'),
			$rndval = new Date().getTime();
		
		$.post("/avatar_ajax.php?rndval="+ $rndval,
			{
				x: $('#x').val(),
				y: $('#y').val(),
				w: $('#w').val(),
				h: $('#h').val(),
				final_width: $d.minWidth, // финальная ширина изображения
				final_height: $d.minHeight, // финальная высота изображения
				ratio: $ratio, //сколько процентов имеет блок CSS  по отношению к текущей картинке
				dir: $d.dir,
				target: $photo_url,
				contest_id: $d.contestId,
				contest_title: $d.contestTitle,
				contest_logo: $d.contestLogo,
				act: 'send'
			},
			function (imgSrc) {
				$js_file.data('imgSrc', imgSrc);
				
				$modal.removeClass("bounceInUp").addClass("bounceOutDown");
				setTimeout(function() {
					if ($d.dir != 'companies/releases') {
						$('#page').removeClass('form-open');
					}
					$modal.hide();
				}, 700);
				
				if ($d.dir == 'contests/users_photo') {
					popupInfo('Now you take part in the contest');
				} else if ($d.dir == 'companies/header_photo') {
					$('#one').css('background-image', 'url("/img/companies/header_photo/'+ imgSrc +'")');
				} else if ($d.dir == 'users' || $d.dir == 'companies/logo') {
					var $profile_logo = $('#js-company-logo');
					$profile_logo.children('.up').addClass('hidden');
					
					if ($profile_logo.children('img').length) {
						$profile_logo.children('img').attr('src', '/img/'+ $d.dir +'/'+ imgSrc);
					} else {
						$profile_logo.prepend('<img src="/img/'+ $d.dir +'/'+ imgSrc +'">');
					}
				} else if ($d.dir == 'companies/releases') {
					$('#releases-send').data('imgSrc', imgSrc);
					popupInfo('Image added');
				} else {
					popupInfo('Image added');
				}
		});
		
		return false;	  
	});

	
	if ($('#slider').length) {
		var $numberReviews = 4,
			$widthReview = 670,
			$totalWidth = $numberReviews * $widthReview,
			$swSlider = $('.swSlider'),
			$title_cnt = '',
			$about_cnt = '',
			$new_contest = true; //конкурс в базе данных, если true еще нет
			
	
		$swSlider.width($totalWidth);
		
		$('#js-arrow').on('click', function(){
			var $this = $(this),
				$value = parseInt($this.data('value')),
				$height = $swSlider.children('.swPage').eq($value).height(),
				$header_photo = $('#js-header-photo').data('imgSrc'),
				$logo = $('#js-logo').data('imgSrc'),
				$rndval = new Date().getTime();
			
			$title_cnt = $('#js-title').val();
			$about_cnt = $('#js-about').val();
			
			if ($value == $numberReviews) {
				popupInfo('Contest created');
	
				setTimeout(function() {
					window.location.href = "/inner_page.php?id="+ $('#prize-send').data('contestId');
				}, 2000);
	
				return false;
			} else {
				
				if ($value == 1 && $new_contest) {
					
					if ($title_cnt == '' || $about_cnt == '') {
						popupInfo('All fields must be filled', true);
						return false;
					}
					
					$about_cnt = nl2ptab($about_cnt); //вставляем параграфы
					
					/*
					if (typeof $header_photo === "undefined") {
						popupInfo('Must add header photo', true);
						return false;	
					}
					
					if (typeof $logo === "undefined") {
						popupInfo('Must add logo', true);
						return false;	
					}
					*/
					
					$.post("/ajax_add_contest.php?rndval="+ $rndval,
						{
							title: $title_cnt,
							about: $about_cnt,
							header_photo: $header_photo,
							logo: $logo,
							duration: $('#js-duration').data('value')
						},
						function(data){
							$('#prize-send').data('contestId', data);
							$('#create-contest').find('.js-file').data('contestId', data);
						});
				} //END $value == 1
				
				$('.item-progress').each(function(index){
					$(this).children('.scale').removeClass('active');
				});
				
				$('.item-progress').eq($value).children('.scale').addClass('active');
				$('#slider').height($height);
				
				$swSlider.stop().animate({'margin-left': - $widthReview * $value}, 'slow');
				$this.data('value', $value + 1);
			}
			return false;
		});
		
		$('.js-item-arrow').on('click', function(){
			var $value = parseInt($(this).data('value')),
				$height = $swSlider.children('.swPage').eq($value).height(),
				$margin_left = 0;
			
			if ($title_cnt == '' || $about_cnt == '') {
				popupInfo('All fields must be filled', true);
				return false;
			}
			
			$new_contest = false; //конкурс в базе данных, если true еще нет
			
			$('.item-progress').each(function(index){
				$(this).children('.scale').removeClass('active');
			});
					
			$('.item-progress').eq($value).children('.scale').addClass('active');
			$('#slider').height($height);
					
			if ($value != 0) {
				$margin_left = - $widthReview * $value;
			}
			$swSlider.stop().animate({'margin-left': $margin_left}, 'slow');
			$('#js-arrow').data('value', $value + 1);
			
			return false;
		});
	} //END slider
	
	if ($('#js-slider-profile').length) {
		var $js_slider = $('#js-slider-profile'),
			$numberReviews = $js_slider.find('.swPage').length,
			$widthReview = $js_slider.width(),
			$totalWidth = $numberReviews * $widthReview,
			$swSlider = $js_slider.find('.swSlider');
		$swSlider.width($totalWidth);
		
		$('#js-arrow-right').on('click', function(){
			var $this = $(this),
				$value = parseInt($this.data('value'));		
			
			if ($value == $numberReviews) {
				$swSlider.stop().animate({'margin-left': 0}, 'slow');
				$this.data('value', 1);
				$('#js-arrow-left').data('value', 0);
				$value = 0;
			} else {
				$swSlider.stop().animate({'margin-left': - $widthReview * $value}, 'slow');
				$this.data('value', $value + 1);
				$('#js-arrow-left').data('value', $value - 1);
			}
			
			if ($('#block-prize-contest').length) {
				var $active_page = $swSlider.children('.swPage').eq($value),
					$contest_id = $active_page.data('contestId'),
					$href = '/inner_page.php?id='+ $contest_id;
				
				$('#js-prt').attr('href', $href);
			}
	
			return false;
		});
		
		
		$('#js-arrow-left').on('click', function(){
			var $this = $(this),
				$value = parseInt($this.data('value'));
			
			if ($value == 0) {
				$swSlider.stop().animate({'margin-left': 0}, 'slow');
				$this.data('value', 0);
				$('#js-arrow-right').data('value', $value + 1);
			} else {
				$swSlider.stop().animate({'margin-left': - $widthReview * $value}, 'slow');
				$this.data('value', $value - 1);
				$('#js-arrow-right').data('value', $value + 1);			
			}
			
			if ($('#block-prize-contest').length) {
				var $active_page = $swSlider.children('.swPage').eq($value),
					$contest_id = $active_page.data('contestId'),
					$href = '/inner_page.php?id='+ $contest_id;
				
				$('#js-prt').attr('href', $href);
			}
		
			return false;
		});
		
	} //END js-slider-profile
	
	
	$('#prize-send').on('click', function(){
		var $this = $(this),
			$data = $this.data(),
			$num = parseInt($('#js-arrow').data('value')) - 1,
			$sw_page = $('#slider').find('.swPage').eq($num),
			$modal = $('#js-prize-modal'),
			$title = $('#prz-title').val(),
			$description = $('#prz-description').val(),
			$winners = $('#prz-winners').val(),
			$height = 0,
			$li = '',
			$rndval = new Date().getTime();
		
		if ($title == '' || $description == '' || $winners  == '') {
			popupInfo('All fields must be filled', true);
			return false;
		}
		
		if (typeof $data.imgSrc === "undefined") {
			popupInfo('Must add image', true);
			return false;	
		}
		
		$.post("/ajax_add_prize.php?rndval="+ $rndval,
			{
				title: $title,
				description: $description,
				winners: $winners,
				img: $data.imgSrc,
				type: $data.type,
				contest_id: $data.contestId
			},
			function(data){
				data = $.parseJSON(data);
				
				$li = $('<li class="item">'
					+'<div class="title">'+ $title +'</div>'
					+'<div class="description">'+ $description +'</div>'
					+'<div class="winners text-effect">'+ data.winners +'</div>'
					+'</li>');
				
				$sw_page.children('.js-wrap-prizes').append($li);
				$height = $sw_page.height();
				$('#slider').height($height);
				
				$this.removeData('imgSrc'); //reset img
				
				$modal.removeClass("bounceInUp").addClass("bounceOutDown");
				setTimeout(function() {
					$('#page').removeClass('form-open');
					$modal.hide();
				}, 700);
				   
			});
		
		return false;
	});
	
	
	$('#durations-form').find('li').click(function(){
		var $this = $(this),
			$modal = $('#js-durations-modal');
		$('#js-duration').text($this.text()).data('value', $this.data('value'));
		
		$modal.removeClass("bounceInUp").addClass("bounceOutDown");
		setTimeout(function() {
			$('#page').removeClass('form-open');
			$modal.hide();
		}, 700);
		return false;
	});
	

	
	
	$('.js-write-prize-modal').on('click', function(){
		var $data = $(this).data(),
			$modal = $('#js-prize-modal'),
			$form = $('#prize-form'),
			$h3 = $form.children('.header').children('h3');
		
		$form.find('.input__field').each(function(index){
			$(this).val('');
		});
		
		$h3.text($data.title);
		$('#prize-send').data('type', $data.type);
		
		$('#page').addClass('form-open');
		$modal.show().removeClass('bounceOutDown').addClass('bounceInUp');
		return false;
	});
	
	
	$('.js-write-prt-modal').on('click', function(){
		var $contest_id = $(this).data('contestId'),
			$modal = $('#js-photo-modal'),
			$rndval = new Date().getTime();
		
		$.post("/ajax_test_prt.php?rndval="+ $rndval,
			{
				contest_id: $contest_id
			},
			function(data){
				if (data == 'error') {
					popupInfo('You are taking part in this contest', true);
					return false;
				} else if (data == 'error2') {
					popupInfo('Companies can not participate in the contest', true);
					return false;
				} else {
					$('#page').addClass('form-open');
					$modal.show().removeClass('bounceOutDown').addClass('bounceInUp');
				}
		});
		
		return false;
	});
	
	//Add releases
	$('#js-add-releases').on('click', function(){
		
		$('#add-releases-form').find('.input__field').each(function(index){
			$(this).val('');
		});
		
		$('#releases-send').removeData('imgSrc'); //reset img
		
		$('#page').addClass('form-open');
		$('#js-releases-modal').show().removeClass('bounceOutDown').addClass('bounceInUp');
		return false;
	});
	
	$('#releases-send').on('click', function(){
		var $this = $(this),
			$modal = $('#js-releases-modal'),
			$title = $('#rls-title').val(),
			$description = $('#rls-description').val(),
			$last_releases = $('#js-last-releases'),
			$rndval = new Date().getTime(),
			$li = '';
		
		/*
		if ($title == '' || $description == '') {
			popupInfo('All fields must be filled', true);
			return false;
		}
		*/
		
		if (typeof $this.data('imgSrc') === "undefined") {
			popupInfo('Must add image', true);
			return false;	
		}
		
		$.post("/ajax_add_releases.php?rndval="+ $rndval,
			{
				title: $title,
				description: $description,
				img: $this.data('imgSrc')
			},
			function(today) {

				$li = $('<li class="item effect">'
						+'<div class="container">'
							+'<img src="/img/companies/releases/'+ $this.data('imgSrc') +'" alt="'+ $title +'">'
							+'<div class="info">'
								+'<div class="contest-title">'+ $title +'</div>'
								+'<time class="date timeago" datetime="'+ today +'"></time>'
							+'</div>'
							+'<div class="hover-block">'
								+'<div>'+ $title +'</div>'
								+'<div class="description">'+ $description +'</div>'
							+'</div>'
						+'</div>'
					+'</li>');
				
				$last_releases.prepend($li).masonry('prepended', $li);
				$last_releases.find('.timeago').timeago();
				
				$this.removeData('imgSrc'); //reset img
				
				$modal.removeClass("bounceInUp").addClass("bounceOutDown");
				setTimeout(function() {
					$('#page').removeClass('form-open');
					$modal.hide();
				}, 700);
				   
			});
		
		return false;
	});
	//END Add releases
	

	if ($('#js-last-releases').length) {
		$('#js-last-releases').masonry({
		   columnWidth: 353,
		   itemSelector: '.item',
		   fitWidth: true
		});
	}
	
	$('#profile-link').on('click', function(){
		$('#profile-menu').show();
		return false;
	});
	
	
	$('#js-to-top').on('click', function(){
		$('html, body').animate({
			scrollTop: 0
		}, 500);
		return false;
	});
	
	
	if ($('#js-end-contest').length) {
		var $end_contest = $("#js-end-contest");
			$date_end = $end_contest.data('dateEnd');
	
		$end_contest.countdown($date_end, function(event) {
			$(this).text(
			  event.strftime('%-D Days %-H Hours %-M Minutes')
			);
		});	
	}
	
	if ($('#js-brand-info-about').length) {
		
		var $brand_about = $('#js-brand-info-about');
		
		if ($brand_about.height() > 90) {
			$brand_about.addClass('short');
			$('#js-write-brand-info-about').addClass('active');
		}
	}
	
	if ($('.timeago').length) {
		$('.timeago').timeago();
	}

	/*---------------------------------------------------------------------------------------------------------*/
	$(function(){
		$(document).click(function(event) {
			if ($('#profile-menu').is(':visible')) {
				if ($(event.target).closest('#profile-menu').length) return;
				$('#profile-menu').hide();
				event.stopPropagation();
			}
		});
	});
	
	$('.js-write-modal').on('click', function(){
		var $modal = $($(this).data('modalId'));
		
		$modal.find('.input__field').each(function(){
			$(this).val('');
		});
		
		$('#page').addClass('form-open');
		$modal.show().removeClass('bounceOutDown').addClass('bounceInUp');
		return false;
	});
	
	$('.js-close').on('click', function(){
		var $modal = $($(this).data('modalId'));
		$modal.removeClass('bounceInUp').addClass('bounceOutDown');
			setTimeout(function() {
				$modal.hide();
				$('#page').removeClass('form-open');
			}, 700);
		return false;
	});
	
	$(function(){
		$(document).click(function(event) {
			if ($('.fs-box').is(':visible')) {
				if ($(event.target).closest('.js-form').length) return;
				var $modal = $('.fs-box:visible');
				$modal.removeClass('bounceInUp').addClass('bounceOutDown');
				setTimeout(function() {
					$modal.hide();
					$('#page').removeClass('form-open');
				}, 700);
				event.stopPropagation();
			}
		});
	});
	
	/*=========================================================================================================================================================================
	[1] SIGN IN
	=========================================================================================================================================================================*/
	$('#login').on('click', function(){
		var $container = $('#page'),
			$password = $('#password-l'),
			$rndval = new Date().getTime();
		
		$.post('/ajax_login.php?rndval='+ $rndval,
			{
				email: $('#email-l').val(),
				password: $password.val()
			},
			function(data){ // Обработчик ответа от сервера
				$data = $.parseJSON(data);
				if ($data.name == 'signin') {
					window.location.href = $data.redirect;
				} else {
					$password.val('').blur();
					if (data == 'code') {
					} else {
						var $field = $('#'+ $data.name +'-l'),
							$offset = $field.offset(),
							$icon = $('<i class="fa fa-exclamation-circle icon-error"></i>');
						$container.children('.icon-error').remove();		
						$icon.css({top: $offset.top + 12, left: $offset.left + $field.width()});
						$container.prepend($icon);
						$container.children('.icon-error').delay(7000).fadeOut(500);
						popupInfo($data.error, true);
					}
				}
  			});
		return false; 
	});
	/*=========================================================================================================================================================================
	[2] SIGN UP
	=========================================================================================================================================================================*/
	$('.js-switch').on('click', function(){
		var $this = $(this),
			$value = parseInt($this.data('value'));
			
		if ($this.hasClass('active')) {
			return false;
		} else {
			$('.js-switch').removeClass('active');
			$this.addClass('active');
			$swSlider.stop().animate({'margin-left': - $widthReview * $value}, 'slow');
		}
		
		return false;
	});
	
	$('#signup').on('click', function(){
		var $container = $('#page'),
			$password = $('#password-c'),
			$rndval = new Date().getTime(),
			$data = {};
		
		$.post('/ajax_signup.php?rndval='+ $rndval,
			{
				name: $('#name-c').val(),   
				password: $password.val(),
				email: $('#email-c').val()
			}, 
			function(data){ // Обработчик ответа от сервера
				$data = $.parseJSON(data);
				if ($data.name == 'signup') {
					window.location.href = $data.redirect;
				} else {
					$password.val('').blur();
					var	$field = $('#'+ $data.name +'-c'),
						$offset = $field.offset(),
						$icon = $('<i class="fa fa-exclamation-circle icon-error"></i>');
					$container.children('.icon-error').remove();		
					$icon.css({top: $offset.top + 12, left: $offset.left + $field.width()});
					$container.prepend($icon);
					$container.children('.icon-error').delay(7000).fadeOut(500);
					popupInfo($data.error, true);
				}
  			});
		return false; 
	});
	
	$('#signup-user').on('click', function(){
		var $container = $('#page'),
			$password = $('#password-u'),
			$rndval = new Date().getTime();
		
		$.post('/ajax_signup_user.php?rndval='+ $rndval,
			{
				username: $('#username-u').val(),
				lastname: $('#lastname-u').val(), 
				password: $password.val(),
				email: $('#email-u').val()
			}, 
			function(data){ // Обработчик ответа от сервера
				$data = $.parseJSON(data);
				if ($data.name == 'signup') {
					window.location.href = $data.redirect;
				} else {
					$password.val('').blur();
					var $field = $('#'+ $data.name +'-u'),
						$offset = $field.offset(),
						$icon = $('<i class="fa fa-exclamation-circle icon-error"></i>');
					$container.children('.icon-error').remove();		
					$icon.css({top: $offset.top + 12, left: $offset.left + $field.width()});
					$container.prepend($icon);
					$container.children('.icon-error').delay(7000).fadeOut(500);
					popupInfo($data.error, true);
				}
  			});
		return false; 
	});
	/*=========================================================================================================================================================================
	[3] PROFILE
	=========================================================================================================================================================================*/
	if ($('#js-profile-about').length) {
		
		var $profile_about = $('#js-profile-about');
		
		if ($profile_about.height() > 115) {
			$profile_about.addClass('short');
			$('#js-write-about').addClass('active');
		}
	}
	
	$('.js-write-edit-modal').on('click', function(){
		var $about_text = $('#js-profile-about').html(),
			$link_text = $('#js-profile-link').children('.link').text();
		
		$about_text = $about_text.replace(/<p>(.*?)<\/p>/g, '$1\n\n');
		$about_text = $.trim($about_text);
			
		$('#edit-about').val($about_text);
		$('#edit-link').val($link_text);
		
		$('#page').addClass('form-open');
		$('#js-edit-modal').show().removeClass('bounceOutDown').addClass('bounceInUp');
		return false;
	});
	
	$('#edit').on('click', function(){
		var $modal = $('#js-edit-modal'),
			$about_text = $('#edit-about').val(),
			$link_text = $('#edit-link').val(),
			$profile_about = $('#js-profile-about'),
			$rndval = new Date().getTime();
		
		$about_text = nl2ptab($about_text); ////вставляем параграфы
		$link_text = $.trim($link_text);
	
		$.post('/ajax_change_profile.php?rndval='+ $rndval,
			{
				about: $about_text,
				profile_link: $link_text
			}, 
			function(data){ // Обработчик ответа от сервера
				if (data == 'edit') {
					//***ABOUT***
					$profile_about.html($about_text);
					
					if ($profile_about.height() <= 115) {
						$profile_about.removeClass('short');
						$('#js-write-about').removeClass('active');
						
					} else {
						$profile_about.addClass('short');
						$('#js-write-about').addClass('active');
					}
					
					//***LINK***	
					if ($link_text == '') {
						$('#js-profile-link').addClass('hidden').children('.link').text('');
					} else {
						$('#js-profile-link').removeClass('hidden').children('.link').text($link_text).attr('href', $link_text);
					}
					//***CLOSE FORM***
					$modal.removeClass("bounceInUp").addClass("bounceOutDown");
					setTimeout(function() {
						$('#page').removeClass('form-open');
						$modal.hide();
					}, 700);
				}
  		});

		return false; 
	});
	
	$('#js-write-about').on('click', function(){
		var $modal = $('#js-about-modal'),
			$html = $('#js-profile-about').html();
		
		$modal.find('.js-text').html($html);
		$('#page').addClass('form-open');
		$modal.show().removeClass('bounceOutDown').addClass('bounceInUp');
		return false;
	});
	/*=========================================================================================================================================================================
	[4] FOLLOW
	=========================================================================================================================================================================*/
	$('#js-follow').on('click', function(){
		var $this = $(this),
			$data = $this.data(),
			$rndval = new Date().getTime();
		
		$.post('/ajax_subscriptions.php?rndval='+ $rndval,
			{
				company_id: $data.companyId,
				company_name: $data.name,
				company_logo: $data.logo
			}, 
			function(data){ // Обработчик ответа от сервера
				if (data == 'subscribe') {
					//type = follow | subscribe
					$this.text('un'+ $data.type);
					popupInfo('You subscribe to '+ $data.name);
				} else {
					$this.text($data.type);
					popupInfo('you unsubscribed from '+ $data.name);
				}
  		});
		
		return false;	
	});
	/*=========================================================================================================================================================================
	[5] LIKES
	=========================================================================================================================================================================*/
	$('.js-like').on('click', function(){
		var $this = $(this),
			$data = $this.data(),
			$rndval = new Date().getTime();
		
		if ($data.end == 1) {
			popupInfo('completed contests in photos estimate can not be', true);
			return false;
		}
		
		$.post('/ajax_likes.php?rndval='+ $rndval,
			{
				company_id: $data.companyId,
				contest_id: $data.contestId,
				author_id: $data.authorId
			}, 
			function(data){ // Обработчик ответа от сервера
				if (data == 'error') {
					popupInfo('You can not evaluate your photo', true);
				} else if (data == 'error2') {
					popupInfo('Can be assessed only one photo in the contest', true);
				} else if (data == 'like') {
					popupInfo('You rated photo');
				} else {
					popupInfo('You canceled like');
				}
  		});
		
		return false;	
	});
	/*=========================================================================================================================================================================
	[6] FEEDS
	=========================================================================================================================================================================*/
	$('.js-hide').on('click', function(){
		var $this = $(this),
			$li = $this.parent(),
			$feed_id = $this.data('feedId'),
			$flag = $this.data('flag'),
			$rndval = new Date().getTime();
			
		$.post('/ajax_feeds.php?rndval='+ $rndval,
			{
				feed_id: $feed_id
			}, 
			function(data){ // Обработчик ответа от сервера
				if ($flag == 1) {
					$li.addClass('hide');
					$this.data('flag', 2).text('show');
					
					$('#page').addClass('form-open');
					$('#js-feed-modal').show().removeClass('bounceOutDown').addClass('bounceInUp');
					$('#js-feed-del').data('feedId', $feed_id);
				} else {
					$li.removeClass('hide');
					$this.data('flag', 1).html('<i class="fa fa-close"></i><span>hide</span>');
				}
  		});
		
		return false;	
	});
	
	
	$('#js-feed-del').on('click', function(){
		var $feed_id = $(this).data('feedId'),
			$rndval = new Date().getTime();
			
		$.post('/ajax_del_feed.php?rndval='+ $rndval,
			{
				feed_id: $feed_id
			}, 
			function(data){ // Обработчик ответа от сервера
				$('#feed'+ $feed_id).remove();
				
				var $modal = $('#js-feed-modal');
				$modal.removeClass('bounceInUp').addClass('bounceOutDown');
				setTimeout(function() {
					$modal.hide();
					$('#page').removeClass('form-open');
				}, 700);
  		});
		
		return false;	
	});
	/*=========================================================================================================================================================================
	[7] MESSAGES
	=========================================================================================================================================================================*/
	$('#page').on('click', '.js-send-message, .js-popup-dialog', function(e){
		if ($(e.target).data('link') != 'link') {
			var $this = $(this),
				$data = $this.data(),
				$wrap_lists = $('#dialog-list'),
				rndval = new Date().getTime();
				
			$.post("/message_ajax.php?rndval="+ rndval, 
				{
					to_id: $data.toId
				}, 
				function(data) {				
					$wrap_lists.html(data);
					
					if ($wrap_lists.find('.timeago').length) {
						$wrap_lists.find('.timeago').timeago();
					}
					
					if ($this.hasClass('js-popup-dialog')) {
						$('#dialog-form').find('.js-link').attr('href', $data.toHref).text($data.toUsername);
						$('#addMessage').data('toId', $data.toId);
						$this.find('.unread').remove();
					}
					
					$('#page').addClass('form-open');
					$('#js-dialog-modal').show().removeClass('bounceOutDown').addClass('bounceInUp');
			});
		}
		return false;
	});
	
	$('#addMessage').on('click', function(){
    
		var $this = $(this),
			$to_id = $this.data('toId'),
			$content = $("#newMessage").children(".content");
			$text = $content.text(),
			$wrap_lists = $('#dialog-list'),
			$scroll = $("#dialog-form").find('.js-scroll'),
			rndval = new Date().getTime();
		
		$.post("/add_message.php?rndval="+ rndval,
			   {to_id: $to_id, text: $text}, 
			   function (data) { // Обработчик ответа от сервера  
				   if (data == "2") {
					   popupInfo("Text is too long, at your disposal to 2048 characters", true);
				   } else if (data == "3") {
					   popupInfo("Send a message can not be empty", true);
				   } else {
					   $wrap_lists.append(data);
					   
						if ($wrap_lists.find('.timeago').length) {
							$wrap_lists.find('.timeago').timeago();
						}
						   
					   $scroll.mCustomScrollbar("update");
					   $scroll.mCustomScrollbar("scrollTo", "bottom");
					   $content.text("").focus();
				   }
		});		
		return false;
	});
	
	$('#message-list').find('.js-dialog-delete').on('click', function(){
		var $data = $(this).data(),
			rndval = new Date().getTime();
	  
		$.post("/del_dialog.php?rndval="+ rndval, 
			{
				dialog_id: $data.dialogId
			},
			function (data) { // Обработчик ответа от сервера  
				$('#dialog'+ $data.dialogId).remove(); //видаляємо діалог
				popupInfo("Removed dialogue with "+ $data.toUsername);
		});
		return false;	
	});
	
	$("#newMessage").on('focus', '.content', function () {
		var $this = $(this),
		    $text = $this.text();
		if ($text == "Message...") $this.text("").addClass('active');
	});

	$("#newMessage").on('blur', '.content', function () {
        var $this = $(this),
		    $text = $this.text();
		if ($text == "") {
			$this.text("Message...").removeClass('active');
		}
	});
	/*=========================================================================================================================================================================
	[8] PASSWORD
	=========================================================================================================================================================================*/
	$('#js-write-new-password').on('click', function(){
		var $modal = $('#js-new-password-modal');
		
		//сбрасываем текст в текстовых полей
		$modal.find('.input__field').each(function(){
			$(this).val('');
		});
		
		$('#page').addClass('form-open');
		$modal.show().removeClass('bounceOutDown').addClass('bounceInUp');
		return false;
	});
	
	$('#js-change-password').on('click', function(){
		var rndval = new Date().getTime();
		
		$.post("/ajax_change_password.php?rndval="+ rndval,
			{ 
				password: $('#new-pass').val(),
				password2: $('#new-pass2').val()
			}, 
			function(data){ // Обработчик ответа от сервера
				if (data) {   
					data = $.parseJSON(data); // Преобразовываем пришедшую строку JSON в объект JS 
					if (data.error) {
						popupInfo(data.error, true);	  
					} else { 
						popupInfo(data.success);
					}
				}
		});
		return false;
	});
	/*=========================================================================================================================================================================
	[9] PASSWORD RECOVERY
	=========================================================================================================================================================================*/
	$('#js-email-reset-password').on('click', function(){
		var $rndval = new Date().getTime();
		
		$.post('/ajax_forgot_password.php?rndval='+ $rndval,
			{
				email: $('#email-fp').val(),
			}, 
			function(data){ // Обработчик ответа от сервера
				if (data) {   
					data = $.parseJSON(data); // Преобразовываем пришедшую строку JSON в объект JS 
					if (data.error) {
						popupInfo(data.error, true);	  
					} else { 
						popupInfo(data.success);
					}
				} 
		});
		return false;
	});
	
	$('#js-reset-password').on('click', function(){
		var $email = $(this).data('email');
			rndval = new Date().getTime();
		
		$.post("/ajax_reset_password.php?rndval="+ rndval,
			{ 
				email: $email,
				password: $('#password-fp').val(),
				password2: $('#password2-fp').val()
			}, 
			function(data){ // Обработчик ответа от сервера
				$data = $.parseJSON(data);
				
				if ($data.name == 'signin') {
					window.location.href = $data.redirect;
				} else {
					popupInfo($data.error, true);
				}
		});
		return false;
	});
	/*=========================================================================================================================================================================
	[10] EDIT CONTEST
	=========================================================================================================================================================================*/
	$('#page').on('click', '.js-write-edit-contest-modal', function(){
		var $id = $(this).data('value'),
			$li = $('#contest'+ $id),
			$data = $li.data(),
			$about_text = $data.about,
			$modal = $('#js-edit-contest-modal');
		
		$about_text = $about_text.replace(/<p>(.*?)<\/p>/g, '$1\n\n');
		$about_text = $.trim($about_text);
		
		$modal.find('#js-title-edit').val($data.title);
		$modal.find('#js-about-edit').val($about_text);
		$('#js-edit-contest').data('value', $id);
		
		$('#page').addClass('form-open');
		$modal.show().removeClass('bounceOutDown').addClass('bounceInUp');
	
		return false;
	});
	
	$('#page').on('click', '.js-delete-contest', function(){
		var $id = $(this).data('value'),
			$rndval = new Date().getTime();
		
		$.post('/ajax_delete_contest.php?rndval='+ $rndval,
			{
				contest_id: $id
			}, 
			function(data) { // Обработчик ответа от сервера
				if (data == 'del') {	
					$('#contest'+ $id).remove();
					popupInfo('The contest was removed');
					
					$('#js-wrap-contests').children('.item').removeClass('jp-hidden').removeAttr('style');
					jPagesFun(); // навигация
				}
		});		
		return false;
	});
	
	$('#js-edit-contest').on('click', function(){
		var $id = $(this).data('value'),
			$li = $('#contest'+ $id),
			$title = $('#js-title-edit').val(),
			$about = $('#js-about-edit').val(),
			$about_base = nl2ptab($about), //вставляем параграфы
			$rndval = new Date().getTime();
		
		$.post('/ajax_edit_contest.php?rndval='+ $rndval,
			{
				contest_id: $id,
				title: $title,
				about: $about_base
			}, 
			function(data) { // Обработчик ответа от сервера
				if (data == 'update') {
					
					$li.children('.title').text($title);
					$li.children('.about').text($about);
					
					$li.data({
						'title': $title,
						'about': $about_base
					});
					
					$('#js-edit-contest-modal').find('.js-close').click();
					popupInfo('Contest edited');
				}
		});		
		return false;
	});
	
	if ($('#edit-contest').length) {
		//EDIT DATE
		flatpickr.init.prototype.l10n.firstDayOfWeek = 1;
		flatpickr.init.prototype.defaultConfig.prevArrow = "<i class='fa fa-caret-left'></i>";
		flatpickr.init.prototype.defaultConfig.nextArrow = "<i class='fa fa-caret-right'></i>";
		
		$('.theday').each(function(){
			var $d_fp = $(this).data();
			
			var $flatpickr_th = flatpickr($d_fp.pickrId, {
				minDate: new Date()
			});
	
			$flatpickr_th.set("onChange", function(obj, str) {
					var $rndval = new Date().getTime();
				
					$.post("/ajax_edit_date.php?rndval="+ $rndval, 
						{
							contest_id: $d_fp.contestId,
							type: $d_fp.type,
							date: str
						},
						function(data) {
							if (data == 'update') {
								if ($d_fp.type == 'start') {
									popupInfo('The date of the contest changed');
								} else {
									popupInfo('Contest End Date changed');
								}
							}	   
					});
			});
			
		});
	}
	
	$('#page').on('click', '.js-delete-prize', function(){
		var $id = $(this).data('value'),
			$rndval = new Date().getTime();
		
		$.post('/ajax_delete_prize.php?rndval='+ $rndval,
			{
				id: $id
			}, 
			function(data) { // Обработчик ответа от сервера
				if (data == 'del') {	
					$('#prize'+ $id).remove();
					popupInfo('The prize was removed');
					
					$('#js-wrap-prizes').children('.item').removeClass('jp-hidden').removeAttr('style');
					jPagesFun(); // навигация
				}
		});		
		return false;
	});
	
	$('#js-type').children('.js-option').click(function(){
		var $this = $(this);
		
		$('#js-type').children('.js-option').removeClass('check');
		$this.addClass('check');
		$('#js-add-prize').data('type', $this.data('type'))
		return false;
	});
	
	$('#js-add-prize').on('click', function(){
		var $this = $(this),
			$data = $this.data(),
			$title = $('#prz-title').val(),
			$description = $('#prz-description').val(),
			$winners = $('#prz-winners').val(),
			$arr = ['rating', 'random', 'likes'],
			$rndval = new Date().getTime();
		
		if ($title == '' || $description == '' || $winners  == '') {
			popupInfo('All fields must be filled', true);
			return false;
		}
		
		if (typeof $data.imgSrc === "undefined") {
			popupInfo('Must add image', true);
			return false;	
		}
		
		$.post("/ajax_add_prize.php?rndval="+ $rndval,
			{
				title: $title,
				description: $description,
				winners: $winners,
				img: $data.imgSrc,
				type: $data.type,
				contest_id: $data.contestId
			},
			function(data){
				data = $.parseJSON(data);
				
				$li = $('<li class="item" id="prize'+ data.id +'">'
					+'<div class="title">'+ $title +'</div>'
					+'<div class="description">'+ $description +'</div>'
					+'<div class="img"><img src="/img/prizes/'+ $data.imgSrc +'" alt="'+ $title +'"></div>'
					+'<div class="type">'+ $arr[$data.type-1] +'</div>'
					+'<div class="winners">'+ data.winners +'</div>'
					+'<div class="action">'
						+'<i class="fa fa-trash-o icon delete js-delete-prize" data-value="'+ data.id +'"></i>'
					+'</div>'
				+'</li>');
				
				$('#js-wrap-prizes').append($li).children('.item').removeClass('jp-hidden').removeAttr('style');
				jPagesFun(); // навигация
				
				$this.removeData('imgSrc'); //reset img
				$('#js-add-prize-modal').find('.js-close').click();
				   
			});
		
		return false;
	});
	
	
}); //END $(document).ready

//вставляем параграфы
function nl2ptab (str) {
	str = '<p>' + (str + '').replace(/\r?\n/g, '</p><p>') + '</p>';
	return str.replace(/<p><\/p>/g, '');
}


function popupInfo($text, $error){
	if ($('.popup-info').length) $('.popup-info').remove();
		
	var $class = 'popup-info',
		$icon = 'fa fa-check-circle',
		$popupInfo = '';
		
	if ($error) {
		$class = 'popup-info popup-error';
		$icon = 'fa fa-exclamation-circle';
	}
		
	$popupInfo = $('<div><i class="'+ $icon +'"></i><p>'+ $text +'</p></div>')
	.addClass($class);
	$('#page').prepend($popupInfo);
	$('.popup-info').delay(7000).fadeOut(500);
}

function windowSize(){
	
	if ($('.js-scroll').length) {
		
		var $height = $(window).height() - 200;
		
		$('.js-scroll').height($height);
		
		if ($('#newMessage').length) {
			$height = $height - 240;
			$('#dialog-form').find('.js-scroll').height($height);
		}
		
		$('#news-feed').find('.wrap-items').height(580);
		
		$('.js-scroll').mCustomScrollbar({
			scrollInertia: 350,
			theme: 'minimal-dark'
		});
	}

}

// навигация
function jPagesFun() {
	if ($("#js-holder").length) {
		var $count = $('.js-wrap').children('.item').length,
			$js_holder = $("#js-holder");
		
		$js_holder.jPages({
			containerID : "js-wrap",
			previous : "←",
			next : "→",
			perPage : 10,
			delay : 20
		});
		
		if ($count < 10) {
			$js_holder.css('visibility', 'hidden');
		} else {
			$js_holder.css('visibility', 'visible');
		}
		
		if ($count < 1) {
			$('#js-first').css('visibility', 'hidden');
			$('#js-empty-content').show();
		} else {
			$('#js-first').css('visibility', 'visible');
			$('#js-empty-content').hide();
		}
	}	
}