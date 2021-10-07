(function ($) {
  "use strict";
  $(document).ready(() => {
    $("#policycloud-authentication").submit((e) => {
      e.preventDefault();
      $("#policycloud-authentication button[type=submit]").addClass("loading");
      // TODO @alexandrosraikos: Handle user email as credential.
      // Perform AJAX request.
      $.ajax({
        url: ajax_properties_account_authentication.ajax_url,
        type: "post",
        data: {
          action: "policycloud_marketplace_account_authentication",
          nonce: ajax_properties_account_authentication.nonce,
          username: $("input[name=username]").val(),
          password: $("input[name=password]").val(),
        },

        // Handle response.
        complete: function (response) {
          var response_data = JSON.parse(response.responseText);
          if (response_data != null) {
            if (response_data.status === "failure") {
              $("#policycloud-authentication .error").html(response_data.data);
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
            $("#policycloud-authentication .error").html(
              "There was an internal error."
            );
          }
          $("#policycloud-authentication button[type=submit]").removeClass(
            "loading"
          );
        },
        dataType: "json",
      });
    });
  });
})(jQuery);
