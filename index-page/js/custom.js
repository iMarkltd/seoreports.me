(function ($) {
  'use strict';

  $(document).ready(function () {
    AOS.init();

    var successStoriesSlider = $("body").find(".success-stories-slider");

    if (successStoriesSlider.length > 0) {
      successStoriesSlider.slick({
        dots: false,
        infinite: true,
        fade: true,
        cssEase: 'linear',
        autoplay: true,
        autoplaySpeed: 5000,
        arrows: false,
        speed: 1500,
        cssEase: 'ease-in-out'
      });
    }


    var header = $("header");
    $(window).on("load scroll", function () {
      var scroll = $(window).scrollTop();

      if (scroll > 5) {
        header.addClass("fixed");
      } else {
        header.removeClass("fixed");
      }
    });

  })



  //Avoid pinch zoom on iOS
  document.addEventListener('touchmove', function (event) {
    if (event.scale !== 1) {
      event.preventDefault();
    }
  }, false);
})(jQuery)