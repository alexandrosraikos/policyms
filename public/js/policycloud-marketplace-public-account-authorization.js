// TODO @alexandrosraikos: Clean up, comment and claim.

(function ($) {
  "use strict";
  $(document).ready(() => {
    $("#policycloud-authorization").submit((e) => {
      e.preventDefault();
      var formData = new FormData($("#policycloud-authorization")[0]);
      formData.append(
        "action",
        "policycloud_marketplace_account_authorization"
      );
      formData.append("nonce", ajax_properties_account_authorization.nonce);

      $("#policycloud-authorization button[type=submit]").addClass("loading");

      // Perform AJAX request.
      $.ajax({
        url: ajax_properties_account_authorization.ajax_url,
        type: "post",
        processData: false,
        contentType: false,
        data: formData,
        // Handle response.
        complete: function (response) {
          var response_data = JSON.parse(response.responseText);
          if (response_data != null) {
            if (response_data.status === "failure") {
              $("#policycloud-authorization .error").html(response_data.data);
            } else if (response_data.status === "success") {
              setAuthorizedToken(response_data.data);
              window.location.href = "/";
            }
          } else {
            $("#policycloud-authorization .error").html(
              "There was an internal error."
            );
          }
          $("#policycloud-authorization button[type=submit]").removeClass(
            "loading"
          );
        },
        dataType: "json",
      });
    });
  });
})(jQuery);
