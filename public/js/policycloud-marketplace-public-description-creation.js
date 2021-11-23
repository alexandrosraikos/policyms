/**
 * @file Provides dynamic fields and handles the AJAX request
 * for the asset creation form.
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
     * Prepare and submit via AJAX the edited information fields.
     *
     * @param {Event} e
     *
     * @author Alexandros Raikos <araikos@unipi.gr>
     */
    function createAsset(e) {
      e.preventDefault();

      // Add loading class.
      $("#policycloud-marketplace-asset-creation button[type=submit]").addClass(
        "loading"
      );

      // Prepare form data.
      var formData = new FormData(
        $("#policycloud-marketplace-asset-creation")[0]
      );
      formData.append("action", "policycloud_marketplace_asset_creation");
      formData.append("nonce", ajax_properties_asset_creation.nonce);

      // Perform AJAX request.
      $.ajax({
        url: ajax_properties_asset_creation.ajax_url,
        type: "post",
        processData: false,
        contentType: false,
        data: formData,
        cache: false,
        dataType: "json",
        complete: (response) => {
          handleAJAXResponse(
            response,
            "#policycloud-marketplace-asset-creation button[type=submit]",
            (data) => {
              window.location.replace(
                ajax_properties_asset_creation.description_page +
                  "/?did=" +
                  data
              );
            }
          );
        },
      });
    }

    /**
     *
     * Generic interface actions & event listeners.
     *
     */

    // Submit the asset.
    $("#policycloud-marketplace-asset-creation button[type=submit]").click(
      createAsset
    );
  });
})(jQuery);
