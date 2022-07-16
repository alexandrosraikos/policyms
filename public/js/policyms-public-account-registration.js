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
      const newSocialField = $("<div><input type='text' name='socials-title[]' placeholder='Example' /><input type='url' name='socials-url[]' placeholder='https://www.example.org/' /><button class='remove-field' title='Remove this link.' ><span class='fas fa-times'></span></button></div>");
      newSocialField.find('input[name*=socials]').each(
        (index, element) => {
          $(element).val("");
        }
      )

      newSocialField.appendTo("#policyms-registration .socials > div");
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
    }

    /**
     * Prepare and submit via AJAX the registration information fields.
     *
     * @param {Event} e
     *
     * @author Alexandros Raikos <araikos@unipi.gr>
     */
    function registerUser(e) {
      // TODO @alexandrosraikos: Correct required fields prompting. (#129)
      e.preventDefault();
      makeWPRequest(
        "#policyms-registration button[type=submit]",
        "policyms_account_user_registration",
        AccountRegistrationProperties.nonce,
        new FormData($("#policyms-registration")[0]),
        (data) => {
          setAuthorizedToken(data);
          window.location.href = AccountRegistrationProperties.accountPage;
        }
      );
    }

    /**
     *
     * Generic interface actions & event listeners.
     *
     */

    // Add another social field.
    $("#policyms-registration .socials button.add-field").click(
      addSocialField
    );

    // Remove last social field.
    $(document).on(
      "click",
      "#policyms-registration .socials button.remove-field",
      removeSocialField
    );

    // Submit the registration.
    $("#policyms-registration").submit(registerUser);
  });
})(jQuery);
