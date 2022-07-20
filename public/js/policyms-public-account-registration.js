/**
 * @file Provides dynamic fields and handles AJAX requests for forms and buttons
 * in the account registration shortcode.
 *
 * @author Alexandros Raikos <alexandros@araikos.gr>
 */

var presetElementQueries = {
  registrationForm: 'form[data-action="policyms-user-registration"]',
  addSocialFieldButton: 'form[data-action="policyms-user-registration"] > fieldset[name="account-details"] > .socials > button[data-action="add-field"]',
  removeSocialFieldButton: 'form[data-action="policyms-user-registration"] > fieldset[name="account-details"] > .socials button[data-action="remove-field"]'
};

/**
 * Add a double input field to the socials container.
 *
 * @param {Event} e
 *
 * @author Alexandros Raikos <alexandros@araikos.gr>
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
 * @author Alexandros Raikos <alexandros@araikos.gr>
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
 * @author Alexandros Raikos <alexandros@araikos.gr>
 */
function registerUser(e) {
  // TODO @alexandrosraikos: Correct required fields prompting. (#129)
  e.preventDefault();
  makeWPRequest(
    presetElementQueries.registrationForm,
    "policyms_account_user_registration",
    $(presetElementQueries.registrationForm).data('nonce'),
    new FormData($(presetElementQueries.registrationForm)[0]),
    (data) => {
      setAuthorizedToken(data);
      window.location.href = $(presetElementQueries.registrationForm).data('redirect');
    }
  );
}

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
     * Generic interface actions & event listeners.
     *
     */

    // Submit the registration.
    $(document).on(
      "submit",
      presetElementQueries.registrationForm,
      registerUser
    );
  });
})(jQuery);
