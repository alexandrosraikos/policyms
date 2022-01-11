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
    function authenticateUser(e) {
      e.preventDefault();

      makeWPRequest(
        "#policycloud-authentication button[type=submit]",
        "policycloud_marketplace_account_user_authentication",
        AccountAuthenticationProperties.nonce,
        new FormData($("#policycloud-authentication")[0]),
        (data) => {
          setAuthorizedToken(data);
          window.location.href = GlobalProperties.rootURLPath;
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
        "#policycloud-marketplace-password-reset button[type=submit]",
        "policycloud_marketplace_account_user_password_reset",
        AccountAuthenticationProperties.nonce,
        new FormData($("#policycloud-marketplace-password-reset")[0]),
        () => {
          showAlert(
            "#policycloud-marketplace-password-reset button[type=submit]",
            "An email has been sent with instructions to reset your password.",
            "notice"
          );
        }
      );
    }

    // TODO @alexandrosraikos: Add Google SSO pop up integration. (#114)
    // TODO @alexandrosraikos: Add AJAX request to forward Google bearer token and log user in on token response. (#114)

    /**
     *
     * Generic interface actions & event listeners.
     *
     */

    $("#policycloud-authentication").submit(authenticateUser);
    $("#policycloud-marketplace-password-reset").submit(resetPasswordRequest);
  });
})(jQuery);
