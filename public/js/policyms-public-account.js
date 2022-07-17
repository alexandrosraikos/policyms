/**
 * @file Provides dynamic fields and handles AJAX requests for forms and buttons
 * in the account shortcode.
 *
 * @author Alexandros Raikos <alexandros@araikos.gr>
 */

var presetElementQueries = {

};

(function ($) {
  "use strict";
  $(document).ready(function () {
    /**
     * Generic
     *
     * This section includes generic functionality
     * regarding the usage of the account shortcode.
     *
     */

    /**
     * Switch visible interface tab.
     *
     * @param {Event} e
     */
    function switchTab(e) {
      e.preventDefault();
      $("#policyms-account section").removeClass("focused");
      $("section." + $(this).attr("id")).addClass("focused");
      $("#policyms-account nav button").removeClass("active");
      $(this).addClass("active");
      var hashPrepare = $(this).attr("id").split("-");
      window.location.hash = "#" + hashPrepare[hashPrepare.length - 1];
    }

    /**
     *
     * Generic interface actions & event listeners.
     *
     */

    // Request a verification email.
    $("a#policyms-resend-verification-email").click(
      retryVerification
    );

    // Change account navigation tab.
    $(
      "button#policyms-account-overview, button#policyms-account-descriptions, button#policyms-account-reviews, button#policyms-account-approvals, button#policyms-account-profile"
    ).click(switchTab);

    // Hash determines active tab?
    if (
      window.location.hash == "#descriptions" ||
      window.location.hash == "#reviews" ||
      window.location.hash == "#approvals" ||
      window.location.hash == "#profile"
    ) {
      $(
        "button#policyms-account-" +
        window.location.hash.substr(1)
      ).trigger("click");
    } else {
      $("button#policyms-account-overview").trigger("click");
    }

    /**
     * Assets / Reviews / Approvals
     * ---------
     * This section contains all the functionality
     * related to the Assets, Reviews and Approvals tabs.
     *
     * Sorting, viewing and filtering takes place here.
     *
     */

    /**
     * Rearranges all the descriptions (as list items) into new lists,
     * based on the properties of the shortcode's Assets section.
     *
     * @param {String} category The category of list items to rearrange.
     * @param {Boolean} rememberPage Pass *true* to stay on the same page after
     * the rearrangement.
     * @param {Int} itemsPerPage Defaults to form value and can
     * be used to rearrange based on custom page size.
     * @param {Int} sortBy Defaults to form value and can be used
     * to rearrange based on a custom sorting rule: `newest`, `oldest`, `rating-asc`, `rating-desc`, `views-asc`, `views-desc` and `title`.
     *
     * @author Alexandros Raikos <alexandros@araikos.gr>
     */
    function rearrangeAssetsLists(
      category,
      rememberPage = false,
      itemsPerPage = $(
        ".policyms-account-" +
        category +
        " form.selector select[name=items-per-page]"
      ).val(),
      sortBy = $(
        ".policyms-account-" +
        category +
        " form.selector select[name=sort-by]"
      ).val()
    ) {
      // Get structure and clear DOM.
      var items = $("ul." + category + " li.visible");
      var hiddenItems = $("ul." + category + " li:not(.visible)");
      var activePage = rememberPage
        ? $("ul." + category + ".visible").data("page")
        : 1;
      $('.paginated-list[data-category="' + category + '"]').empty();

      // Sort by property.
      switch (sortBy) {
        case "newest":
          items.sort((a, b) => {
            return $(a).data("date-updated") < $(b).data("date-updated")
              ? 1
              : -1;
          });
          break;
        case "oldest":
          items.sort((a, b) => {
            return $(a).data("date-updated") > $(b).data("date-updated")
              ? 1
              : -1;
          });
          break;
        case "rating-asc":
          items.sort((a, b) => {
            return $(a).data("rating") < $(b).data("rating") ? 1 : -1;
          });
          break;
        case "rating-desc":
          items.sort((a, b) => {
            return $(a).data("rating") > $(b).data("rating") ? 1 : -1;
          });
          break;
        case "views-asc":
          items.sort((a, b) => {
            return $(a).data("total-views") < $(b).data("total-views") ? 1 : -1;
          });
          break;
        case "views-desc":
          items.sort((a, b) => {
            return $(a).data("total-views") > $(b).data("total-views") ? 1 : -1;
          });
          break;
        case "title":
          items.sort((a, b) => {
            return $(a).find("h4").html().localeCompare($(b).find("h4").html());
          });
          break;
      }

      // Add items and page selectors.
      var page = 1;
      for (let i = 0; i < items.length; i++) {
        if (i == 0 || i % itemsPerPage == 0) {
          // Add page.
          $('.paginated-list[data-category="' + category + '"]').prepend(
            '<ul data-page="' +
            page +
            '" class="page ' +
            category +
            " " +
            (page == activePage ? "visible" : "") +
            '"></ul>'
          );

          // Add pagination and buttons.
          if (i == 0) {
            $('.paginated-list[data-category="' + category + '"]').append(
              '<nav class="pagination"></nav>'
            );
          }
          $(
            '.paginated-list[data-category="' + category + '"] nav.pagination'
          ).append(
            '<button data-category="' +
            category +
            '" class="page-selector' +
            (page == activePage ? " active" : "") +
            '" data-' +
            category +
            '-page="' +
            page +
            '">' +
            page +
            "</button>"
          );
          page++;
        }
        // Add items to page.
        $("ul[data-page='" + (page - 1) + "']." + category).append(items[i]);
      }

      // Add hidden items list for future use.
      $('.paginated-list[data-category="' + category + '"]').append(
        '<ul class="' + category + ' hidden"></ul>'
      );
      $('.paginated-list[data-category="' + category + '"] ul.hidden').append(
        hiddenItems
      );
    }

    /**
     * Print the collection filter buttons by reading the
     * available collections in the description list.
     *
     * @param {String} category The tab in which the filters are being displayed
     * (currently only supports `description`, `review` and `approval`).
     * @param {[String]} collections
     *
     * @author Alexandros Raikos <alexandros@araikos.gr>
     */
    function calculateCollectionFilters(category, collections = null) {
      if (collections == null) {
        var collections = [];
        $.each(
          $('.paginated-list[data-category="' + category + 's"] li'),
          function () {
            if (!collections.includes($(this).data("type-filter")))
              collections.push($(this).data("type-filter"));
          }
        );
      }
      for (let i = 0; i < collections.length; i++) {
        $('.collection-filters[data-category="' + category + 's"]').append(
          '<button class="outlined" data-category="' +
          category +
          's" data-type-filter="' +
          collections[i] +
          '">' +
          collections[i] +
          "</button>"
        );
      }
    }

    /**
     * Manages button activation, filter application and subsquent rearrangement
     * of active description list items.
     *
     * @listens click
     *
     * @param {jQuery} button The filter button.
     * @param {String} category The category of the list.
     *
     * @author Alexandros Raikos <alexandros@araikos.gr>
     */
    function applyFilters(category, collection) {
      // Highlight active button.
      $(
        '#policyms-account-content .collection-filters[data-category="' +
        category +
        '"] button[data-type-filter="' +
        collection +
        '"]'
      ).toggleClass("active");

      // If at least one filter is active.
      if (
        $('.collection-filters[data-category="' + category + '"] button.active')
          .length > 0
      ) {
        // Remove "visible" class from every description.
        $('.paginated-list[data-category="' + category + '"] li').removeClass(
          "visible"
        );

        // Iterate only active filters.
        $(
          '.collection-filters[data-category="' + category + '"] button.active'
        ).each(
          /**
           * Add "visible" class to filter matching data type descriptions.
           */
          function () {
            $(
              '.paginated-list[data-category="' +
              category +
              '"] li[data-type-filter="' +
              $(this).data("type-filter") +
              '"]'
            ).addClass("visible");
          }
        );
      }
      // If no filter is active.
      else {
        // Add "visible" class to every description.
        $('.paginated-list[data-category="' + category + '"] li').addClass(
          "visible"
        );
      }
      rearrangeAssetsLists(category);
    }

    /**
     *
     * Move the event related description page into view.
     *
     * @param {Event} e
     *
     * @author Alexandros Raikos <alexandros@araikos.gr>
     */
    function changePage(e) {
      e.preventDefault();
      $(
        "#policyms-account-content nav.pagination button"
      ).removeClass("active");
      $(
        "#policyms-account-content section ul." +
        $(this).data("category")
      ).removeClass("visible");
      $(this).addClass("active");
      $(
        "#policyms-account-content section ul[data-page='" +
        $(this).data($(this).data("category") + "-page") +
        "']." +
        $(this).data("category")
      ).addClass("visible");
    }

    /**
     *
     * Assets / Reviews / Approvals interface actions & event listeners.
     *
     */

    // Initial print of the filtering buttons.
    calculateCollectionFilters("description");
    calculateCollectionFilters("review");
    calculateCollectionFilters("approval");

    // Select different sorting.
    $(
      "#policyms-account-content form.selector select[name=sort-by]"
    ).change(
      /**
       * Rearranges the list on sorting value change.
       *
       * @listens change
       * @param {Event} e
       *
       * @author Alexandros Raikos <alexandros@araikos.gr>
       */
      function (e) {
        e.preventDefault();
        rearrangeAssetsLists($(this).data("category"), true);
      }
    );

    // Select different page size.
    $(
      "#policyms-account-content form.selector select[name=items-per-page]"
    ).change(
      /**
       * Rearranges the list on page size value change.
       *
       * @listens change
       * @param {Event} e
       *
       * @author Alexandros Raikos <alexandros@araikos.gr>
       */
      function (e) {
        e.preventDefault();
        rearrangeAssetsLists($(this).data("category"));
      }
    );

    // Filter by collection.
    $(document).on(
      "click",
      "#policyms-account-content .collection-filters button",
      (e) => {
        e.preventDefault();
        applyFilters(
          $(e.target).data("category"),
          $(e.target).data("type-filter")
        );
      }
    );

    // Change page.
    $(document).on(
      "click",
      "#policyms-account-content nav.pagination button",
      changePage
    );

    /**
     * Information
     * ---------
     * This section contains all the functionality
     * related to the Information tab.
     *
     * Editing fields, email verification and
     *
     */

    /**
     * Toggle the "visible" class for all form fields and special divs.
     *
     * @param {Event} e
     *
     * @author Alexandros Raikos <alexandros@araikos.gr>
     */
    function toggleFormFields(e) {
      e.preventDefault();
      $(".folding").toggleClass("visible");
      if ($(this).html().includes("Edit")) {
        $(this).html('<span class="fas fa-times"></span> Cancel');
      } else {
        $(this).html('<span class="fas fa-pen"></span> Edit');
      }
      $("#policyms-account-edit .error").removeClass("visible");
      $("#policyms-account-edit .notice").removeClass("visible");
    }

    /**
     * Add another sibling field for weblinks.
     *
     * @param {Event} e
     *
     * @author Alexandros Raikos <alexandros@araikos.gr>
     */
    function addWeblinkField(e) {
      e.preventDefault();
      $(
        "<div><input type='text' name='socials-title[]' placeholder='Example' /><input type='url' name='socials-url[]' placeholder='https://www.example.org/' /><button class='remove-field' title='Remove this link.' ><span class='fas fa-times'></span></button></div>"
      ).appendTo("#policyms-account-edit .socials > div");
    }

    /**
     * Remove the weblink field.
     * @param {Event} e
     *
     * @author Alexandros Raikos <alexandros@araikos.gr>
     */
    function removeWeblinkField(e) {
      e.preventDefault();
      $(this).parent().remove();
    }

    /**
     * Display a current password requirement in the form.
     *
     * @param {Boolean} active Set to `true` if you want to display the prompt.
     *
     * @author Alexandros Raikos <alexandros@araikos.gr>
     */
    function setCurrentPasswordRequirement(enabled = false) {
      if (enabled) {
        $("#policyms-account-edit .critical-action").addClass(
          "visible"
        );
        $(
          "#policyms-account-edit input[name=current-password]"
        ).prop("required", true);
      } else {
        $("#policyms-account-edit .critical-action").removeClass(
          "visible"
        );
        $(
          "#policyms-account-edit input[name=current-password]"
        ).prop("required", false);
      }
    }

    /**
     * Prepare and submit via AJAX the edited information fields.
     *
     * @param {Event} e
     *
     * @author Alexandros Raikos <alexandros@araikos.gr>
     */
    function updateInformation(e) {
      e.preventDefault();

      // Prepare form data.
      var formData = new FormData(
        $("#policyms-account-edit")[0]
      );
      formData.append("uid", AccountEditingProperties.userID);
      formData.append("subsequent_action", "edit_account_user");

      makeWPRequest(
        "#policyms-account-edit button[type=submit]",
        "policyms_account_user_edit",
        AccountEditingProperties.nonce,
        formData,
        () => {
          window.location.reload();
        }
      );
    }

    /**
     * Submit via AJAX a profile picture deletion request.
     * Uses the same endpoint as account editing.
     *
     * @param {Event} e
     *
     * @author Alexandros Raikos <alexandros@araikos.gr>
     */
    function deleteProfilePicture(e) {
      e.preventDefault();

      // Add loading class to delete button.
      $(
        '.policyms .file-editor[data-name="profile-picture"] .delete'
      ).prop("disabled", true);

      // Prepare deletion form.
      var formData = new FormData();
      formData.append("uid", AccountEditingProperties.userID ?? "");
      formData.append("subsequent_action", "delete_profile_picture");

      makeWPRequest(
        '.policyms .file-editor[data-name="profile-picture"] .delete',
        "policyms_account_user_edit",
        AccountEditingProperties.nonce,
        formData,
        () => {
          window.location.reload();
        }
      );
    }

    /**
     * Request a new account verification email via AJAX.
     *
     * @param {Event} e
     *
     * @author Alexandros Raikos <alexandros@araikos.gr>
     */
    function retryVerification(e) {
      e.preventDefault();
      makeWPRequest(
        "button#policyms-resend-verification-email",
        "policyms_account_user_retry_verification",
        AccountEditingProperties.verificationRetryNonce,
        {
          uid: AccountEditingProperties.userID,
        },
        () => {
          showAlert(
            "button#policyms-resend-verification-email",
            "The verification email has been resent. Please check your spam folder as well.",
            "notice"
          );
        }
      );

      $("button#policyms-resend-verification-email").addClass(
        "loading"
      );
    }

    /**
     * Request a copy of the user's data via AJAX.
     *
     * @param {Event} e
     *
     * @author Alexandros Raikos <alexandros@araikos.gr>
     */
    function requestDataCopy(e) {
      e.preventDefault();
      makeWPRequest(
        "button#policyms-request-data-copy",
        "policyms_account_user_data_request",
        AccountEditingProperties.requestDataCopyNonce,
        {},
        (data) => {
          const blobData = JSON.stringify(data, null, 2);
          var blob = new Blob([blobData], {
            type: "text/plain",
          });
          var a = document.createElement("a");
          a.download = AccountEditingProperties.userID + "_account_data.txt";
          a.href = URL.createObjectURL(blob);
          a.dataset.downloadurl = ["text/plain", a.download, a.href].join(":");
          a.style.display = "none";
          a.setAttribute("id", "policyms-download-data-copy");
          $("section.policyms-account-profile").append(a);
          $("#policyms-download-data-copy").get(0).click();
        }
      );
    }

    /**
     * Show password prompt and verify the deletion request
     * before sending via AJAX.
     *
     * @param {Event} e
     *
     * @author Alexandros Raikos <alexandros@araikos.gr>
     */
    function validateDeletionRequest(e) {
      e.preventDefault();

      // Show password prompt.
      $("#policyms-delete-account > div").addClass("visible");
      $(
        "#policyms-delete-account input[name=current-password]"
      ).attr("required", true);

      // Perform the AJAX request for a present password value.
      if (
        $(
          "#policyms-delete-account input[name=current-password]"
        ).val() !== ""
      ) {
        makeWPRequest(
          "#policyms-delete-account button[type=submit]",
          "policyms_account_user_deletion",
          AccountEditingProperties.deletionNonce,
          {
            current_password: $(
              "#policyms-delete-account input[name=current-password]"
            ).val(),
            user: $('#policyms-delete-account button[type=submit]').attr('user') ?? ''
          },
          () => {
            if ($('#policyms-delete-account button[type=submit]').attr('user').length == 0) {
              removeAuthorization();
              window.location.href(GlobalProperties.rootURLPath);
            } else {
              window.location.reload();
            }
          }
        );
      }
    }

    /**
     *
     * Information interface actions & event listeners.
     *
     */

    // Edit information.
    $("#policyms-account-edit-toggle").click(toggleFormFields);

    // Delete profile picture.
    $(
      '.policyms #policyms-account-edit button[data-action="delete-picture"]'
    ).click(deleteProfilePicture);

    // Add a weblink field.
    $("#policyms-account-edit .socials button.add-field").click(
      addWeblinkField
    );

    // Remove a weblink field.
    $(document).on(
      "click",
      "#policyms-account-edit .socials button.remove-field",
      removeWeblinkField
    );

    // Verify a weblink field.
    $(document).on("change");

    // Show current password field on email editing.
    const initialEmailAddress = $(
      "#policyms-account-edit input[name=email]"
    ).val();
    $("#policyms-account-edit input[name=email]").on(
      "change paste keyup",
      function () {
        setCurrentPasswordRequirement($(this).val() !== initialEmailAddress);
      }
    );

    // Show current password field on password editing.
    $("#policyms-account-edit input[name=password]").on(
      "change paste keyup",
      function () {
        setCurrentPasswordRequirement($(this).val() !== "");
      }
    );

    // Request a verification email.
    $("button#policyms-resend-verification-email").click(
      retryVerification
    );

    // Submit the updated information.
    $("#policyms-account-edit").submit(updateInformation);

    // Request a copy of the account's data.
    $("button#policyms-request-data-copy").click(
      requestDataCopy
    );

    // Show current password field on deletion request.
    $("#policyms-delete-account").submit(
      validateDeletionRequest
    );

    $("button[data-action=\"disconnect-google\"]").click(
      disconnectGoogle
    )

    $("button[data-action=\"disconnect-keycloak\"]").click(
      disconnectKeyCloak
    )

    $("button[data-action=\"disconnect-egi\"]").click(
      disconnectEGI
    )
  });
})(jQuery);
