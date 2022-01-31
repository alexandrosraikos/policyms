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
     * Add a double input field to the links container.
     *
     * @param {Event} e
     *
     * @author Alexandros Raikos <araikos@unipi.gr>
     */
    function addLinksField(e) {
      e.preventDefault();
      const container = $(this).prev();
      const newLinksField = container.children("div:last-of-type").clone();
      newLinksField.find('input[name*=links]').each(
        (index, element) => {
          $(element).val("");
        }
      );
      newLinksField.appendTo(container);
      container.find("button.remove-field").prop(
        "disabled",
        container.children().length === 1
      );
    }
    /**
     *
     * Remove a double input field from the links container.
     *
     * @param {Event} e
     *
     * @author Alexandros Raikos <araikos@unipi.gr>
     */
    function removeLinksField(e) {
      e.preventDefault();
      const container = $(this).parent().parent();
      $(this).parent().remove();
      container.find("button.remove-field").prop(
        "disabled",
        container.children().length === 1
      );
    }

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

    // Add another link field.
    $(document).on(
      "click",
      ".policycloud-marketplace.description.editor .links button.add-field",
      addLinksField
    );

    // Remove last link field.
    $(document).on(
      "click",
      ".policycloud-marketplace.description.editor .links button.remove-field",
      removeLinksField
    );
  });
})(jQuery);
