// TODO @alexandrosraikos: Clean up, comment and claim.

(function ($) {
  "use strict";
  $(document).ready(function () {
    $(
      "#policycloud-object-create select[name=type], #policycloud-object-create select[name=subtype]"
    ).change(() => {});

    /*
      ------------------------
      AJAX Object Creation
      ------------------------
    */
    $("#policycloud-object-create").submit((e) => {
      e.preventDefault();
      $("#policycloud-object-create button[type=submit]").addClass("loading");

      // Perform AJAX request.
      $.ajax({
        url: ajax_properties_object_creation.ajax_url,
        type: "post",
        data: {
          action: "policycloud_marketplace_object_creation",
          nonce: ajax_properties_object_creation.nonce,
          title: $("input[name=title]").val(),
          type: $("input[name=type]").val(),
          subtype: $("input[name=subtype]").val(),
          owner: $("input[name=owner]").val(),
          description: $("textarea[name=description]").val(),
          field_of_use: $("input[name=field-of-use]").val(),
          comment: $("input[name=comment]").val(),
        },

        // Handle response.
        complete: function (response) {
          try {
            var response_data = JSON.parse(response.responseText);
            if (response_data != null) {
              if (response_data.status === "success") {
                window.location.replace(
                  ajax_properties_object_creation.account_page
                );
              } else {
                $("#policycloud-marketplace-description-create .error").html(
                  response_data.data
                );
                $(
                  "#policycloud-marketplace-description-create .error"
                ).addClass("visible");
              }
            }
            if (response.status != 200) {
              $("#policycloud-marketplace-description-create .error").html(
                "HTTP Error " + response.status + ": " + response.statusText
              );
              $("#policycloud-marketplace-description-create .error").addClass(
                "visible"
              );
            }
            $(
              "#policycloud-marketplace-description-create input[type=submit]"
            ).removeClass("loading");
          } catch (objError) {
            $("#policycloud-marketplace-description-create .error").html(
              "Invalid response: " + response.responseText
            );
            $("#policycloud-marketplace-description-create .error").addClass(
              "visible"
            );
          }
        },
        dataType: "json",
      });
    });
  });
})(jQuery);
