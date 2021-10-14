/**
 * @file Provides dynamic handling of user authorization related forms.
 *
 * @author Alexandros Raikos <araikos@unipi.gr>
 */

(function ($) {
  "use strict";
  $(document).ready(() => {
    /**
     * Generic
     *
     * This section includes generic functionality
     * regarding the usage of the account shortcode.
     *
     */

    /**
     *
     * @param {Event} e
     */
    function authorizeUser(e) {
      e.preventDefault();

      /**
       * Handle the response after requesting user authorization.
       *
       * @param {Object} response The raw response AJAX object.
       *
       * @author Alexandros Raikos <araikos@unipi.gr>
       */
      function handleResponse(response) {
        if (response.status === 200) {
          try {
            var data = JSON.parse(response.responseText);
            setAuthorizedToken(data);
            window.location.href = "/";
          } catch (objError) {
            console.error("Invalid JSON response: " + objError);
          }
        } else if (
          response.status === 400 ||
          response.status === 404 ||
          response.status === 500
        ) {
          showAlert(
            "#policycloud-authorization button[type=submit]",
            response.responseText
          );
        } else if (response.status === 440) {
          removeAuthorization(true);
        } else {
          console.error(response.responseText);
        }
      }

      // Get form data.
      var formData = new FormData($("#policycloud-authorization")[0]);
      formData.append(
        "action",
        "policycloud_marketplace_account_authorization"
      );

      // Append WordPress nonce.
      formData.append("nonce", ajax_properties_account_authorization.nonce);

      // Activate loading button.
      $("#policycloud-authorization button[type=submit]").addClass("loading");

      // Perform AJAX request.
      $.ajax({
        url: ajax_properties_account_authorization.ajax_url,
        type: "post",
        processData: false,
        contentType: false,
        data: formData,
        dataType: "json",
        complete: handleResponse,
      });
    }

    /**
     *
     * Generic interface actions & event listeners.
     *
     */

    $("#policycloud-authorization").submit(authorizeUser);
  });
})(jQuery);
