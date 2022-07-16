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
      const newLinksField = $("<div><input type='text' name='links-title[]' placeholder='Example' /><input type='url' name='links-url[]' placeholder='https://www.example.org/' /><button class='remove-field' title='Remove this link.' ><span class='fas fa-times'></span></button></div>");
      newLinksField.find('input[name*=links]').each(
        (index, element) => {
          $(element).val("");
        }
      );
      newLinksField.appendTo(container);
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
    }

    /**
     * Prepare and submit via AJAX the edited information fields.
     *
     * @param {Event} e
     *
     * @author Alexandros Raikos <araikos@unipi.gr>
     */
    function createAsset(e) {
      // TODO @alexandrosraikos: Correct required fields prompting. (#129)
      e.preventDefault();
      makeWPRequest(
        ".policyms.description.editor button[type=submit]",
        "policyms_description_creation",
        DescriptionCreationProperties.nonce,
        new FormData($(".policyms.description.editor form")[0]),
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
      ".policyms.description.editor button[type=submit]"
    ).click(createAsset);

    // Add another link field.
    $(document).on(
      "click",
      ".policyms.description.editor .links button.add-field",
      addLinksField
    );

    // Remove last link field.
    $(document).on(
      "click",
      ".policyms.description.editor .links button.remove-field",
      removeLinksField
    );
  });
})(jQuery);
