(function ($) {
  "use strict";
  $(document).ready(function () {
    // Navigation
    $(
      "button#policycloud-account-overview, button#policycloud-account-assets, button#policycloud-account-reviews, button#policycloud-account-information"
    ).click(function (e) {
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
    });

    // Check for existing hash
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
     */

    // - Asset sorting.
    function rearrageAssetsLists(rememberPage = false) {
      // Get structure and clear DOM.
      var items = $("#policycloud-account-assets-list ul li.visible");
      var hiddenItems = $(
        "#policycloud-account-assets-list ul li:not(.visible)"
      );
      var itemsPerPage = $(
        ".policycloud-account-assets form.selector select[name=items-per-page]"
      ).val();
      var sortBy = $(
        ".policycloud-account-assets form.selector select[name=sort-by]"
      ).val();
      var activePage = rememberPage
        ? $("#policycloud-account-assets-list ul.visible").data("page")
        : 1;

      $("#policycloud-account-assets-list").empty();

      // Sort.
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

    // Sorting by attribute.
    $(".policycloud-account-assets form.selector select[name=sort-by]").change(
      function (e) {
        e.preventDefault();
        rearrageAssetsLists(true);
      }
    );

    // Regrouping by page size.
    $(
      ".policycloud-account-assets form.selector select[name=items-per-page]"
    ).change(function (e) {
      e.preventDefault();
      rearrageAssetsLists();
    });

    // - Asset collection filters.
    // Print buttons.
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
    calculateCollectionFilters();

    $(document).on(
      "click",
      "#policycloud-account-asset-collection-filters button",
      function (e) {
        e.preventDefault();

        // Highlight filter button.
        $(this).toggleClass("active");

        if (
          $("#policycloud-account-asset-collection-filters button.active")
            .length > 0
        ) {
          $("#policycloud-account-assets-list li").removeClass("visible");

          // Show all filtered items.
          var buttons = $(
            "#policycloud-account-asset-collection-filters button"
          );
          buttons.each(function (i, v) {
            if ($(v).hasClass("active")) {
              $(
                "#policycloud-account-assets-list li[data-type-filter=" +
                  $(v).data("type-filter") +
                  "]"
              ).addClass("visible");
            }
          });
        } else {
          // Show all if no filters.
          $("#policycloud-account-assets-list li").addClass("visible");
        }
        rearrageAssetsLists();
      }
    );

    // - Assets pagination.
    $(document).on(
      "click",
      ".policycloud-account-assets nav.pagination button",
      function (e) {
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
    );

    // Profile editing.
    $("#policycloud-marketplace-account-edit-toggle").click(function (e) {
      e.preventDefault();
      $(".folding").toggleClass("visible");
      if ($(this).html().includes("Edit")) {
        $(this).html('<span class="fas fa-times"></span> Cancel');
      } else {
        $(this).html('<span class="fas fa-pen"></span> Edit');
      }
      $("#policycloud-marketplace-account-edit .error").removeClass("visible");
      $("#policycloud-marketplace-account-edit .notice").removeClass("visible");
    });

    // -- Dynamic socials fields
    $("#policycloud-marketplace-account-edit .socials button.add-field").click(
      function (e) {
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
          $(
            "#policycloud-marketplace-account-edit .socials button.remove-field"
          ).length === 1
        );
      }
    );
    $(document).on(
      "click",
      "#policycloud-marketplace-account-edit .socials button.remove-field",
      function (e) {
        e.preventDefault();
        $(this).parent().remove();
        $(
          "#policycloud-marketplace-account-edit .socials button.remove-field"
        ).prop(
          "disabled",
          $(
            "#policycloud-marketplace-account-edit .socials button.remove-field"
          ).length === 1
        );
      }
    );

    // Critical information editing (email, password).
    var initialEmailAddress = $(
      "#policycloud-marketplace-account-edit input[name=email]"
    ).val();
    $("#policycloud-marketplace-account-edit input[name=email]").on(
      "change paste keyup",
      function (e) {
        if ($(this).val() !== initialEmailAddress) {
          $("#policycloud-marketplace-account-edit .critical-action").addClass(
            "visible"
          );
          $(
            "#policycloud-marketplace-account-edit input[name=current-password]"
          ).prop("required", true);
        } else {
          $(
            "#policycloud-marketplace-account-edit .critical-action"
          ).removeClass("visible");
          $(
            "#policycloud-marketplace-account-edit input[name=current-password]"
          ).prop("required", false);
        }
      }
    );
    $("#policycloud-marketplace-account-edit input[name=password]").on(
      "change paste keyup",
      function (e) {
        if ($(this).val() !== "") {
          $("#policycloud-marketplace-account-edit .critical-action").addClass(
            "visible"
          );
          $(
            "#policycloud-marketplace-account-edit input[name=current-password]"
          ).prop("required", true);
        } else {
          $(
            "#policycloud-marketplace-account-edit .critical-action"
          ).removeClass("visible");
          $(
            "#policycloud-marketplace-account-edit input[name=current-password]"
          ).prop("required", false);
        }
      }
    );

    // Editing submission
    $("#policycloud-marketplace-account-edit").submit((e) => {
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
                  // Set 30 day cookie.
                  let date = new Date();
                  date.setTime(date.getTime() + 30 * 24 * 60 * 60 * 1000);
                  const expires = "expires=" + date.toUTCString();
                  document.cookie =
                    "ppmapi-token=" +
                    response_data.data +
                    "; path=/;" +
                    expires;
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
    });

    // Verification email resend.
    $(
      "button#policycloud-marketplace-resend-verification-email, a#policycloud-marketplace-resend-verification-email"
    ).click((e) => {
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
    });

    // Store newly verified token
    if (ajax_properties_account_editing.verified_token) {
      // Set 30 day cookie.
      let date = new Date();
      date.setTime(date.getTime() + 30 * 24 * 60 * 60 * 1000);
      const expires = "expires=" + date.toUTCString();
      document.cookie =
        "ppmapi-token=" +
        ajax_properties_account_editing.verified_token +
        "; " +
        expires;

      // Redirect to same page without the verification parameter.
      window.location.replace(location.pathname);
    }
  });
})(jQuery);
