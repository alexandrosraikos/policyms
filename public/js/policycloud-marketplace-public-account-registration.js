(function ($) {
  "use strict";
  $(document).ready(() => {
    // Display pre-existing token error.
    if (ajax_properties_account_registration.error === "existing-token") {
      $("#policycloud-registration .error").html("You are already logged in.");
    }

    console.log(ajax_properties_account_registration.error);
    $("#policycloud-registration").submit((e) => {
      e.preventDefault();
      $("#policycloud-registration button[type=submit]").addClass("loading");

      // Perform AJAX request.
      $.ajax({
        url: ajax_properties_account_registration.ajax_url,
        type: "post",
        data: {
          action: "policycloud_marketplace_account_registration",
          nonce: ajax_properties_account_registration.nonce,
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
              $("#policycloud-registration .error").html(response_data.data);
            } else if (response_data.status === "success") {
              // Set 30 day cookie.
              let date = new Date();
              date.setTime(date.getTime() + 30 * 24 * 60 * 60 * 1000);
              const expires = "expires=" + date.toUTCString();
              document.cookie =
                "ppmapi-token=" + response_data.data + "; " + expires;
              window.location.href = "/";
            }
          } else {
            $("#policycloud-registration .error").html(
              "There was an internal error."
            );
          }
          $("#policycloud-registration button[type=submit]").removeClass(
            "loading"
          );
        },
        dataType: "json",
      });
    });
  });
})(jQuery);
