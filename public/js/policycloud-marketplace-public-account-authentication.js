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
      new Modal(
        'keycloak-form',
        `
        <div class="policycloud-marketplace" style="padding:30px 20px 0 20px;">
          <form class="keycloak-sign-in">
            <fieldset name="keycloak-account-credentials">
            <h2>Sign in with PolicyCloud</h2>
            <p>You can connect to your account using your PolicyCloud KeyCloak credentials.</p>
              <label for="keycloak-username">KeyCloak Username *</label>
              <input required name="keycloak-username" placeholder="e.g. johndoe" type="text" />
              <label for="keycloak-password">KeyCloak Password *</label>
              <input required name="keycloak-password" placeholder="Insert your password" type="password" />
            </fieldset>
            <div class="actions">
                <button type="submit" class="action">Log in</button>
            </div>
          </form>
          </div>
        `
      )
    })

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
            window.location.href = GlobalProperties.rootURLPath;
          }
        )
      }
    );

    function onSuccess(googleUser) {
      console.log('Logged in as: ' + googleUser.getBasicProfile().getName());
    }
    function onFailure(error) {
      console.log(error);
    }
    function renderButton() {
      gapi.signin2.render('google-signin', {
        'scope': 'profile email',
        'width': 240,
        'height': 50,
        'longtitle': true,
        'theme': 'dark',
        'onsuccess': onSuccess,
        'onfailure': onFailure
      });
    }

    function googleCallback(code) {
      makeWPRequest(
        '.google-signin',
        'policycloud_marketplace_account_user_authentication_google',
        AccountAuthenticationProperties.GoogleSSONonce,
        {
          google_token: code
        },
        (data) => {
          setAuthorizedToken(data);
          window.location.href = GlobalProperties.rootURLPath;
        }
      )
    }


    function start() {
      gapi.load('auth2', function () {
        auth2 = gapi.auth2.init({
          client_id: '861485154625-4bdkkkbihuqbsf97k8uj831ivnlb9dp2.apps.googleusercontent.com',
        });
      });
    }

    start();
    renderButton();
    $('.google-signin').click(function () {
      // signInCallback defined in step 6.
      auth2.grantOfflineAccess().then(googleCallback);
    });
  });
})(jQuery);
