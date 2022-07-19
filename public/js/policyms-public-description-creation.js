/**
 * @file Provides dynamic fields and handles the AJAX request
 * for the asset creation form.
 *
 * @author Alexandros Raikos <alexandros@araikos.gr>
 */

var presetElementQueries = {
  creationForm: 'form[data-action="policyms-edit-description"]',
  addLinksFieldButton: 'form[data-action="policyms-edit-description"] > fieldset[name="basic-information"] > .links > button[data-action="add-field"]',
  removeLinksFieldButton: 'form[data-action="policyms-edit-description"] > fieldset[name="basic-information"] > .links button[data-action="remove-field"]'
};

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
     * @author Alexandros Raikos <alexandros@araikos.gr>
     */
    function createDescription(e) {
      // TODO @alexandrosraikos: Correct required fields prompting. (#129)
      e.preventDefault();
      makeWPRequest(
        presetElementQueries.creationForm,
        "policyms_description_creation",
        $(presetElementQueries.creationForm).data('nonce'),
        new FormData($(presetElementQueries.creationForm)[0]),
        (data) => {
          window.location.replace(
            $(presetElementQueries.creationForm).data('redirect') + "/?did=" + data
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
    $(document).on(
      "submit",
      presetElementQueries.creationForm,
      createDescription
    );

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
