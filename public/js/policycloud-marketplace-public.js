// TODO @alexandrosraikos: Clean up, comment and claim.

/**
 * Store the encrypted token as a cookie
 * into the user's browser for 15 days.
 *
 * @param {string} encryptedToken
 */
function setAuthorizedToken(encryptedToken) {
  let date = new Date();
  date.setTime(date.getTime() + 15 * 24 * 60 * 60 * 1000);
  const expires = "expires=" + date.toUTCString();
  document.cookie = "ppmapi-token=" + encryptedToken + "; " + expires;
}

(function ($) {
  "use strict";
  $(document).ready(() => {
    // Dismiss error dialogues and notices.
    $(
      ".policycloud-marketplace-error.dismissable, .policycloud-marketplace-notice.dismissable"
    ).prepend(
      '<button class="policycloud-marketplace-alert-close">Dismiss</button>'
    );
    $(".policycloud-marketplace-alert-close").click(function (e) {
      $(this.parentNode).addClass("seen");
    });

    // User log out.
    $("a.policycloud-logout, button.policycloud-logout").click((e) => {
      document.cookie =
        "ppmapi-token=; Path=/; Expires=Thu, 01 Jan 1970 00:00:01 GMT;";
      window.location.href = "/";
    });
  });
})(jQuery);
