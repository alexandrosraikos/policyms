(function ($) {
  "use strict";
  $(document).ready(() => {
    $("#pform input").not("#submit1").keydown(function (event) {
      if (event.keyCode == 13) {
        event.preventDefault();
        return false;
      }
    });
    /*add toggle input*/
    /* $(document).on("click", ".edit2", function(){
       $(this).parents("tr").find("td:not(:last-child)").each(function(){
         $(this).attr("contenteditable","true");
       });		
     });*/
    $(document).on("click", ".edit2", function () {
      $(this).parents("tr").find("td:not(:last-child)").each(function () {
        $(this).html('<input type="text" size ="1" value="' + $(this).text() + '">');
      });
      $(this).parents("tr").find(".add, .edit2").toggle();
      $(".add-new").attr("disabled", "disabled");
    });
    /*second row*/
    $(document).on("click", ".edit3", function () {
      $(this).parents("tr").find("td:not(:last-child)").each(function () {
        $(this).html('<input type="text" size ="1" value="' + $(this).text() + '" >');
      });
      $(this).parents("tr").find(".add, .edit3").toggle();
      $(".add-new").attr("disabled", "disabled");
    });
    /*  add and remove forms       */
    $('#edit1').click(function () {
      $(".edit2").trigger("click");
      $(".edit3").trigger("click");

      $('#description-title').html('<input type="text" name ="dtitle" class="h2title" value="' + $('#description-title').text() + '" style="width: 600px; font-size:65px;">');
      // $('#description-title').addClass('hidden');
      /*  $("#descs").addClass("hidden");*/
      //$("#description-title").html('<input type="text" id="titlesasset" value="' + $('#description-title').text() + '">');
      //$('$dstitle').html('<input type="text" id="titleasset" value="' + $('#description-title').text() + '">');
      //$('#dstitle').removeClass('hidden');
      $("#descp").html('<textarea id="descp1" name="w3review" rows="4" cols="50" >' + $('#descp').text() + ' </textarea>');
      $("#descs").html('<textarea id="descs1" name="w3review" rows="4" cols="50" >' + $('#descs').text() + ' </textarea>');
      //$("#descs").html('<textarea id="story"  placeholder="' + $('#descs').text() + '" value="' + $('#descs').text() + '" rows="5" cols="33"> </textarea>');
      $('#edit1').remove();
      $('#pguest').removeClass('hidden');
      //$('#submit1').removeClass('hidden');
      // $('#edit1').addClass('hidden');

    });
    // Show the first tab and hide the rest
    $('#tabs-nav li:first-child').addClass('active');
    $('.tab-content').hide();
    $('.tab-content:first').show();

    // Click function
    $('#tabs-nav li').click(function () {
      $('#tabs-nav li').removeClass('active');
      $(this).addClass('active');
      $('.tab-content').hide();

      var activeTab = $(this).find('a').attr('href');
      $(activeTab).fadeIn();
      return false;
    });

    /*slideshow */
    $("#slideshow > div:gt(0)").hide();

    setInterval(function () {
      $('#slideshow > .slide-tab:first')
        .fadeOut(1000)
        .next()
        .fadeIn(1000)
        .end()
        .appendTo('#slideshow');
    }, 3000);
    /*accordion */
    $(".accordion").on("click", function () {
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
