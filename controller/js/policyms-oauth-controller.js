function googleRegistrationCallback(response) {
    makeWPRequest(
        '#google-signin',
        'policyms_account_user_registration_google',
        OAuthProperties.googleActionNonce, {
        google_token: response.credential
    },
        (data) => {
            setAuthorizedToken(data);
            window.location.href = OAuthProperties.rootURLPath;
        }
    )
}

function googleCallback(response) {
    makeWPRequest(
        '#google-signin',
        'policyms_account_user_authentication_google',
        OAuthProperties.googleActionNonce, {
        google_token: response.credential
    },
        (data) => {
            setAuthorizedToken(data);
            window.location.href = OAuthProperties.rootURLPath;
        }
    )
}

window.onload = function () {
    google.accounts.id.initialize({
        client_id: "129650564826-9bf7dhacn26c1hf1k0h0qcn48iv8mv8s.apps.googleusercontent.com",
        callback: window[OAuthProperties.callbackID]
    });
    google.accounts.id.renderButton(
        document.getElementById("google-signin"), {
        type: 'standard',
        shape: 'rectangular',
        theme: "filled_black",
        size: "large",
        locale: "en-GB"
    }
    );
    google.accounts.id.prompt();
}