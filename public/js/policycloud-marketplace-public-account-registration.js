/**
 * @file Provides dynamic fields and handles AJAX requests for forms and buttons
 * in the account registration shortcode.
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
     * Add a double input field to the socials container.
     *
     * @param {Event} e
     *
     * @author Alexandros Raikos <araikos@unipi.gr>
     */
    function addSocialField(e) {
      e.preventDefault();
      $("#policycloud-registration .socials > div > div:last-of-type")
        .clone()
        .appendTo("#policycloud-registration .socials > div");
      $("#policycloud-registration .socials button.remove-field").prop(
        "disabled",
        $("#policycloud-registration .socials button.remove-field").length === 1
      );
    }
    /**
     *
     * Remove a double input field from the socials container.
     *
     * @param {Event} e
     *
     * @author Alexandros Raikos <araikos@unipi.gr>
     */
    function removeSocialField(e) {
      e.preventDefault();
      $(this).parent().remove();
      $("#policycloud-registration .socials button.remove-field").prop(
        "disabled",
        $("#policycloud-registration .socials button.remove-field").length === 1
      );
    }

    /**
     * Prepare and submit via AJAX the registration information fields.
     *
     * @param {Event} e
     *
     * @author Alexandros Raikos <araikos@unipi.gr>
     */
    function registerUser(e) {
      e.preventDefault();

      /**
       * Handle the response after requesting user registration.
       *
       * @param {Event} response
       *
       * @author Alexandros Raikos <araikos@unipi.gr>
       */
      function handleResponse(response) {
        if (response.status === 200) {
          try {
            var data = JSON.parse(response.responseText);
            setAuthorizedToken(data.newToken);

            // Handle any warning message in case of semi-complete registration.
            if (data.warningMessage) {
              $("#policycloud-registration fieldset").prop("disabled", true);
              showAlert(
                "#policycloud-registration button[type=submit]",
                data.warningMessage
              );
            } else {
              window.location.reload();
            }
          } catch (objError) {
            console.error("Invalid JSON response: " + objError);
          }
        } else if (
          response.status === 400 ||
          response.status === 404 ||
          response.status === 500
        ) {
          showAlert(
            "#policycloud-registration button[type=submit]",
            response.responseText
          );
        } else {
          console.error(response.responseText);
        }

        $("#policycloud-registration button[type=submit]").removeClass(
          "loading"
        );
      }

      // Prepare form data.
      var formData = new FormData($("#policycloud-registration")[0]);
      formData.append("action", "policycloud_marketplace_account_registration");
      formData.append("nonce", ajax_properties_account_registration.nonce);

      // Add "loading" class to the submission button.
      $("#policycloud-registration button[type=submit]").addClass("loading");

      // Perform AJAX request.
      $.ajax({
        url: ajax_properties_account_registration.ajax_url,
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

    // Add another social field.
    $("#policycloud-registration .socials button.add-field").click(
      addSocialField
    );

    // Remove last social field.
    $(document).on(
      "click",
      "#policycloud-registration .socials button.remove-field",
      removeSocialField
    );

    // Submit the registration.
    $("#policycloud-registration").submit(registerUser);
  });
})(jQuery);
