(function ($) {
  "use strict";
  $(document).ready(function () {
    $("#pform input")
      .not("#submit1")
      .keydown(function (event) {
        if (event.keyCode == 13) {
          event.preventDefault();
          return false;
        }
      });
    $("#edit1").click(function () {
      /*  $("#descs").addClass("hidden");*/
      /*$("#descs").html('<input type="text" id="descasset" value="' + $('#descs').text() + '">');*/
      $("#descs").html(
        '<textarea id="w3review" name="w3review" rows="4" cols="50" >' +
          $("#descs").text() +
          " </textarea>"
      );
      //$("#descs").html('<textarea id="story"  placeholder="' + $('#descs').text() + '" value="' + $('#descs').text() + '" rows="5" cols="33"> </textarea>');
      $("#edit1").remove();
      $("#pguest").removeClass("hidden");
      //$('#submit1').removeClass('hidden');
      // $('#edit1').addClass('hidden');
    });
    // Show the first tab and hide the rest
    $("#tabs2-nav li:first-child").addClass("active");
    $(".tab-content").hide();
    $(".tab-content:first").show();

    // Click function
    $("#tabs2-nav li").click(function () {
      $("#tabs2-nav li").removeClass("active");
      $(this).addClass("active");
      $(".tab-content").hide();

      var activeTab = $(this).find("a").attr("href");
      $(activeTab).fadeIn();
      return false;
    });

    /*accordion */
    $(".accordion").on("click", function () {
      $(this).toggleClass("active");

      var panel = this.nextElementSibling;
      if (panel.style.maxHeight) {
        panel.style.maxHeight = null;
      } else {
        panel.style.maxHeight = panel.scrollHeight + "px";
      }
    });
  });
})(jQuery);
