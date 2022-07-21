/**
 * @file Provides dynamic handling of user authorization related forms.
 *
 * @author Alexandros Raikos <alexandros@araikos.gr>
 */

var authenticationElements = {
  authenticationForm: 'form[data-action="policyms-user-authentication"]',
  passwordResetForm: 'form[data-action="policyms-user-password-reset"]',
};

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
    function authenticateUser(e) {
      e.preventDefault();

      makeWPRequest(
        authenticationElements.authenticationForm,
        "policyms_account_user_authentication",
        $(authenticationElements.authenticationForm).data('nonce'),
        new FormData($(authenticationElements.authenticationForm,)[0]),
        (data) => {
          setAuthorizedToken(data);
          window.location.href = $(authenticationElements.authenticationForm).data('redirect');
        }
      );
    }

    /**
     *
     * @param {Event} e
     */
    function resetPasswordRequest(e) {
      e.preventDefault();

      makeWPRequest(
        authenticationElements.passwordResetForm,
        "policyms_account_user_password_reset",
        $(authenticationElements.passwordResetForm).data('nonce'),
        new FormData($(authenticationElements.passwordResetForm)[0]),
        () => {
          showAlert(
            authenticationElements.passwordResetForm,
            "An email has been sent with instructions to reset your password.",
            "notice"
          );
        }
      );
    }

    /**
     *
     * Generic interface actions & event listeners.
     *
     */


    $(authenticationElements.authenticationForm).submit(authenticateUser);
    $(authenticationElements.passwordResetForm).submit(resetPasswordRequest);

  });
})(jQuery);
