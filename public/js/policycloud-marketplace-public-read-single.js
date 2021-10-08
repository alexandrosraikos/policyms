(function ($) {
  "use strict";
  $(document).ready(() => {
    $("#policycloud-marketplace-description-edit input")
      .not("#submit1")
      .keydown(function (event) {
        if (event.keyCode == 13) {
          event.preventDefault();
          return false;
        }
      });
    /* range filter   */

    /* $(document).on("input change", "#pcslider", function () {
       $("#pcslider_value").html($(this).val());
     });
 
     /*add toggle input*/
    /* $(document).on("click", ".edit2", function(){
       $(this).parents("tr").find("td:not(:last-child)").each(function(){
         $(this).attr("contenteditable","true");
       });		
     });*/
    /* $(document).on("click", ".edit2", function () {
      $(this).parents("tr").find("td:not(:last-child)").each(function () {
        $(this).html('<input type="text" size ="1" value="' + $(this).text() + '">');
      });
      $(this).parents("tr").find(".add, .edit2").toggle();
      $(".add-new").attr("disabled", "disabled");
    });
    /*second row*/
    /* $(document).on("click", ".edit2", function () {
        $('#pctablename').html('<input type="text" size ="1" value="' + $('#pctablename').text() + '" >');
      });
    //remove input*/

    /*  add and remove forms       */
    $("#edit1").click(function () {
      // $(".edit2").trigger("click");
      // $(".edit3").trigger("click");
      $(".pctablename").html(
        '<input type="text" size ="1" value="' +
          $(".pctablename").text() +
          '" >'
      );

      //description edits
      $("#description-title").html(
        '<input type="text" id="dtitle" name ="dtitle" class="h2title" value="' +
          $("#description-title").text() +
          '" style="width: 650px; font-size:40px;">'
      );
      // $('#description-title').addClass('hidden');
      /*  $("#descs").addClass("hidden");*/
      //$("#description-title").html('<input type="text" id="titlesasset" value="' + $('#description-title').text() + '">');
      //$('$dstitle').html('<input type="text" id="titleasset" value="' + $('#description-title').text() + '">');
      //$('#dstitle').removeClass('hidden');
      $("#descp").html(
        '<textarea id="descp1" name="w3review" rows="4" cols="20" >' +
          $("#descp").text() +
          " </textarea>"
      );
      $("#descs").html(
        '<textarea id="descs1" name="w3review" rows="4" cols="20" >' +
          $("#descs").text() +
          " </textarea>"
      );
      //$("#descs").html('<textarea id="story"  placeholder="' + $('#descs').text() + '" value="' + $('#descs').text() + '" rows="5" cols="33"> </textarea>');
      $("#edit1").addClass("hidden");
      $("#pguest").removeClass("hidden");
      //$('#submit1').removeClass('hidden');
      // $('#edit1').addClass('hidden');
    });
    // Show the first tab and hide the rest
    $("#tabs-nav li:first-child").addClass("active");
    $(".tab-content").hide();
    $(".tab-content:first").show();

    // Click function
    $("#tabs-nav li").click(function () {
      $("#tabs-nav li").removeClass("active");
      $(this).addClass("active");
      $(".tab-content").hide();

      var activeTab = $(this).find("a").attr("href");
      $(activeTab).fadeIn();
      return false;
    });
    //remove
    $("#pcdelete").click(function () {
      if ($("#dtitle").length) {
        $("#description-title").html("<h1>" + $("#dtitle").val() + "</h1>");

        //  $("#description-title").text();
        //$('#dtitle').remove();
      }
      if ($("#descp1").length) {
        $("#descp").html("<p>" + $("#descp1").val() + " </p>");
      }
      if ($("#descs1").length) {
        $("#descs").html("<p>" + $("#descs1").val() + " </p>");
      }
      $("#edit1").removeClass("hidden");
      // $( "<button id='edit1'>Edit</button>" ).insertAfter( "p" );
      $("#pcdelete").addClass("hidden");
      $("#pguest").addClass("hidden");
    });
    /*slideshow */
    $("#slideshow > div:gt(0)").hide();

    setInterval(function () {
      $("#slideshow > .slide-tab:first")
        .fadeOut(1000)
        .next()
        .fadeIn(1000)
        .end()
        .appendTo("#slideshow");
    }, 3000);
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

    /*
      ------------------------
      AJAX Description Editing
      ------------------------
    */

    $("#policycloud-marketplace-description-edit").submit((e) => {
      e.preventDefault();
      $(
        "#policycloud-marketplace-description-edit input[type=submit]"
      ).addClass("loading");

      // Perform AJAX request.
      $.ajax({
        url: ajax_properties_description_editing.ajax_url,
        type: "post",
        data: {
          action: "policycloud_marketplace_description_edit",
          nonce: ajax_properties_description_editing.nonce,
          username: $("input[name=username]").val(),
        },

        // Handle response.
        complete: function (response) {
          var response_data = JSON.parse(response.responseText);
          if (response_data != null) {
            if (response_data.status === "failure") {
              $(".registration-error").html(response_data.data);
            } else if (response_data.status === "success") {
              window.location.reload();
            }
          }
          if (response.status != 200) {
            $(".error").html(
              "HTTP Error " + response.status + ": " + response.statusText
            );
          }
          $(
            "#policycloud-marketplace-description-edit input[type=submit]"
          ).removeClass("loading");
        },
        dataType: "json",
      });
    });
  });
})(jQuery);
