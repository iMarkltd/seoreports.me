// To make images retina, add a class "2x" to the img element
// and add a <image-name>@2x.png image. Assumes jquery is loaded.

function isRetina() {
	var mediaQuery = "(-webkit-min-device-pixel-ratio: 1.5),\
					  (min--moz-device-pixel-ratio: 1.5),\
					  (-o-min-device-pixel-ratio: 3/2),\
					  (min-resolution: 1.5dppx)";

	if (window.devicePixelRatio > 1)
		return true;

	if (window.matchMedia && window.matchMedia(mediaQuery).matches)
		return true;

	return false;
};


function retina() {

	if (!isRetina())
		return;

	$("img.2x").map(function(i, image) {

		var path = $(image).attr("src");

		path = path.replace(".png", "@2x.png");
		path = path.replace(".jpg", "@2x.jpg");

		$(image).attr("src", path);
	});
};

$(document).ready(retina);

var dropPositions = ['bottom center', 'top center'];
var drops = [];

function dropCreator(index, drop_target) {
	var drop = new Drop({
		target: drop_target,
		content: $("#download-popup").html(),
		position: dropPositions[index % dropPositions.length],
		openOn: 'click',
		classes: "drop-theme-arrows-bounce"
	});

	drops.push(drop);
}

// $(".drop-target").each(dropCreator);

function quick_validate_email(email) {
	if(typeof(email) != "string") {
		return false;
	}

	var atPos = email.indexOf("@");
	if(atPos < 1) {
		// we want at least one character before @
		return false;
	}


	var domainDotPos = email.indexOf(".", atPos);
	if(domainDotPos <= atPos+1) {
		// at least one character between @ and domain .
		return false;
	}

	// at least one character after domain .
	return (email.length - domainDotPos) > 1;
}


$('.form-error').removeClass('is-active animate fadeInUp');
$('.form-label').addClass('is-active');

function check_download_email(form_element) {
	form_jq = $(form_element);
	email = form_jq.find(".mnd-email-input").val();
	email_valid = quick_validate_email(email);

	if(!email_valid) {
		//console.log("tell user email is not valid");
		form_jq.find('.form-label').removeClass('is-active');
		form_jq.find('.form-error').addClass('is-active animate fadeInUp show');
		form_jq.find('.mnd-email-input').addClass('is-error');
	}

	return email_valid;
}

// Subscription Form

jQuery(document).ready(function($){

  var messages = $('div[data-type="message"]');
  //check if user updates the email field
  $('.cd-form .cd-email').keyup(function(event){
    //check if user has pressed the enter button (event.which == 13)
    if(event.which!= 13) {
      //if not..
      //hide messages and loading bar
      messages.removeClass('slide-in is-visible');
      $('.cd-form').removeClass('is-submitted');
    }

    var emailInput = $(this),
      insertedEmail = emailInput.val(),
      atPosition = insertedEmail.indexOf("@");
        dotPosition = insertedEmail.lastIndexOf(".");
      //check if user has inserted a "@" and a dot
      if (atPosition< 1 || dotPosition<atPosition+2 ) {
        //if he hasn't..
        //hide the submit button
        $('.cd-form').removeClass('is-active');
      } else {
        //if he has..
        //show the submit button
        $('.cd-form').addClass('is-active');
      }
  });

  //backspace doesn't fire the keyup event in android mobile
  //so we check if the email input is focused to hide messages and loading bar
  $('.cd-form .cd-email').on('focus', function(){
    messages.removeClass('slide-in is-visible');
    $('.cd-form').removeClass('is-submitted').find('.cd-loading').off('webkitTransitionEnd otransitionend oTransitionEnd msTransitionEnd transitionend');
  });


  //placeholder fallback (i.e. IE9)
  //credits http://www.hagenburger.net/BLOG/HTML5-Input-Placeholder-Fix-With-jQuery.html
  if(!Modernizr.input.placeholder){
    $('[placeholder]').focus(function() {
      var input = $(this);
      if (input.val() == input.attr('placeholder')) {
        input.val('');
        }
    }).blur(function() {
       var input = $(this);
        if (input.val() == '' || input.val() == input.attr('placeholder')) {
        input.val(input.attr('placeholder'));
        }
    }).blur();
    $('[placeholder]').parents('form').submit(function() {
        $(this).find('[placeholder]').each(function() {
        var input = $(this);
        if (input.val() == input.attr('placeholder')) {
           input.val('');
        }
        })
    });
  }
});

// Compare slider ()
//
// jQuery(document).ready(function($){
// 		//check if the .cd-image-container is in the viewport
// 		//if yes, animate it
// 		checkPosition($('.cd-image-container'));
// 		$(window).on('scroll', function(){
// 				checkPosition($('.cd-image-container'));
// 		});
//
// 		//make the .cd-handle element draggable and modify .cd-resize-img width according to its position
// 		$('.cd-image-container').each(function(){
// 				var actual = $(this);
// 				drags(actual.find('.cd-handle'), actual.find('.cd-resize-img'), actual, actual.find('.cd-image-label[data-type="original"]'), actual.find('.cd-image-label[data-type="modified"]'));
// 		});
//
// 		//upadate images label visibility
// 		$(window).on('resize', function(){
// 				$('.cd-image-container').each(function(){
// 						var actual = $(this);
// 						updateLabel(actual.find('.cd-image-label[data-type="modified"]'), actual.find('.cd-resize-img'), 'left');
// 						updateLabel(actual.find('.cd-image-label[data-type="original"]'), actual.find('.cd-resize-img'), 'right');
// 				});
// 		});
// });
//
// function checkPosition(container) {
// 		container.each(function(){
// 				var actualContainer = $(this);
// 				if( $(window).scrollTop() + $(window).height()*0.5 > actualContainer.offset().top) {
// 						actualContainer.addClass('is-visible');
// 				}
// 		});
// }
//
// //draggable funtionality - credits to http://css-tricks.com/snippets/jquery/draggable-without-jquery-ui/
// function drags(dragElement, resizeElement, container, labelContainer, labelResizeElement) {
// 		dragElement.on("mousedown vmousedown", function(e) {
// 				dragElement.addClass('draggable');
// 				resizeElement.addClass('resizable');
//
// 				var dragWidth = dragElement.outerWidth(),
// 						xPosition = dragElement.offset().left + dragWidth - e.pageX,
// 						containerOffset = container.offset().left,
// 						containerWidth = container.outerWidth(),
// 						minLeft = containerOffset + 10,
// 						maxLeft = containerOffset + containerWidth - dragWidth - 10;
//
// 				dragElement.parents().on("mousemove vmousemove", function(e) {
// 						leftValue = e.pageX + xPosition - dragWidth;
//
// 						//constrain the draggable element to move inside his container
// 						if(leftValue < minLeft ) {
// 								leftValue = minLeft;
// 						} else if ( leftValue > maxLeft) {
// 								leftValue = maxLeft;
// 						}
//
// 						widthValue = (leftValue + dragWidth/2 - containerOffset)*100/containerWidth+'%';
//
// 						$('.draggable').css('left', widthValue).on("mouseup vmouseup", function() {
// 								$(this).removeClass('draggable');
// 								resizeElement.removeClass('resizable');
// 						});
//
// 						$('.resizable').css('width', widthValue);
//
// 						updateLabel(labelResizeElement, resizeElement, 'left');
// 						updateLabel(labelContainer, resizeElement, 'right');
//
// 				}).on("mouseup vmouseup", function(e){
// 						dragElement.removeClass('draggable');
// 						resizeElement.removeClass('resizable');
// 				});
// 				e.preventDefault();
// 		}).on("mouseup vmouseup", function(e) {
// 				dragElement.removeClass('draggable');
// 				resizeElement.removeClass('resizable');
// 		});
// }
//
// function updateLabel(label, resizeElement, position) {
// 		if(position == 'left') {
// 				( label.offset().left + label.outerWidth() < resizeElement.offset().left + resizeElement.outerWidth() ) ? label.removeClass('is-hidden') : label.addClass('is-hidden') ;
// 		} else {
// 				( label.offset().left > resizeElement.offset().left + resizeElement.outerWidth() ) ? label.removeClass('is-hidden') : label.addClass('is-hidden') ;
// 		}
// }
//
// $('a.anchor').click(function() {
// 	if (location.pathname.replace(/^\//,'') == this.pathname.replace(/^\//,'')
// 		|| location.hostname == this.hostname) {
//
// 			var target = $(this.hash);
// 			target = target.length ? target : $('[name=' + this.hash.slice(1) +']');
// 			if (target.length) {
// 				$('html,body').animate({
// 					scrollTop: target.offset().top - $('.navbar').outerHeight()
// 				}, 500);
// 				return false;
// 			}
// 		}
// 	});
