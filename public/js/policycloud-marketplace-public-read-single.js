(function ($) {
  "use strict";
  $(document).ready(() => {
    
   // Show the first tab and hide the rest
$('#tabs-nav li:first-child').addClass('active');
$('.tab-content').hide();
$('.tab-content:first').show();

// Click function
$('#tabs-nav li').click(function(){
  $('#tabs-nav li').removeClass('active');
  $(this).addClass('active');
  $('.tab-content').hide();
  
  var activeTab = $(this).find('a').attr('href');
  $(activeTab).fadeIn();
  return false;
});
/*slideshow */
$("#slideshow > div:gt(0)").hide();

setInterval(function() { 
  $('#slideshow > .slide-tab:first')
    .fadeOut(1000)
    .next()
    .fadeIn(1000)
    .end()
    .appendTo('#slideshow');
},  3000);
    /*accordion */
    $(".accordion").on( "click", function() {
      $(this).toggleClass('active');
      
      var panel = this.nextElementSibling;
      if (panel.style.maxHeight) {
        panel.style.maxHeight = null;
      } else {
        panel.style.maxHeight = panel.scrollHeight + "px";
      } 
    });
   
    /* ΕΔΩ */
  });
})(jQuery);
