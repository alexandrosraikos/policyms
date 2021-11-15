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

      // Activate loading button.
      $("#policycloud-authorization button[type=submit]").addClass("loading");

      // Get form data.
      var formData = new FormData($("#policycloud-authorization")[0]);
      formData.append(
        "action",
        "policycloud_marketplace_account_authorization"
      );

      // Append WordPress nonce.
      formData.append("nonce", ajax_properties_account_authorization.nonce);

      // Perform AJAX request.
      $.ajax({
        url: ajax_properties_account_authorization.ajax_url,
        type: "post",
        processData: false,
        contentType: false,
        data: formData,
        dataType: "json",
        complete: (response) => {
          handleAJAXResponse(
            response,
            "#policycloud-authorization button[type=submit]",
            (data) => {
              setAuthorizedToken(data);
              window.location.href = GlobalProperties.rootURLPath;
            }
          );
        },
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
