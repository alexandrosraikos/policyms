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

    /**
     *
     * Generic interface actions & event listeners.
     *
     */


    $("#policycloud-authentication").submit(authenticateUser);
    $("#policycloud-marketplace-password-reset").submit(resetPasswordRequest);

    $("button[data-action=\"keycloak-form\"]").click((e) => {
      e.preventDefault();
      var registration = $(e.target).attr('id') === 'keycloak-registration';
      new Modal(
        'keycloak-form',
        `
        <div class="policycloud-marketplace" style="padding:30px 20px 0 20px;">
          <form class="keycloak-${registration ? 'registration' : 'sign-in'}">
            <fieldset name="keycloak-account-credentials">
            <h2>${registration ? "Sign up" : "Sign in"} with Policy Cloud</h2>
            <p>You can ${registration ? "register" : "connect to"} your account using your Policy Cloud KeyCloak credentials.</p>
              <label for="keycloak-username">KeyCloak Username *</label>
              <input required name="keycloak-username" placeholder="e.g. johndoe" type="text" />
              <label for="keycloak-password">KeyCloak Password *</label>
              <input required name="keycloak-password" placeholder="Insert your password" type="password" />
            </fieldset>
            <div class="actions">
                <button type="submit" class="action">${registration ? "Register" : "Log in"}</button>
            </div>
          </form>
          </div>
        `
      );
    });

    $(document).on(
      'submit',
      'form.keycloak-sign-in',
      (e) => {
        e.preventDefault();
        makeWPRequest(
          'form.keycloak-sign-in button[type="submit"]',
          'policycloud_marketplace_account_user_authentication_keycloak',
          AccountAuthenticationProperties.KeyCloakSSONonce,
          new FormData($("form.keycloak-sign-in")[0]),
          (data) => {
            setAuthorizedToken(data);
            if (AccountAuthenticationProperties.RedirectSSO !== undefined) {
              window.location.reload();
            }
            else {
              window.location.href = GlobalProperties.rootURLPath;
            }
          }
        )
      }
    );

    $(document).on(
      'submit',
      'form.keycloak-registration',
      (e) => {
        e.preventDefault();
        makeWPRequest(
          'form.keycloak-registration button[type="submit"]',
          'policycloud_marketplace_account_user_registration_keycloak',
          AccountAuthenticationProperties.KeyCloakSSORegistrationNonce,
          new FormData($("form.keycloak-registration")[0]),
          (data) => {
            setAuthorizedToken(data);
            window.location.href = GlobalProperties.rootURLPath;
          }
        )
      }
    );

    if (AccountAuthenticationProperties.EGISuccessRedirect) {
      setAuthorizedToken(AccountAuthenticationProperties.EGISuccessToken);
      window.location.href = AccountAuthenticationProperties.EGISuccessRedirect;
    }

  });
})(jQuery);
