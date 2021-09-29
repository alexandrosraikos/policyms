(function ($) {
  "use strict";
  $(document).ready(function () {
    var current_fs, next_fs, previous_fs; //fieldsets
    var opacity;
    var current = 1;
    var steps = $("fieldset").length;

    setProgressBar(current);

    $(".next").click(function () {
      current_fs = $(this).parent();
      next_fs = $(this).parent().next();

      //Add Class Active
      $("#progressbar li").eq($("fieldset").index(next_fs)).addClass("active");

      //show the next fieldset
      next_fs.show();
      //hide the current fieldset with style
      current_fs.animate(
        { opacity: 0 },
        {
          step: function (now) {
            // for making fielset appear animation
            opacity = 1 - now;

            current_fs.css({
              display: "none",
              position: "relative",
            });
            next_fs.css({ opacity: opacity });
          },
          duration: 500,
        }
      );
      setProgressBar(++current);
    });

    $(".previous").click(function () {
      current_fs = $(this).parent();
      previous_fs = $(this).parent().prev();

      //Remove class active
      $("#progressbar li")
        .eq($("fieldset").index(current_fs))
        .removeClass("active");

      //show the previous fieldset
      previous_fs.show();

      //hide the current fieldset with style
      current_fs.animate(
        { opacity: 0 },
        {
          step: function (now) {
            // for making fielset appear animation
            opacity = 1 - now;

            current_fs.css({
              display: "none",
              position: "relative",
            });
            previous_fs.css({ opacity: opacity });
          },
          duration: 500,
        }
      );
      setProgressBar(--current);
    });

    function setProgressBar(curStep) {
      var percent = parseFloat(100 / steps) * curStep;
      percent = percent.toFixed();
      $(".progress-bar").css("width", percent + "%");
    }

    /*
      ------------------------
      AJAX Description Creation
      ------------------------
    */

    $("#policycloud-marketplace-description-create").submit((e) => {
      e.preventDefault();
      $(
        "#policycloud-marketplace-description-create input[type=submit]"
      ).addClass("loading");

      // Perform AJAX request.
      // TODO @alexandrosraikos: Add description creation form fields for processing.
      $.ajax({
        url: ajax_properties_description_creation.ajax_url,
        type: "post",
        data: {
          action: "policycloud_marketplace_description_create",
          nonce: ajax_properties_description_creation.nonce,
          username: $("input[name=username]").val(),
        },

        // Handle response.
        complete: function (response) {
          var response_data = JSON.parse(response.responseText);
          if (response_data != null) {
            if (response_data.status === "failure") {
              $(".error").html(response_data.data);
            } else if (response_data.status === "success") {
              // TODO @alexandrosraikos: Redirect to account assets page.
              window.location.reload();
            }
          }
          if (response.status != 200) {
            $(".error").html(
              "HTTP Error " + response.status + ": " + response.statusText
            );
          }
          $(
            "#policycloud-marketplace-description-creation input[type=submit]"
          ).removeClass("loading");
        },
        dataType: "json",
      });
    });
  });
})(jQuery);
