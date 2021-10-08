(function ($) {
  "use strict";
  $(document).ready(function () {
    // Dynamic socials fields
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
      window.location.hash == "#objects" ||
      window.location.hash == "#reviews" ||
      window.location.hash == "#information"
    ) {
      $("button#policycloud-account-" + window.location.hash.substr(1)).trigger(
        "click"
      );
    }

    // Asset collection filters
    $("#policycloud-account-asset-collection-filters button").click(function (
      e
    ) {
      e.preventDefault();
      $(this).toggleClass("active");

      if (
        $("#policycloud-account-asset-collection-filters button.active")
          .length > 0
      ) {
        $("#policycloud-account-assets-list li").removeClass("visible");

        // Show all active filters.
        var buttons = $("#policycloud-account-asset-collection-filters button");
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
    });

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
    });

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
                // Set 30 day cookie.
                let date = new Date();
                date.setTime(date.getTime() + 30 * 24 * 60 * 60 * 1000);
                const expires = "expires=" + date.toUTCString();
                document.cookie =
                  "ppmapi-token=" + response_data.data + "; path=/;" + expires;
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
