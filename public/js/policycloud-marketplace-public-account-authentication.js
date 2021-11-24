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
        "#policycloud-authorization button[type=submit]",
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
     * Generic interface actions & event listeners.
     *
     */

    $("#policycloud-authentication").submit(authenticateUser);
  });
})(jQuery);
