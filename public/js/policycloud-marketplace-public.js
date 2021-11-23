/**
 * @file Provides global functions for PolicyCloud Marketplace shortcodes.
 *
 * @author Alexandros Raikos <araikos@unipi.gr>
 * @since 1.0.0
 */

var $ = jQuery;

// TODO @alexandrosraikos: Refactor all dependent JS by moving to `makeWPRequest` (#60).

/**
 * Global
 *
 * This section includes global functionality.
 *
 */

/**
 * The Modal class, which refers to the given modal in the DOM.
 *
 * For the referring structure:
 * @see policycloud-marketplace-public-display.php::show_modal()
 * @since 1.0.0
 */
class Modal {
  /**
   * The Modal class constructor.
   * @param {string} type The type of modal.
   * @param {any} data The modal's data. Will add controls if iterable.
   */
  constructor(type, data, index = 0) {
    // Initialize variables.
    this.type = type;
    this.data = data;
    this.index = index ?? 0;
    this.iterable = this.data.constructor === Array;

    /**
     * The modal HTML.
     */
    this.HTML = `<div id="policycloud-marketplace-modal" class="policycloud-marketplace ${
      this.type
    } hidden">
        <button class="close tactile"><span class="fas fa-times"></span></button>
        <div class="container">
        ${
          this.iterable
            ? `<button class="previous tactile" ${
                this.index - 1 < 0 ? "disabled" : ""
              }>
          <span class="fas fa-chevron-left"></span>
        </button>`
            : ``
        }
            <div class="content">
            </div>
            
        ${
          this.iterable
            ? `<button class="next tactile" ${
                this.index + 2 > this.data.length ? "disabled" : ""
              }>
          <span class="fas fa-chevron-right"></span>
        </button>`
            : ``
        }
        </div>
    </div>
    `;

    // Stop body scrolling.
    $("html, body").css({ overflow: "hidden" });

    // Append to document body.
    $("body").append(this.HTML);

    // Append data.
    this.set(this.iterable ? this.data[index] : this.data);

    // Show modal.
    $("#policycloud-marketplace-modal." + this.type).removeClass("hidden");

    /**
     * Listeners
     * ----------
     */

    // Iterate through content on button click.
    if (this.iterable) {
      // Set next on click.
      this.controls.next().on("click", () => {
        this.next();
      });
      // Set next on right arrow key press.
      $(document).on("keydown", (e) => {
        e.preventDefault();
        if (e.key === "ArrowRight") this.next();
      });

      // Set next.
      this.controls.previous().on("click", () => {
        this.previous();
      });
      // Set previous on left arrow key press.
      $(document).on("keydown", (e) => {
        e.preventDefault();
        if (e.key === "ArrowLeft") this.previous();
      });
    }

    // Dismiss modal on button click.
    $("#policycloud-marketplace-modal > .close").on("click", (e) => {
      e.preventDefault();
      this.hide();
    });

    // Dismiss modal on 'Escape' key press.
    $(document).on("keyup", (e) => {
      e.preventDefault();
      if (e.key === "Escape") this.hide();
    });
  }

  /**
   * References
   * ----------
   */

  /**
   * The modal's content reference.
   */
  content = () => {
    return $(
      "#policycloud-marketplace-modal." + this.type + " > .container > .content"
    );
  };

  /**
   * The modal's controls reference.
   */
  controls = {
    previous: () => {
      return $(
        "#policycloud-marketplace-modal." +
          this.type +
          " > .container > .previous"
      );
    },
    next: () => {
      return $(
        "#policycloud-marketplace-modal." + this.type + " > .container > .next"
      );
    },
  };

  /**
   * Methods
   * ----------
   */

  /**
   * Adds the `hidden` class to a given modal.
   * @param {Event} e The event.
   */
  hide() {
    $("#policycloud-marketplace-modal." + this.type).remove();
    $("html, body").css({ overflow: "auto" });
  }

  /**
   * Set the control buttons disabled status.
   *
   * @param {Boolean} previousState Whether there is a previous state.
   * @param {Boolean} nextState Whether there is a next state.
   */
  setControls(previousState, nextState) {
    this.controls.previous().prop("disabled", !previousState);
    this.controls.next().prop("disabled", !nextState);
  }

  /**
   * Clear previous content and append new.
   *
   * @param { HTMLElement } content
   */
  set(data) {
    this.content().empty();
    this.content().append(data);
  }

  next() {
    this.index = this.index + 1 > this.data.length - 1 ? 0 : this.index + 1;
    this.set(this.data[this.index]);
    this.setControls(
      this.controls.previous(),
      this.index + 1 <= this.data.length - 1
    );
  }

  previous() {
    this.index = this.index - 1 < 0 ? this.data.length - 1 : this.index - 1;
    this.set(this.data[this.index]);
    this.setControls(this.index - 1 >= 0, this.controls.next());
  }
}

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
  document.cookie =
    "ppmapi-token=" +
    encryptedToken +
    "; Path=" +
    GlobalProperties.rootURLPath +
    "; " +
    expires;
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
    "ppmapi-token=; Path=" +
    GlobalProperties.rootURLPath +
    "; Expires=Thu, 01 Jan 1970 00:00:01 GMT;";
  if (reload) window.location.reload();
  else window.location.href = GlobalProperties.rootURLPath;
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
function showAlert(
  selector,
  message,
  type = "error",
  placeBefore = false,
  disappearing = true
) {
  if (placeBefore) {
    $(selector).before(
      '<div class="policycloud-marketplace-' +
        type +
        ' animated"><span>' +
        message +
        "</span></div>"
    );
  } else {
    $(selector).after(
      '<div class="policycloud-marketplace-' +
        type +
        ' animated"><span>' +
        message +
        "</span></div>"
    );
  }
  if (disappearing) {
    setTimeout(() => {
      $(selector).next().addClass("seen");
    }, 2500);
    setTimeout(() => {
      $(selector).next().addClass("dismissed");
    }, 2700);
  }
}

/**
 * Refreshes with a new page query.
 *
 * @param {Event} e The click event
 *
 * @author Alexandros Raikos <araikos@unipi.gr>
 * @author Eleftheria Kouremenou <elkour@unipi.gr>
 */
function toggleFileList(e) {
  e.preventDefault();
  $(this)
    .closest(".policycloud-marketplace.file-viewer")
    .toggleClass("collapsed");
}

/**
 * Make a WP request.
 *
 * This function handles success data using the `completion` and appends errors automatically.
 *
 * @param {string} actionDOMSelector The selector of the DOM element triggering the action.
 * @param {string} action The action as registered in {@link ../../class-wc-biz-courier-logistics.php}
 * @param {string} nonce The single nonce appointed to the action.
 * @param {Object} data The array of data to be included in the request.
 * @param {Function} completion The actions to perform when the response was successful.
 *
 * @author Alexandros Raikos <alexandros@araikos.gr>
 * @since 1.4.0
 */
function makeWPRequest(actionDOMSelector, action, nonce, data, completion) {
  // Add the loading class.
  $(actionDOMSelector).addClass("loading");
  if (actionDOMSelector.includes("button")) {
    $(actionDOMSelector).prop("disabled", true);
  }

  // Prepare data fields for WordPress.
  data.action = action;
  data.nonce = nonce;

  // Perform AJAX request.
  $.ajax({
    url: GlobalProperties.ajaxURL,
    type: "post",
    data: data,
    dataType: "json",
    complete: (response) => {
      if (response.status === 200) {
        try {
          // Parse the data.
          var object = JSON.parse(
            response.responseText == ""
              ? '{"message":"completed"}'
              : response.responseText
          );

          // Execution completion callback.
          if (object.message === "completed") completion(object);
          else completion();

          // Remove the loading class.
          $(actionDOMSelector).removeClass("loading");
          if (actionDOMSelector.includes("button")) {
            $(actionDOMSelector).prop("disabled", false);
          }
        } catch (objError) {
          console.error("Invalid JSON response: " + objError);
        }
      } else if (response.status === 400 || response.status === 500) {
        showAlert(actionDOMSelector, response.responseText, "failure");

        // Remove the loading class.
        $(actionDOMSelector).removeClass("loading");
        if (actionDOMSelector.includes("button")) {
          $(actionDOMSelector).prop("disabled", false);
        }
      } else {
        showAlert(
          actionDOMSelector,
          "There was an unknown connection error, please try again later.",
          "failure"
        );

        // Log additional information into the console.
        console.error(
          "Policy Cloud Marketplace error: " + response.responseText
        );

        // Remove the loading class.
        $(actionDOMSelector).removeClass("loading");
        if (actionDOMSelector.includes("button")) {
          $(actionDOMSelector).prop("disabled", false);
        }
      }
    },
  });
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
          ? '{"message":"completed"}'
          : response.responseText
      );
      completedAction(data);
    } catch (objError) {
      console.error("Invalid JSON response: " + objError);
    }
  } else if (
    response.status === 400 ||
    response.status === 401 ||
    response.status === 404 ||
    response.status === 500
  ) {
    showAlert(actionSelector, response.responseText);
  } else {
    console.error(response.responseText);
  }

  // Remove the loading class.
  $(actionSelector).removeClass("loading");
  if (actionSelector.includes("button")) {
    $(actionSelector).prop("disabled", false);
  }
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
