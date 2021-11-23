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

      // Add "loading" class to the submission button.
      $("#policycloud-registration button[type=submit]").addClass("loading");

      // Prepare form data.
      var formData = new FormData($("#policycloud-registration")[0]);
      formData.append("action", "policycloud_marketplace_account_registration");
      formData.append("nonce", ajax_properties_account_registration.nonce);

      makeWPRequest(
        "#policycloud-registration button[type=submit]",
        "policycloud_marketplace_account_registration",
        AccountRegistrationProperties.nonce,
        formData,
        () => {
          window.location.href(GlobalProperties.rootURLPath);
        }
      );
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
