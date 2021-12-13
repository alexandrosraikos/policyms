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
      makeWPRequest(
        ".policycloud-marketplace.description.editor button[type=submit]",
        "policycloud_marketplace_description_creation",
        DescriptionCreationProperties.nonce,
        new FormData($(".policycloud-marketplace.description.editor form")[0]),
        (data) => {
          window.location.replace(
            DescriptionCreationProperties.descriptionPage + "/?did=" + data
          );
        }
      );
    }

    /**
     *
     * Generic interface actions & event listeners.
     *
     */

    // Submit the asset.
    $(
      ".policycloud-marketplace.description.editor button[type=submit]"
    ).click(createAsset);
  });
})(jQuery);
