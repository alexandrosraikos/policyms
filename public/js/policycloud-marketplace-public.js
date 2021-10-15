/**
 * @file Provides global functions for PolicyCloud Marketplace shortcodes.
 *
 * @author Alexandros Raikos <araikos@unipi.gr>
 */

var $ = jQuery;

/**
 * Global
 *
 * This section includes global functionality.
 *
 */

/**
 * Store the encrypted token as a cookie
 * into the user's browser for 15 days.
 *
 * @param {string} encryptedToken
 *
 * @author Alexandros Raikos <araikos@unipi.gr>
 */
function setAuthorizedToken(encryptedToken) {
  let date = new Date();
  date.setTime(date.getTime() + 15 * 24 * 60 * 60 * 1000);
  const expires = "expires=" + date.toUTCString();
  document.cookie = "ppmapi-token=" + encryptedToken + "; Path=/; " + expires;
}

/**
 * Removes the encrypted token from the browser
 * cookie storage (aka log out).
 *
 * @param {Boolean} reload Choose `true` if you want to reload into the same page.
 *
 * @author Alexandros Raikos <araikos@unipi.gr>
 */
function removeAuthorization(reload = false) {
  document.cookie =
    "ppmapi-token=; Path=/; Expires=Thu, 01 Jan 1970 00:00:01 GMT;";
  if (reload) window.location.reload();
  else window.location.href = "/";
}

/**
 *
 * Display an alert container relative to the referenced element.
 *
 * @param {string} selector The DOM selector of the element that will be alerted about.
 * @param {string} message The message that will be displayed in the alert.
 * @param {string} type The type of alert (either an `'error'` or `'notice'`)/
 * @param {Boolean} placeBefore Whether the alert is placed before the selected element.
 *
 * @author Alexandros Raikos <araikos@unipi.gr>
 */
function showAlert(selector, message, type = "error", placeBefore = false) {
  if (placeBefore) {
    $(selector).before(
      '<div class="policycloud-marketplace-' +
        type +
        '"><span>' +
        message +
        "</span></div>"
    );
  } else {
    $(selector).after(
      '<div class="policycloud-marketplace-' +
        type +
        '"><span>' +
        message +
        "</span></div>"
    );
  }
}

/**
 * Handle the response after requesting via WP ajax.
 *
 * @param {Object} response The raw response AJAX object.
 * @param {string} actionSelector The selector of the DOM element triggering the action.
 * @param {completedAction} callback The actions to perform when the response was successful.
 *
 * @author Alexandros Raikos <araikos@unipi.gr>
 */
function handleAJAXResponse(response, actionSelector, completedAction) {
  if (response.status === 200) {
    try {
      var data = JSON.parse(
        response.responseText == ""
          ? '{"string":"completed"}'
          : response.responseText
      );
      completedAction(data);
    } catch (objError) {
      console.error("Invalid JSON response: " + objError);
    }
  } else if (
    response.status === 400 ||
    response.status === 404 ||
    response.status === 500
  ) {
    showAlert(actionSelector, response.responseText);
  } else if (response.status === 440) {
    removeAuthorization(true);
  } else {
    console.error(response.responseText);
  }

  // Remove the loading class.
  $(actionSelector).removeClass("loading");
}

/**
 *
 * Global interface actions & event listeners.
 *
 */

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
  $("a.policycloud-logout, button.policycloud-logout").click(
    removeAuthorization
  );
});
