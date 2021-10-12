// TODO @alexandrosraikos: Clean up, comment and claim.

(function ($) {
  "use strict";
  $(document).ready(() => {
    // Dynamic socials fields
    $("#policycloud-registration .socials button.add-field").click(function (
      e
    ) {
      e.preventDefault();
      $("#policycloud-registration .socials > div > div:last-of-type")
        .clone()
        .appendTo("#policycloud-registration .socials > div");
      $("#policycloud-registration .socials button.remove-field").prop(
        "disabled",
        $("#policycloud-registration .socials button.remove-field").length === 1
      );
    });
    $(document).on(
      "click",
      "#policycloud-registration .socials button.remove-field",
      function (e) {
        e.preventDefault();
        $(this).parent().remove();
        $("#policycloud-registration .socials button.remove-field").prop(
          "disabled",
          $("#policycloud-registration .socials button.remove-field").length ===
            1
        );
      }
    );

    // Registration submission
    $("#policycloud-registration").submit((e) => {
      e.preventDefault();
      var formData = new FormData($("#policycloud-registration")[0]);
      formData.append("action", "policycloud_marketplace_account_registration");
      formData.append("nonce", ajax_properties_account_registration.nonce);

      $("#policycloud-registration button[type=submit]").addClass("loading");

      // Perform AJAX request.
      $.ajax({
        url: ajax_properties_account_registration.ajax_url,
        type: "post",
        processData: false,
        contentType: false,
        data: formData,
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
