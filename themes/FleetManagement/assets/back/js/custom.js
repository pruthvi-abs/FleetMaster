(function ($) {
    "use strict"; // Start of use strict
    $('#sidebarCollapse').on('click', function () {
        $('#sidebar').toggleClass('active');
    });
    /*Loader Javascript
    -------------------*/
    var window_var = $(window);
    window_var.on('load', function () {
        $(".loading").fadeOut("fast");
    });
    // scroll to top
    $(window).scroll(function () {
        if ($(this).scrollTop() > 50) {
            $('#back-to-top').fadeIn();
        } else {
            $('#back-to-top').fadeOut();
        }
    });
    // scroll body to 0px on click
    $('#back-to-top').click(function () {
        $('body,html').animate({
            scrollTop: 0
        }, 800);
        return false;
    });
    // scroll to top End
    // console.log('called');

    var list = $('ul li');
  
    if ($(list).has('ul')) {
    //   console.log('child ul');
    //   console.log(list);
      list.find('ul').addClass('collapse');
    }
  
    $('span.cexpand').on("click", function(e) {
        // console.log('clicked');
        // $('ul.collapse').animate({height: 'toggle'});
        $('ul.collapse').removeClass('open');
      if ($(this).parent('li').find('ul').hasClass('collapse')) {
        $(this).parent('li').find('ul').addClass('in');
        $(this).parent('li').find('ul').animate({height: 'toggle'});
        // $(this).parent('li').find('ul').collapse('toggle');
        $(this).toggleClass('open');
      }
    });
})(jQuery);
