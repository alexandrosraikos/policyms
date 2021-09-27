(function ($) {
  "use strict";
  $(document).ready(() => {
    // Display pre-existing token error.
    if (ajax_prop.error === "existing-token") {
      $(".registration-error").html("You are already logged in.");
    }

    $("#policycloud-registration").submit((e) => {
      e.preventDefault();
      $(".submit-registration").addClass("loading");

      // Perform AJAX request.
      $.ajax({
        url: ajax_prop.ajax_url,
        type: "post",
        data: {
          action: "registration",
          nonce: ajax_prop.nonce,
          username: $("input[name=username]").val(),
          password: $("input[name=password]").val(),
          password_confirm: $("input[name=password-confirm]").val(),
          email: $("input[name=email]").val(),
          name: $("input[name=name]").val(),
          surname: $("input[name=surname]").val(),
          phone: $("input[name=phone]").val(),
          organization: $("input[name=organization]").val(),
          title: $("select[name=title]").val(),
          gender: $("select[name=gender]").val(),
        },

        // Handle response.
        complete: function (response) {
          var response_data = JSON.parse(response.responseText);
          if (response_data != null) {
            if (response_data.status === "failure") {
              $(".registration-error").html(response_data.data);
            } else if (response_data.status === "success") {
              // Set 30 day cookie.
              let date = new Date();
              date.setTime(date.getTime() + 30 * 24 * 60 * 60 * 1000);
              const expires = "expires=" + date.toUTCString();
              document.cookie =
                "ppmapi-token=" + response_data.data + "; " + expires;
            }
          } else {
            $(".registration-error").html("There was an internal error.");
          }
          $(".submit-registration").removeClass("loading");
        },
        dataType: "json",
      });
    });
  });
})(jQuery);
