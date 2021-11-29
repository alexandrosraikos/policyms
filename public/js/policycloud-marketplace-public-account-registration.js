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
      makeWPRequest(
        "#policycloud-registration button[type=submit]",
        "policycloud_marketplace_account_user_registration",
        AccountRegistrationProperties.nonce,
        new FormData($("#policycloud-registration")[0]),
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
