/**
 * @file Provides dynamic fields and handles AJAX requests for forms and buttons
 * in the account shortcode.
 *
 * @author Alexandros Raikos <araikos@unipi.gr>
 */

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
      $("#policycloud-account section").removeClass("focused");
      $("section." + $(this).attr("id")).addClass("focused");
      $("#policycloud-account nav button").removeClass("active");
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
    $("a#policycloud-marketplace-resend-verification-email").click(
      sendVerificationEmail
    );

    // Change account navigation tab.
    $(
      "button#policycloud-account-overview, button#policycloud-account-assets, button#policycloud-account-reviews, button#policycloud-account-approvals, button#policycloud-account-information"
    ).click(switchTab);

    // Hash determines active tab?
    if (
      window.location.hash == "#assets" ||
      window.location.hash == "#reviews" ||
      window.location.hash == "#approvals" ||
      window.location.hash == "#information"
    ) {
      $("button#policycloud-account-" + window.location.hash.substr(1)).trigger(
        "click"
      );
    } else {
      $("button#policycloud-account-overview").trigger("click");
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
     * Rearranges all the assets (as list items) into new lists,
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
     * @author Alexandros Raikos <araikos@unipi.gr>
     */
    function rearrangeAssetsLists(
      category,
      rememberPage = false,
      itemsPerPage = $(
        ".policycloud-account-" +
          category +
          " form.selector select[name=items-per-page]"
      ).val(),
      sortBy = $(
        ".policycloud-account-" +
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
            return $(a).data("date-updated") < $(b).data("date-updated");
          });
          break;
        case "oldest":
          items.sort((a, b) => {
            return $(a).data("date-updated") > $(b).data("date-updated");
          });
          break;
        case "rating-asc":
          items.sort((a, b) => {
            return $(a).data("rating") < $(b).data("rating");
          });
          break;
        case "rating-desc":
          items.sort((a, b) => {
            return $(a).data("rating") > $(b).data("rating");
          });
          break;
        case "views-asc":
          items.sort((a, b) => {
            return $(a).data("total-views") < $(b).data("total-views");
          });
          break;
        case "views-desc":
          items.sort((a, b) => {
            return $(a).data("total-views") > $(b).data("total-views");
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
     * available collections in the asset list.
     *
     * @param {String} category The tab in which the filters are being displayed
     * (currently only supports `asset`, `review` and `approval`).
     * @param {[String]} collections
     *
     * @author Alexandros Raikos <araikos@unipi.gr>
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
     * of active asset list items.
     *
     * @listens click
     *
     * @param {jQuery} button The filter button.
     * @param {String} category The category of the list.
     *
     * @author Alexandros Raikos <araikos@unipi.gr>
     */
    function applyFilters(category, collection) {
      // Highlight active button.
      $(
        '#policycloud-account-content .collection-filters[data-category="' +
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
        // Remove "visible" class from every asset.
        $('.paginated-list[data-category="' + category + '"] li').removeClass(
          "visible"
        );

        // Iterate only active filters.
        $(
          '.collection-filters[data-category="' + category + '"] button.active'
        ).each(
          /**
           * Add "visible" class to filter matching data type assets.
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
        // Add "visible" class to every asset.
        $('.paginated-list[data-category="' + category + '"] li').addClass(
          "visible"
        );
      }
      rearrangeAssetsLists(category);
    }

    /**
     *
     * Move the event related asset page into view.
     *
     * @param {Event} e
     *
     * @author Alexandros Raikos <araikos@unipi.gr>
     */
    function changePage(e) {
      e.preventDefault();
      $("#policycloud-account-content nav.pagination button").removeClass(
        "active"
      );
      $(
        "#policycloud-account-content section ul." + $(this).data("category")
      ).removeClass("visible");
      $(this).addClass("active");
      $(
        "#policycloud-account-content section ul[data-page='" +
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
    calculateCollectionFilters("asset");
    calculateCollectionFilters("review");
    calculateCollectionFilters("approval");

    // Select different sorting.
    $("#policycloud-account-content form.selector select[name=sort-by]").change(
      /**
       * Rearranges the list on sorting value change.
       *
       * @listens change
       * @param {Event} e
       *
       * @author Alexandros Raikos <araikos@unipi.gr>
       */
      function (e) {
        e.preventDefault();
        rearrangeAssetsLists($(this).data("category"), true);
      }
    );

    // Select different page size.
    $(
      "#policycloud-account-content form.selector select[name=items-per-page]"
    ).change(
      /**
       * Rearranges the list on page size value change.
       *
       * @listens change
       * @param {Event} e
       *
       * @author Alexandros Raikos <araikos@unipi.gr>
       */
      function (e) {
        e.preventDefault();
        rearrangeAssetsLists($(this).data("category"));
      }
    );

    // Filter by collection.
    $(document).on(
      "click",
      "#policycloud-account-content .collection-filters button",
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
      "#policycloud-account-content nav.pagination button",
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
     * @author Alexandros Raikos <araikos@unipi.gr>
     */
    function toggleFormFields(e) {
      e.preventDefault();
      $(".folding").toggleClass("visible");
      if ($(this).html().includes("Edit")) {
        $(this).html('<span class="fas fa-times"></span> Cancel');
      } else {
        $(this).html('<span class="fas fa-pen"></span> Edit');
      }
      $("#policycloud-marketplace-account-edit .error").removeClass("visible");
      $("#policycloud-marketplace-account-edit .notice").removeClass("visible");
    }

    /**
     * Add another sibling field for weblinks.
     *
     * @param {Event} e
     *
     * @author Alexandros Raikos <araikos@unipi.gr>
     */
    function addWeblinkField(e) {
      e.preventDefault();
      $(
        "<div><input type='text' name='socials-title[]' placeholder='Example' /><input type='url' name='socials-url[]' placeholder='https://www.example.org/' /><button class='remove-field' title='Remove this link.' ><span class='fas fa-times'></span></button></div>"
      ).appendTo("#policycloud-marketplace-account-edit .socials > div");
      $(
        "#policycloud-marketplace-account-edit .socials button.remove-field"
      ).prop(
        "disabled",
        $("#policycloud-marketplace-account-edit .socials button.remove-field")
          .length === 1
      );
    }

    /**
     * Remove the weblink field.
     * @param {Event} e
     *
     * @author Alexandros Raikos <araikos@unipi.gr>
     */
    function removeWeblinkField(e) {
      e.preventDefault();
      $(this).parent().remove();
      $(
        "#policycloud-marketplace-account-edit .socials button.remove-field"
      ).prop(
        "disabled",
        $("#policycloud-marketplace-account-edit .socials button.remove-field")
          .length === 1
      );
    }

    /**
     * Display a current password requirement in the form.
     *
     * @param {Boolean} active Set to `true` if you want to display the prompt.
     *
     * @author Alexandros Raikos <araikos@unipi.gr>
     */
    function setCurrentPasswordRequirement(enabled = false) {
      if (enabled) {
        $("#policycloud-marketplace-account-edit .critical-action").addClass(
          "visible"
        );
        $(
          "#policycloud-marketplace-account-edit input[name=current-password]"
        ).prop("required", true);
      } else {
        $("#policycloud-marketplace-account-edit .critical-action").removeClass(
          "visible"
        );
        $(
          "#policycloud-marketplace-account-edit input[name=current-password]"
        ).prop("required", false);
      }
    }

    /**
     * Prepare and submit via AJAX the edited information fields.
     *
     * @param {Event} e
     *
     * @author Alexandros Raikos <araikos@unipi.gr>
     */
    function updateInformation(e) {
      e.preventDefault();

      // Add loading class.
      $("#policycloud-marketplace-account-edit button[type=submit]").addClass(
        "loading"
      );

      // Prepare form data.
      var formData = new FormData(
        $("#policycloud-marketplace-account-edit")[0]
      );
      formData.append("action", "policycloud_marketplace_account_edit");
      formData.append("nonce", ajax_properties_account_editing.nonce);
      formData.append(
        "username",
        ajax_properties_account_editing.user_id ?? ""
      );
      formData.append("subsequent_action", "edit_account");

      // Perform AJAX request.
      $.ajax({
        url: ajax_properties_account_editing.ajax_url,
        type: "post",
        processData: false,
        contentType: false,
        data: formData,
        cache: false,
        dataType: "json",
        complete: (response) => {
          handleAJAXResponse(
            response,
            "#policycloud-marketplace-account-edit button[type=submit]",
            (data) => {
              if (data.hasOwnProperty("message")) {
                if (data.message != "completed") {
                  setAuthorizedToken(data);
                } else {
                  showAlert(data.message);
                }
              } else {
                setAuthorizedToken(data);
              }
              window.location.reload();
            }
          );
        },
      });
    }

    /**
     * Submit via AJAX a profile picture deletion request.
     * Uses the same endpoint as account editing.
     *
     * @param {Event} e
     *
     * @author Alexandros Raikos <araikos@unipi.gr>
     */
    function deleteProfilePicture(e) {
      e.preventDefault();

      // Add loading class to delete button.
      $(
        '.policycloud-marketplace .file-editor[data-name="profile-picture"] .delete'
      ).prop("disabled", true);

      // Prepare deletion form.
      var formData = new FormData();
      formData.append("action", "policycloud_marketplace_account_edit");
      formData.append("nonce", ajax_properties_account_editing.nonce);
      formData.append(
        "username",
        ajax_properties_account_editing.user_id ?? ""
      );
      formData.append("subsequent_action", "delete_profile_picture");

      // Perform AJAX request.
      $.ajax({
        url: ajax_properties_account_editing.ajax_url,
        type: "post",
        processData: false,
        contentType: false,
        data: formData,
        cache: false,
        dataType: "json",
        complete: (response) => {
          handleAJAXResponse(
            response,
            '.policycloud-marketplace .file-editor[data-name="profile-picture"] button.delete',
            (data) => {
              if (data.hasOwnProperty("message")) {
                if (data.message != "completed") {
                  setAuthorizedToken(data);
                }
              } else {
                setAuthorizedToken(data);
              }
              window.location.reload();
            }
          );
        },
      });
    }

    /**
     * Request a new account verification email via AJAX.
     *
     * @param {Event} e
     *
     * @author Alexandros Raikos <araikos@unipi.gr>
     */
    function sendVerificationEmail(e) {
      e.preventDefault();

      $("button#policycloud-marketplace-resend-verification-email").addClass(
        "loading"
      );

      // Perform AJAX request.
      $.ajax({
        url: ajax_properties_account_editing.ajax_url,
        type: "post",
        data: {
          action: "policycloud_marketplace_account_email_verification_resend",
          nonce: ajax_properties_account_editing.nonce,
        },
        dataType: "json",
        complete: (response) => {
          handleAJAXResponse(
            response,
            "button#policycloud-marketplace-resend-verification-email",
            (data) => {
              showAlert(
                "button#policycloud-marketplace-resend-verification-email",
                data,
                "notice"
              );
            }
          );
        },
      });
    }

    /**
     * Request a copy of the user's data via AJAX.
     *
     * @param {Event} e
     *
     * @author Alexandros Raikos <araikos@unipi.gr>
     */
    function requestDataCopy(e) {
      e.preventDefault();

      $("button#policycloud-marketplace-request-data-copy").addClass("loading");
      // Perform AJAX request.
      $.ajax({
        url: ajax_properties_account_editing.ajax_url,
        type: "post",
        data: {
          action: "policycloud_marketplace_account_data_request",
          nonce: ajax_properties_account_editing.nonce,
        },
        dataType: "json",
        complete: (response) => {
          handleAJAXResponse(
            response,
            "button#policycloud-marketplace-request-data-copy",
            (data) => {
              var blob = new Blob([JSON.stringify(data, null, 2)], {
                type: "text/plain",
              });
              var a = document.createElement("a");
              a.download = "account_data.txt";
              a.href = URL.createObjectURL(blob);
              a.dataset.downloadurl = ["text/plain", a.download, a.href].join(
                ":"
              );
              a.style.display = "none";
              a.setAttribute(
                "id",
                "policycloud-marketplace-download-data-copy"
              );
              $("section.policycloud-account-information").append(a);
              $("#policycloud-marketplace-download-data-copy").get(0).click();
            }
          );
        },
      });
    }

    /**
     * Show password prompt and verify the deletion request
     * before sending via AJAX.
     *
     * @param {Event} e
     *
     * @author Alexandros Raikos <araikos@unipi.gr>
     */
    function validateDeletionRequest(e) {
      e.preventDefault();

      // Show password prompt.
      $("#policycloud-marketplace-delete-account > div").addClass("visible");
      $(
        "#policycloud-marketplace-delete-account input[name=current-password]"
      ).attr("required", true);

      // Perform the AJAX request for a present password value.
      if (
        $(
          "#policycloud-marketplace-delete-account input[name=current-password]"
        ).val() !== ""
      ) {
        // Add loading class
        $(
          "#policycloud-marketplace-delete-account button[type=submit]"
        ).addClass("loading");

        $.ajax({
          url: ajax_properties_account_editing.ajax_url,
          type: "post",
          data: {
            action: "policycloud_marketplace_account_deletion",
            nonce: ajax_properties_account_editing.nonce,
            current_password: $(
              "#policycloud-marketplace-delete-account input[name=current-password]"
            ).val(),
          },
          dataType: "json",
          complete: (response) => {
            handleAJAXResponse(
              response,
              "#policycloud-marketplace-delete-account button[type=submit]",
              () => {
                removeAuthorization();
                window.reload();
              }
            );
          },
        });
      }
    }

    /**
     *
     * Information interface actions & event listeners.
     *
     */

    // Edit information.
    $("#policycloud-marketplace-account-edit-toggle").click(toggleFormFields);

    // Delete profile picture.
    $(
      '.policycloud-marketplace .file-editor[data-name="profile-picture"] .delete'
    ).click(deleteProfilePicture);

    // Add a weblink field.
    $("#policycloud-marketplace-account-edit .socials button.add-field").click(
      addWeblinkField
    );

    // Remove a weblink field.
    $(document).on(
      "click",
      "#policycloud-marketplace-account-edit .socials button.remove-field",
      removeWeblinkField
    );

    // Show current password field on email editing.
    const initialEmailAddress = $(
      "#policycloud-marketplace-account-edit input[name=email]"
    ).val();
    $("#policycloud-marketplace-account-edit input[name=email]").on(
      "change paste keyup",
      function () {
        setCurrentPasswordRequirement($(this).val() !== initialEmailAddress);
      }
    );

    // Show current password field on password editing.
    $("#policycloud-marketplace-account-edit input[name=password]").on(
      "change paste keyup",
      function () {
        setCurrentPasswordRequirement($(this).val() !== "");
      }
    );

    // Request a verification email.
    $("button#policycloud-marketplace-resend-verification-email").click(
      sendVerificationEmail
    );

    // Submit the updated information.
    $("#policycloud-marketplace-account-edit button[type=submit]").click(
      updateInformation
    );

    // Request a copy of the account's data.
    $("button#policycloud-marketplace-request-data-copy").click(
      requestDataCopy
    );

    // Show current password field on deletion request.
    $("#policycloud-marketplace-delete-account").submit(
      validateDeletionRequest
    );

    // Store any newly verified encrypted token.
    if (ajax_properties_account_editing.verified_token) {
      setAuthorizedToken(ajax_properties_account_editing.verified_token);
      window.location.replace(location.pathname);
    }
  });
})(jQuery);
