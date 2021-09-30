(function ($) {
  "use strict";
  $(document).ready(function () {
    // Navigation
    $("button#policycloud-account-overview").click(function (e) {
      e.preventDefault();
      $(
        "section.policycloud-account-overview, section.policycloud-account-likes, section.policycloud-account-assets, section.policycloud-account-details"
      ).removeClass("focused");
      $("section.policycloud-account-overview").addClass("focused");

      $(
        "button#policycloud-account-overview, button#policycloud-account-assets, button#policycloud-account-likes, button#policycloud-account-details"
      ).removeClass("active");
      $(this).addClass("active");
      window.location.hash = "";
    });
    $("button#policycloud-account-assets").click(function (e) {
      e.preventDefault();
      $(
        "section.policycloud-account-overview, section.policycloud-account-likes, section.policycloud-account-assets, section.policycloud-account-details"
      ).removeClass("focused");
      $("section.policycloud-account-assets").addClass("focused");

      $(
        "button#policycloud-account-overview, button#policycloud-account-assets, button#policycloud-account-likes, button#policycloud-account-details"
      ).removeClass("active");
      $(this).addClass("active");
      window.location.hash = "assets";
    });
    $("button#policycloud-account-likes").click(function (e) {
      e.preventDefault();
      $(
        "section.policycloud-account-overview, section.policycloud-account-likes, section.policycloud-account-assets, section.policycloud-account-details"
      ).removeClass("focused");
      $("section.policycloud-account-likes").addClass("focused");

      $(
        "button#policycloud-account-overview, button#policycloud-account-assets, button#policycloud-account-likes, button#policycloud-account-details"
      ).removeClass("active");
      $(this).addClass("active");
      window.location.hash = "likes";
    });
    $("button#policycloud-account-details").click(function (e) {
      e.preventDefault();
      $(
        "section.policycloud-account-overview, section.policycloud-account-likes, section.policycloud-account-assets, section.policycloud-account-details"
      ).removeClass("focused");
      $("section.policycloud-account-details").addClass("focused");

      $(
        "button#policycloud-account-overview, button#policycloud-account-assets, button#policycloud-account-likes, button#policycloud-account-details"
      ).removeClass("active");
      $(this).addClass("active");
      window.location.hash = "details";
    });

    // Check for existing hash
    if (
      window.location.hash == "#assets" ||
      window.location.hash == "#likes" ||
      window.location.hash == "#details"
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

      // Set all active filters.
      var buttons = $("#policycloud-account-asset-collection-filters button");
      buttons.each(function (i, v) {
        if ($(v).hasClass("active")) {
          $(
            "#policycloud-account-assets-list li." + $(v).data("type-filter")
          ).addClass("visible");
        } else {
          $(
            "#policycloud-account-assets-list li." + $(v).data("type-filter")
          ).removeClass("visible");
        }
      });

      // Show all if no filters.
      if (
        $("#policycloud-account-asset-collection-filters button.active")
          .length == 0
      ) {
        $("#policycloud-account-assets-list li").addClass("visible");
      }
    });

    // Profile editing.
    $("#policycloud-marketplace-account-edit-toggle").click(function (e) {
      e.preventDefault();
      $(".folding").toggleClass("visible");
      if ($(this).html() === "Edit") {
        $(this).html("Cancel");
      } else {
        $(this).html("Edit");
      }
      $("#policycloud-marketplace-account-edit .error").removeClass("visible");
    });

    // Account editing form.
    $("#policycloud-marketplace-account-edit").submit((e) => {
      e.preventDefault();
      $("#policycloud-marketplace-account-edit button[type=submit]").addClass(
        "loading"
      );

      // Perform AJAX request.
      $.ajax({
        url: ajax_properties_account_editing.ajax_url,
        type: "post",
        data: {
          action: "policycloud_marketplace_account_edit",
          nonce: ajax_properties_account_editing.nonce,
          username: $("input[name=policycloud-marketplace-username]").val(),
          password: $("input[name=policycloud-marketplace-password]").val(),
          password_confirm: $(
            "input[name=policycloud-marketplace-password-confirm]"
          ).val(),
          email: $("input[name=policycloud-marketplace-email]").val(),
          public_email: $(
            "input[name=policycloud-marketplace-public-email]"
          ).val(),
          name: $("input[name=policycloud-marketplace-name]").val(),
          surname: $("input[name=policycloud-marketplace-surname]").val(),
          phone: $("input[name=policycloud-marketplace-phone]").val(),
          public_phone: $(
            "input[name=policycloud-marketplace-public-phone]"
          ).val(),
          organization: $(
            "input[name=policycloud-marketplace-organization]"
          ).val(),
          title: $("select[name=policycloud-marketplace-title]").val(),
          gender: $("select[name=policycloud-marketplace-gender]").val(),
        },

        // Handle response.
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
                  "ppmapi-token=" + response_data.data + "; " + expires;
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
      window.location.replace(location.pathname);
    }
  });
})(jQuery);
