/**
 * @file Provides global functions for PolicyMS shortcodes.
 *
 * @author Alexandros Raikos <araikos@unipi.gr>
 * @since 1.0.0
 */

var $ = jQuery;

FontAwesomeConfig = { autoA11y: true }

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
 * @see policyms-public-display.php::show_modal()
 * @since 1.0.0
 */
class Modal {
  /**
   * The Modal class constructor.
   * @param {string} type The type of modal.
   * @param {any} data The modal's data. Will add controls if iterable.
   */
  constructor(type, data, index = 0, completion = null) {
    // Initialize variables.
    this.type = type;
    this.data = data;
    this.index = index ?? 0;
    this.iterable = this.data.constructor === Array;
    this.completion = completion;

    /**
     * The modal HTML.
     */
    this.HTML = `
    <div class="policyms modal ${this.type
      }" hidden>
        <div class="backdrop"></div>
        <button class="close tactile" data-action="close">
          <span class="fas fa-times"></span>
        </button>
        <div class="container">
          ${this.iterable
        ? `<button class="previous tactile" ${this.index - 1 < 0 ? "disabled" : ""
        }>
            <span class="fas fa-chevron-left"></span>
          </button>`
        : ``
      }
          <div class="content">
          </div>
            
        ${this.iterable
        ? `<button class="next tactile" ${this.index + 2 > this.data.length ? "disabled" : ""
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
    $(".policyms.modal." + this.type).removeClass("hidden");

    /**
     * Listeners
     * ----------
     */

    // Iterate through content on button click.
    if (this.iterable) {
      $(document).ready(() => {
        // Set next on click.
        this.controls.next().on("click", () => {
          this.next();
        });
        // Set next on right arrow key press.
        $(document).on("keydown", (e) => {
          if (e.key === "ArrowRight") this.next();
        });

        // Set next.
        this.controls.previous().on("click", () => {
          this.previous();
        });
        // Set previous on left arrow key press.
        $(document).on("keydown", (e) => {
          if (e.key === "ArrowLeft") this.previous();
        });
      });
    }

    // Dismiss modal on button click.
    $(".policyms.modal button[data-action=\"close\"]").on("click", (e) => {
      e.preventDefault();
      this.hide();
    });

    $(".policyms.modal .backdrop").on(
      "click",
      (e) => {
        e.preventDefault();
        this.hide();
      }
    )

    // Dismiss modal on 'Escape' key press.
    $(document).on("keydown", (e) => {
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
      ".policyms.modal." + this.type + " > .container > .content"
    );
  };

  /**
   * The modal's controls reference.
   */
  controls = {
    previous: () => {
      return $(
        ".policyms.modal." +
        this.type +
        " > .container > .previous"
      );
    },
    next: () => {
      return $(
        ".policyms.modal." + this.type + " > .container > .next"
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
    $(".policyms.modal." + this.type).remove();
    $("html, body").css({ overflow: "auto" });
  }

  /**
   * Destroy any modal.
   * @param {string} id The identifier of the modal
   */
  static kill(id) {
    $(".policyms.modal." + id).remove();
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
    if (this.completion) {
      this.completion(this.content().children()[0]);
    }
  }

  previous() {
    if (this.index - 1 >= 0) {
      this.index = this.index - 1 < 0 ? this.data.length - 1 : this.index - 1;
      this.set(this.data[this.index]);
      this.setControls(this.index - 1 >= 0, this.controls.next());
    }
  }

  next() {
    if (this.index + 2 <= this.data.length) {
      this.index = this.index + 1 > this.data.length - 1 ? 0 : this.index + 1;
      this.set(this.data[this.index]);
      this.setControls(
        this.controls.previous(),
        this.index + 1 <= this.data.length - 1
      );
    }
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
    "pcmapi-token=" +
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
function removeAuthorization(redirect_login = false) {
  document.cookie =
    "pcmapi-token=; Path=" +
    GlobalProperties.rootURLPath +
    "; Expires=Thu, 01 Jan 1970 00:00:01 GMT;";
  if (redirect_login) {
    window.location.href = GlobalProperties.loginPage;
  }
  else {
    window.location.href = GlobalProperties.rootURLPath;
  }
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
      '<div class="policyms-' +
      type +
      ' animated"><span>' +
      message +
      "</span></div>"
    );
  } else {
    $(selector).after(
      '<div class="policyms-' +
      type +
      ' animated"><span>' +
      message +
      "</span></div>"
    );
  }
  if (disappearing) {
    setTimeout(() => {
      $(selector).next().addClass("seen");
    }, 3500);
    setTimeout(() => {
      $(selector).next().addClass("dismissed");
    }, 3700);
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
    .closest(".policyms.file-viewer")
    .toggleClass("collapsed");
}

/**
 * Make a WP request.
 *
 * This function handles success data using the `completion` and appends errors automatically.
 *
 * @param {any} actionDOMSelector The selector of the DOM element triggering the action.
 * @param {string} action The action as registered in {@link ../../class-wc-biz-courier-logistics.php}
 * @param {string} nonce The single nonce appointed to the action.
 * @param {Object} data The array of data to be included in the request.
 * @param {Function} completion The actions to perform when the response was successful.
 *
 * @author Alexandros Raikos <alexandros@araikos.gr>
 * @since 1.4.0
 */
function makeWPRequest(actionDOMSelector, action, nonce, data, completion) {
  function completionHandler(response) {
    if (response.status === 200) {
      try {
        // Parse the data.
        if (response.responseText == '') {
          completion();
        }
        else {
          completion(response.responseJSON ?? JSON.parse(response.responseText));
        }
      } catch (objError) {
        completion();
      }
    } else if (response.status === 400 || response.status === 500) {
      showAlert(actionDOMSelector, response.responseText, "error");
    } else {
      showAlert(
        actionDOMSelector,
        "There was an unknown connection error, please try again later.",
        "error"
      );

      // Log additional information into the console.
      console.error("PolicyMS error: " + response.responseText);
    }

    // Remove the loading class.
    $(actionDOMSelector).removeClass("loading");
    $(actionDOMSelector).closest('form').removeClass("loading");
    if (typeof actionDOMSelector === 'string') {
      if (actionDOMSelector.includes("button")) {
        $(actionDOMSelector).prop("disabled", false);
      }
    }
  }

  // Add the loading class.
  $(actionDOMSelector).addClass("loading");
  $(actionDOMSelector).closest('form').addClass("loading");
  if (typeof actionDOMSelector === 'string') {
    if (actionDOMSelector.includes("button")) {
      $(actionDOMSelector).prop("disabled", true);
    }
  }

  if (data instanceof FormData) {
    data.append("action", action);
    data.append("nonce", nonce);

    // Perform AJAX request.
    $.ajax({
      url: GlobalProperties.ajaxURL,
      type: "post",
      data: data,
      contentType: false,
      processData: false,
      complete: completionHandler,
    });
  } else if (typeof data === "object") {
    // Prepare data fields for WordPress.
    data.action = action;
    data.nonce = nonce;

    // Perform AJAX request.
    $.ajax({
      url: GlobalProperties.ajaxURL,
      type: "post",
      data: data,
      dataType: "json",
      complete: completionHandler,
    });
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
    ".policyms-error.dismissable, .policyms-notice.dismissable"
  ).prepend(
    '<button class="policyms-alert-close">Dismiss</button>'
  );
  $(".policyms-alert-close").click(function (e) {
    $(this.parentNode).addClass("seen");
  });

  if ($('.policyms-error[logout]').length) {
    removeAuthorization(true);
  }

  // Search button listener.
  $(".policyms button[data-action=\"description-search\"]").click((e) => {
    e.preventDefault();
    new Modal(
      "description-search",
      `
      <div
        class="policyms menu-search">
        <form 
          method="get\" 
          action="`+ GlobalProperties.archivePage + `">
          <input type="text\" name="search" placeholder="Search descriptions..." />
          <button class="tactile" type="submit" title="Search">
              <span class="fas fa-search"></span>
          </button>
        </form>
      </div>
      `
    );
  });

  // User log out.
  $("a.policyms-logout, button.policyms-logout").click(
    removeAuthorization
  );
});
