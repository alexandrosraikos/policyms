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
     */
    function sendVerificationEmail(e) {
      e.preventDefault();
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
        complete: function (response) {
          try {
            var response_data = JSON.parse(response.responseText);
            if (response_data != null) {
              if (response_data.status === "failure") {
                alert(response_data.data);
                $("#policycloud-marketplace-account-edit .error").html(
                  response_data.data
                );
                $(
                  "#policycloud-marketplace-account-edit button[type=submit]"
                ).removeClass("disabled");
              } else if (response_data.status === "success") {
                alert(
                  "Successfully sent a verification email. If you still haven't received it, please check your spam inbox as well."
                );
              }
            }
            if (response.status != 200) {
              alert(
                "HTTP Error " + response.status + ": " + response.statusText
              );
              $(
                "#policycloud-marketplace-account-edit button[type=submit]"
              ).removeClass("disabled");
            }
          } catch (objError) {
            alert("Invalid response: " + response.responseText);
            $(
              "#policycloud-marketplace-account-edit button[type=submit]"
            ).removeClass("disabled");
          }
        },
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
            return (
              $(a).data("date-average-rating") <
              $(b).data("date-average-rating")
            );
          });
          break;
        case "rating-desc":
          items.sort((a, b) => {
            return (
              $(a).data("date-average-rating") >
              $(b).data("date-average-rating")
            );
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
     */
    function updateInformation(e) {
      e.preventDefault();
      var formData = new FormData(
        $("#policycloud-marketplace-account-edit")[0]
      );
      formData.append("action", "policycloud_marketplace_account_edit");
      formData.append("nonce", ajax_properties_account_editing.nonce);
      formData.append("username", ajax_properties_account_editing.user_id);

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
        complete: function (response) {
          try {
            var response_data = JSON.parse(response.responseText);
            if (response_data != null) {
              if (response_data.status === "failure") {
                $("#policycloud-marketplace-account-edit .error").html(
                  response_data.data
                );
                $("#policycloud-marketplace-account-edit .error").addClass(
                  "visible"
                );
              } else if (response_data.status === "success") {
                if (response_data.data != null) {
                  setAuthorizedToken(response_data.data);
                }
                window.location.reload();
              }
            }
            if (response.status != 200) {
              $("#policycloud-marketplace-account-edit .error").html(
                "HTTP Error " + response.status + ": " + response.statusText
              );
              $("#policycloud-marketplace-account-edit .error").addClass(
                "visible"
              );
            }
            $(
              "#policycloud-marketplace-account-edit button[type=submit]"
            ).removeClass("loading");
          } catch (objError) {
            $("#policycloud-marketplace-account-edit .error").html(
              "Invalid response: " + response.responseText
            );
            $("#policycloud-marketplace-account-edit .error").addClass(
              "visible"
            );
          }
        },
        dataType: "json",
      });
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

    // Submit the updated information.
    $("#policycloud-marketplace-account-edit").submit(updateInformation);

    // Request a verification email.
    $("button#policycloud-marketplace-resend-verification-email").click(
      sendVerificationEmail
    );

    // Store any newly verified encrypted token.
    if (ajax_properties_account_editing.verified_token) {
      setAuthorizedToken(ajax_properties_account_editing.verified_token);
      window.location.replace(location.pathname);
    }
  });
})(jQuery);
