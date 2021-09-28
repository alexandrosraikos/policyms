(function ($) {
  "use strict";
  $(document).ready(() => {
    // Display pre-existing token error.
    if (ajax_prop.error === "existing-token") {
      $(".login-error").html("You are already logged in.");
    }

    $("#policycloud-login").submit((e) => {
      e.preventDefault();
      $(".submit-login").addClass("loading");

      // Perform AJAX request.
      $.ajax({
        url: ajax_prop.ajax_url,
        type: "post",
        data: {
          action: "policycloud_marketplace_login",
          nonce: ajax_prop.nonce,
          username: $("input[name=username]").val(),
          password: $("input[name=password]").val(),
        },

        // Handle response.
        complete: function (response) {
          var response_data = JSON.parse(response.responseText);
          if (response_data != null) {
            if (response_data.status === "failure") {
              $(".login-error").html(response_data.data);
            } else if (response_data.status === "success") {
              // Set 30 day cookie.
              let date = new Date();
              date.setTime(date.getTime() + 30 * 24 * 60 * 60 * 1000);
              const expires = "expires=" + date.toUTCString();
              document.cookie =
                "ppmapi-token=" + response_data.data + "; Path=/; " + expires;
              window.location.href = "/";
            }
          } else {
            $(".login-error").html("There was an internal error.");
          }
          $(".submit-login").removeClass("loading");
        },
        dataType: "json",
      });
    });
  });
})(jQuery);
