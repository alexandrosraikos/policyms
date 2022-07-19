/**
 * @file Provides dynamic handling of user authorization related forms.
 *
 * @author Alexandros Raikos <alexandros@araikos.gr>
 */

var presetElementQueries = {
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
        presetElementQueries.authenticationForm,
        "policyms_account_user_authentication",
        $(presetElementQueries.authenticationForm).data('nonce'),
        new FormData($(presetElementQueries.authenticationForm,)[0]),
        (data) => {
          setAuthorizedToken(data);
          window.location.href = $(presetElementQueries.authenticationForm).data('redirect');
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
        presetElementQueries.passwordResetForm,
        "policyms_account_user_password_reset",
        $(presetElementQueries.passwordResetForm).data('nonce'),
        new FormData($(presetElementQueries.passwordResetForm)[0]),
        () => {
          showAlert(
            presetElementQueries.passwordResetForm,
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


    $(presetElementQueries.authenticationForm).submit(authenticateUser);
    $(presetElementQueries.passwordResetForm).submit(resetPasswordRequest);

  });
})(jQuery);
