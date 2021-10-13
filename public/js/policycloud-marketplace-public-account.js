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
     * Request a new account verification email via AJAX.
     *
     * @param {Event} e
     *
     * @author Alexandros Raikos <araikos@unipi.gr>
     */
    function sendVerificationEmail(e) {
      e.preventDefault();
      /**
       * Handle the response after requesting a new verification email.
       *
       * @param {Object} response
       *
       * @author Alexandros Raikos <araikos@unipi.gr>
       */
      function handleResponse(response) {
        try {
          var payload = JSON.parse(response.responseText);
          if (payload != null) {
            if (payload.status === "failure") {
              showAlert(
                "#policycloud-marketplace-account-edit button[type=submit]",
                payload.data
              );
            } else if (payload.status === "success") {
              showAlert(
                "#policycloud-marketplace-account-edit button[type=submit]",
                "Successfully sent a verification email. If you still haven't received it, please check your spam inbox as well.",
                "notice"
              );
            }
          }
          if (response.status != 200) {
            showAlert(
              "#policycloud-marketplace-account-edit button[type=submit]",
              "HTTP Error " + response.status + ": " + response.statusText
            );
          }
        } catch (objError) {
          showAlert(
            "#policycloud-marketplace-account-edit button[type=submit]",
            "Invalid response: " + response.responseText
          );
        }
        $(
          "#policycloud-marketplace-account-edit button[type=submit]"
        ).removeClass("disabled");
      }

      $(this).addClass("disabled");

      // Perform AJAX request.
      $.ajax({
        url: ajax_properties_account_editing.ajax_url,
        type: "post",
        data: {
          action: "policycloud_marketplace_user_email_verification_resend",
          nonce: ajax_properties_account_editing.nonce,
        },

        // Handle response.
        complete: handleResponse,
        dataType: "json",
      });
    }

    /**
     * Switch visible interface tab.
     *
     * @param {Event} e
     */
    function switchTab(e) {
      e.preventDefault();
      $(
        "section.policycloud-account-overview, section.policycloud-account-reviews, section.policycloud-account-assets, section.policycloud-account-information"
      ).removeClass("focused");
      $("section." + $(this).attr("id")).addClass("focused");

      $(
        "button#policycloud-account-overview, button#policycloud-account-assets, button#policycloud-account-reviews, button#policycloud-account-information"
      ).removeClass("active");
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
      "button#policycloud-account-overview, button#policycloud-account-assets, button#policycloud-account-reviews, button#policycloud-account-information"
    ).click(switchTab);

    // Hash determines active tab?
    if (
      window.location.hash == "#assets" ||
      window.location.hash == "#reviews" ||
      window.location.hash == "#information"
    ) {
      $("button#policycloud-account-" + window.location.hash.substr(1)).trigger(
        "click"
      );
    }

    /**
     * Assets
     * ---------
     * This section contains all the functionality
     * related to the Assets tab.
     *
     * Sorting, viewing and filtering takes place here.
     *
     */

    /**
     * Rearranges all the assets (as list items) into new lists,
     * based on the properties of the shortcode's Assets section.
     * @param {Boolean} rememberPage Pass *true* to stay on the same page after
     * the rearrangement.
     * @param {Int} itemsPerPage Defaults to form value and can
     * be used to rearrange based on custom page size.
     * @param {Int} sortBy Defaults to form value and can be used
     * to rearrange based on a custom sorting rule: `newest`, `oldest`, `rating-asc`, `rating-desc`, `views-asc`, `views-desc` and `title`.
     *
     * @author Alexandros Raikos <araikos@unipi.gr>
     */
    function rearrageAssetsLists(
      rememberPage = false,
      itemsPerPage = $(
        ".policycloud-account-assets form.selector select[name=items-per-page]"
      ).val(),
      sortBy = $(
        ".policycloud-account-assets form.selector select[name=sort-by]"
      ).val()
    ) {
      // Get structure and clear DOM.
      var items = $("#policycloud-account-assets-list ul li.visible");
      var hiddenItems = $(
        "#policycloud-account-assets-list ul li:not(.visible)"
      );
      var activePage = rememberPage
        ? $("#policycloud-account-assets-list ul.visible").data("page")
        : 1;

      $("#policycloud-account-assets-list").empty();

      // Sort by property.
      switch (sortBy) {
        case "newest":
          items.sort((a, b) => {
            return $(a).data("date-updated") > $(b).data("date-updated");
          });
          break;
        case "oldest":
          items.sort((a, b) => {
            return $(a).data("date-updated") < $(b).data("date-updated");
          });
          break;
        case "rating-asc":
          items.sort((a, b) => {
            return $(a).data("average-rating") < $(b).data("average-rating");
          });
          break;
        case "rating-desc":
          items.sort((a, b) => {
            return $(a).data("average-rating") > $(b).data("average-rating");
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
          $("#policycloud-account-assets-list").prepend(
            '<ul data-page="' +
              page +
              '" class="' +
              (page == activePage ? "visible" : "") +
              '"></ul>'
          );

          // Add pagination and buttons.
          if (i == 0) {
            $("#policycloud-account-assets-list").append(
              '<nav class="pagination"></nav>'
            );
          }
          $("#policycloud-account-assets-list nav.pagination").append(
            '<button class="page-selector' +
              (page == activePage ? " active" : "") +
              '" data-assets-page="' +
              page +
              '">' +
              page +
              "</button>"
          );
          page++;
        }
        // Add items to page.
        $(
          "#policycloud-account-assets-list ul[data-page='" + (page - 1) + "']"
        ).append(items[i]);
      }

      // Add hidden items list for future use.
      $("#policycloud-account-assets-list").append('<ul class="hidden"></ul>');
      $("#policycloud-account-assets-list ul.hidden").append(hiddenItems);
    }

    /**
     * Print the collection filter buttons by reading the
     * available collections in the asset list.
     *
     * @param {[String]} collections
     *
     * @author Alexandros Raikos <araikos@unipi.gr>
     */
    function calculateCollectionFilters(collections = null) {
      if (collections == null) {
        var collections = [];
        $.each($("#policycloud-account-assets-list ul li"), function () {
          if (!collections.includes($(this).data("type-filter")))
            collections.push($(this).data("type-filter"));
        });
      }
      for (let i = 0; i < collections.length; i++) {
        $("#policycloud-account-asset-collection-filters").append(
          '<button class="outlined" data-type-filter="' +
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
     * @param {Event} e
     *
     * @author Alexandros Raikos <araikos@unipi.gr>
     */
    function applyFilters(e) {
      e.preventDefault();

      // Highlight active button.
      $(this).toggleClass("active");

      // If at least one filter is active.
      if (
        $("#policycloud-account-asset-collection-filters button.active")
          .length > 0
      ) {
        // Remove "visible" class from every asset.
        $("#policycloud-account-assets-list li").removeClass("visible");

        // Iterate only active filters.
        $("#policycloud-account-asset-collection-filters button.active").each(
          /**
           * Add "visible" class to filter matching data type assets.
           */
          function () {
            $(
              "#policycloud-account-assets-list li[data-type-filter=" +
                $(this).data("type-filter") +
                "]"
            ).addClass("visible");
          }
        );
      }
      // If no filter is active.
      else {
        // Add "visible" class to every asset.
        $("#policycloud-account-assets-list li").addClass("visible");
      }
      rearrageAssetsLists();
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
      $(".policycloud-account-assets nav.pagination button").removeClass(
        "active"
      );
      $("#policycloud-account-assets-list > ul").removeClass("visible");
      $(this).addClass("active");
      $(
        "#policycloud-account-assets-list > ul[data-page='" +
          $(this).attr("data-assets-page") +
          "']"
      ).addClass("visible");
    }

    /**
     *
     * Assets interface actions & event listeners.
     *
     */

    // Initial print of the filtering buttons.
    calculateCollectionFilters();

    // Select different asset sorting.
    $(".policycloud-account-assets form.selector select[name=sort-by]").change(
      /**
       * Rearranges the asset list on sorting value change.
       *
       * @listens change
       * @param {Event} e
       *
       * @author Alexandros Raikos <araikos@unipi.gr>
       */
      function (e) {
        e.preventDefault();
        rearrageAssetsLists(true);
      }
    );

    // Select different page size.
    $(
      ".policycloud-account-assets form.selector select[name=items-per-page]"
    ).change(
      /**
       * Rearranges the asset list on page size value change.
       *
       * @listens change
       * @param {Event} e
       *
       * @author Alexandros Raikos <araikos@unipi.gr>
       */
      function (e) {
        e.preventDefault();
        rearrageAssetsLists();
      }
    );

    // Filter asset by collection.
    $(document).on(
      "click",
      "#policycloud-account-asset-collection-filters button",
      applyFilters
    );

    // Change page.
    $(document).on(
      "click",
      ".policycloud-account-assets nav.pagination button",
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
        "#policycloud-marketplace-account-edit .socials > div > div:last-of-type"
      )
        .clone()
        .appendTo("#policycloud-marketplace-account-edit .socials > div");
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

      /**
       * Handle the response after requesting an update of the account information.
       *
       * @param {Object} response The raw response AJAX object.
       *
       * @author Alexandros Raikos <araikos@unipi.gr>
       */
      function handleResponse(response) {
        if (response.status === 200) {
          try {
            var data = JSON.parse(response.responseText);
            setAuthorizedToken(data);
            window.location.reload();
          } catch (objError) {
            console.error("Invalid JSON response: " + objError);
          }
        } else if (
          response.status === 400 ||
          response.status === 404 ||
          response.status === 500
        ) {
          showAlert(
            "#policycloud-marketplace-request-data-copy",
            response.responseText
          );
        } else if (response.status === 440) {
          removeAuthorization(true);
        } else {
          console.error(response.responseText);
        }
      }

      var formData = new FormData(
        $("#policycloud-marketplace-account-edit")[0]
      );
      formData.append("action", "policycloud_marketplace_account_edit");
      formData.append("nonce", ajax_properties_account_editing.nonce);
      formData.append(
        "username",
        ajax_properties_account_editing.user_id ?? ""
      );
      $("#policycloud-marketplace-account-edit button[type=submit]").addClass(
        "loading"
      );
      // Perform AJAX request.
      $.ajax({
        url: ajax_properties_account_editing.ajax_url,
        type: "post",
        processData: false,
        contentType: false,
        data: formData,
        complete: handleResponse,
        dataType: "json",
      });

      $(
        "#policycloud-marketplace-account-edit button[type=submit]"
      ).removeClass("loading");
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
      /**
       * Handle the response after requesting a copy of the account data.
       *
       * @param {Object} response The raw response AJAX object.
       *
       * @author Alexandros Raikos <araikos@unipi.gr>
       */
      function handleResponse(response) {
        if (response.status === 200) {
          try {
            var data = JSON.parse(response.responseText);
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
            a.setAttribute("id", "policycloud-marketplace-download-data-copy");
            $("section.policycloud-account-information").append(a);
            $("#policycloud-marketplace-download-data-copy").get(0).click();
          } catch (objError) {
            console.error("Invalid JSON response: " + objError);
          }
        } else if (response.status === 404 || response.status === 500) {
          showAlert(
            "#policycloud-marketplace-request-data-copy",
            response.responseText
          );
        } else if (response.status === 440) {
          removeAuthorization();
        } else {
          console.error(response.responseText);
        }
      }

      $("#policycloud-marketplace-request-data-copy").addClass("loading");
      // Perform AJAX request.
      $.ajax({
        url: ajax_properties_account_editing.ajax_url,
        type: "post",
        data: {
          action: "policycloud_marketplace_account_data_request",
          nonce: ajax_properties_account_editing.nonce,
        },
        complete: handleResponse,
        dataType: "json",
      });
      $("#policycloud-marketplace-request-data-copy").removeClass("loading");
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

      /**
       * Handle the response after requesting account deletion.
       *
       * @param {Object} response The raw response AJAX object.
       *
       * @author Alexandros Raikos <araikos@unipi.gr>
       */
      function handleResponse(response) {
        try {
          var payload = JSON.parse(response.responseText);
          if (payload != null) {
            if (payload.status === "failure") {
              showAlert(
                "#policycloud-marketplace-delete-account",
                payload.data
              );
            } else if (payload.status === "success") {
              removeAuthorization();
              window.reload();
            }
          }
          if (response.status != 200) {
            showAlert(
              "#policycloud-marketplace-delete-account",
              "HTTP Error " + response.status + ": " + response.statusText
            );
          }
          $("#policycloud-marketplace-request-data-copy").removeClass(
            "loading"
          );
        } catch (objError) {
          showAlert(
            "#policycloud-marketplace-delete-account",
            "Invalid response: " + objError
          );
        }
      }

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
          complete: handleResponse,
          dataType: "json",
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

    // Show current password field on deletion request.
    $("#policycloud-marketplace-delete-account").submit(
      validateDeletionRequest
    );

    // Submit the updated information.
    $("#policycloud-marketplace-account-edit").submit(updateInformation);

    // Request a verification email.
    $("button#policycloud-marketplace-resend-verification-email").click(
      sendVerificationEmail
    );

    $("#policycloud-marketplace-request-data-copy").click(requestDataCopy);

    // Store any newly verified encrypted token.
    if (ajax_properties_account_editing.verified_token) {
      setAuthorizedToken(ajax_properties_account_editing.verified_token);
      window.location.replace(location.pathname);
    }
  });
})(jQuery);
