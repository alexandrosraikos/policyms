/**
 * @file Provides interactivity to SSO buttons.
 *
 * @author Alexandros Raikos <alexandros@araikos.gr>
 */

var presetElementQueries = {
    googleButton: '#google-signin',
    keyCloakModalButton: 'button[data-action="show-keycloak-modal"]',
    keyCloakModalFormRegistration: 'form[data-action="policyms-keycloak-registration"]',
    keyCloakModalFormAuthentication: 'form[data-action="policyms-keycloak-authentication"]',
    googleDisconnectButton: 'button[data-action="policyms-disconnect-google"]',
    keyCloackDisconnectButton: 'button[data-action="policyms-disconnect-keycloak"]',
    egiDisconnectButton: 'button[data-action="policyms-disconnect-egi"]',
};

function googleRegistrationCallback(response) {
    makeWPRequest(
        presetElementQueries.googleButton,
        'policyms_account_user_registration_google',
        $(presetElementQueries.googleButton).data('nonce'), {
        google_token: response.credential
    },
        (data) => {
            setAuthorizedToken(data);
            window.location.href = $(presetElementQueries.googleButton).data('redirect');
        }
    )
}

function googleCallback(response) {
    makeWPRequest(
        presetElementQueries.googleButton,
        'policyms_account_user_authentication_google',
        $(presetElementQueries.googleButton).data('nonce'), {
        google_token: response.credential
    },
        (data) => {
            setAuthorizedToken(data);
            window.location.href = $(presetElementQueries.googleButton).data('redirect');
        }
    )
}


function disconnectGoogle(e) {
    e.preventDefault();
    if ($(presetElementQueries.googleDisconnectButton).attr("password-protected") === undefined) {
        if (confirm("You need to set a new password before disconnecting your Google account. Head to the \"Reset password\" page and follow the steps provided.")) {
            window.location.href = $(presetElementQueries.googleDisconnectButton).data('redirect');
        }
    } else {
        makeWPRequest(
            presetElementQueries.googleDisconnectButton,
            'policyms_account_disconnect_google',
            $(presetElementQueries.googleDisconnectButton).data('nonce'),
            {},
            (data) => {
                setAuthorizedToken(data);
                window.location.reload();
            }
        )
    }
}

function disconnectKeyCloak(e) {
    e.preventDefault();
    if ($(presetElementQueries.keyCloackDisconnectButton).attr("password-protected") === undefined) {
        if (confirm("You need to set a new password before disconnecting your KeyCloak account. Head to the \"Reset password\" page and follow the steps provided.")) {
            window.location.href = $(presetElementQueries.googleDisconnectButton).data('redirect');
        }
    } else {
        makeWPRequest(
            presetElementQueries.keyCloackDisconnectButton,
            'policyms_account_disconnect_keycloak',
            $(presetElementQueries.keyCloackDisconnectButton).data('nonce'),
            {},
            (data) => {
                setAuthorizedToken(data);
                window.location.reload();
            }
        )
    }
}

function disconnectEGI(e) {
    e.preventDefault();
    if ($(presetElementQueries.egiDisconnectButton).attr("password-protected") === undefined) {
        if (confirm("You need to set a new password before disconnecting your EGI credentials. Head to the \"Reset password\" page and follow the steps provided.")) {
            window.location.href = $(presetElementQueries.egiDisconnectButton).data('redirect');
        }
    } else {
        makeWPRequest(
            presetElementQueries.egiDisconnectButton,
            'policyms_account_disconnect_egi',
            $(presetElementQueries.egiDisconnectButton).data('nonce'),
            {},
            (data) => {
                setAuthorizedToken(data);
                window.location.reload();
            }
        )
    }
}

window.onload = function () {
    google.accounts.id.initialize({
        client_id: "129650564826-9bf7dhacn26c1hf1k0h0qcn48iv8mv8s.apps.googleusercontent.com",
        callback: window[$(presetElementQueries.googleButton).data('context')]
    });
    google.accounts.id.renderButton(
        document.querySelector(presetElementQueries.googleButton), {
        type: 'standard',
        shape: 'rectangular',
        theme: "filled_black",
        size: "large",
        locale: "en-GB"
    }
    );
    google.accounts.id.prompt();

    $('button[data-action="show-keycloak-modal"]').click((e) => {
        e.preventDefault();
        var registration = $(e.target).data('context') === 'registration';
        new Modal(
            'keycloak-form',
            `
          <div 
            class="policyms-keycloak" 
            style="padding:30px 20px 0 20px;">
            <form data-action="policyms-keycloak-${registration ? "registration" : "authentication"}">
              <fieldset name="keycloak-account-credentials">
              <h2>${registration ? "Sign up" : "Sign in"} with PolicyMS</h2>
              <p>You can ${registration ? "register" : "connect to"} your account using your PolicyMS KeyCloak credentials.</p>
                <label for="keycloak-username">KeyCloak Username *</label>
                <input required name="keycloak-username" placeholder="e.g. johndoe" type="text" />
                <label for="keycloak-password">KeyCloak Password *</label>
                <input required name="keycloak-password" placeholder="Insert your password" type="password" />
              </fieldset>
              <div class="actions">
                <button 
                    type="submit" 
                    class="action">
                    ${registration ? "Register" : "Log in"}
                </button>
              </div>
            </form>
            </div>
          `
        );
    });

    $(document).on(
        'submit',
        presetElementQueries.keyCloakModalFormAuthentication,
        (e) => {
            e.preventDefault();
            makeWPRequest(
                presetElementQueries.keyCloakModalFormAuthentication,
                'policyms_account_user_authentication_keycloak',
                $(presetElementQueries.keyCloakModalButton).data('nonce'),
                new FormData($("form.keycloak-sign-in")[0]),
                (data) => {
                    setAuthorizedToken(data);
                    window.location.href = $(presetElementQueries.keyCloakModalButton).data('redirect');
                }
            )
        }
    );

    $(document).on(
        'submit',
        presetElementQueries.keyCloakModalFormRegistration,
        (e) => {
            e.preventDefault();
            makeWPRequest(
                presetElementQueries.keyCloakModalFormRegistration,
                'policyms_account_user_registration_keycloak',
                $(presetElementQueries.keyCloakModalButton).data('nonce'),
                new FormData($("form.keycloak-registration")[0]),
                (data) => {
                    setAuthorizedToken(data);
                    window.location.href = $(presetElementQueries.keyCloakModalButton).data('redirect');
                }
            )
        }
    );

    const EGIProperties = {
        redirect: $('div[data-action="policyms-handle-egi-redirect"]').data('egi-redirect'),
        token: $('div[data-action="policyms-handle-egi-redirect"]').data('egi-token')
    }

    if (EGIProperties.redirect && EGIProperties.token) {
        setAuthorizedToken(EGIProperties.token);
        window.location.href = EGIProperties.redirect;
    }

}