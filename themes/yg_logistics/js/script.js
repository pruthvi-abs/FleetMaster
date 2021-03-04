(function ($, Drupal) {
  Drupal.behaviors.myModuleBehavior = {
    attach: function (context, settings) {

      jQuery(document).ready(function( $ ) {
      $('.faq').click( function(){
        if ( $(this).hasClass('bgcolor') ) {
            $(this).removeClass('bgcolor');
        } else {
            $('.faq').removeClass('bgcolor');
            $(this).addClass('bgcolor');
        }
      });
        $(".our_services").owlCarousel({
          autoplay: true,
          dots: false,
          loop: true,
          nav: true,
          lazyLoad : true,
          responsive: { 0: { items: 1 }, 500: { items: 2 }, 768: { items: 2 }, 900: { items: 3 }
          }
        });
          $( ".owl-prev").html('<i class="fa fa-chevron-left"></i>');
          $( ".owl-next").html('<i class="fa fa-chevron-right"></i>');

      $('ul.nav li.dropdown').hover(function() {
        $(this).find('.dropdown-menu').stop(true, true).delay(200).fadeIn(500);
      }, function() {
        $(this).find('.dropdown-menu').stop(true, true).delay(200).fadeOut(500);
      });

        new WOW().init();  
          $('.testimonial').owlCarousel({
          loop: true,
          margin: 10,
          responsive: {
            0:{
                items:1
            },
            600:{
                items:1
            },
            1000:{
                items:1
            }    
          }
        });

        $('.gallery-popup').magnificPopup({
          type: 'image',
          removalDelay: 300,
          mainClass: 'mfp-fade',
          gallery: {
            enabled: true
          },
          zoom: {
            enabled: false,
            duration: 300,
            easing: 'ease-in-out',
            opener: function(openerElement) {
              return openerElement.is('img') ? openerElement : openerElement.find('img');
            }
          }
        });
      });

      jQuery(document).ready(function( $ ) {
       $('.popup-youtube').magnificPopup({
              type: 'iframe',
              mainClass: 'mfp-fade',
              removalDelay: 160,
              preloader: false,
              fixedContentPos: false
        }); 
      });

      jQuery(window).scroll(function(){
          var scroll = $(window).scrollTop();
          if (scroll >= 100) {
              $("#sticky_menu").addClass("sticky");
          } else {
              $("#sticky_menu").removeClass("sticky");
          }
      });

      // Back-to-top
        var btn = $('#back_to_top');
        $(window).scroll(function() {
          if ($(window).scrollTop() > 300) {
            btn.addClass('show');
          } else {
            btn.removeClass('show');
          }
        });
        btn.on('click', function(e) {
          e.preventDefault();
          $('html, body').animate({scrollTop:0}, '300');
        });


    }
  };
})(jQuery, Drupal);

 