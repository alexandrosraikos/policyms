/**
 * @file Provides dynamic fields and handles form requests for forms and buttons
 * in the asset shortcode.
 *
 * @author Alexandros Raikos <araikos@unipi.gr>
 * @author Eleftheria Kouremenou <elkour@unipi.gr>
 */

(function ($) {
  "use strict";

  $(document).ready(function () {
    /**
     * Generic
     *
     * This section includes generic functionality
     * regarding the usage of the asset archive shortcode.
     *
     */

    /**
     * Display the gallery image modal.
     * @param {Event} e
     *
     * @author Alexandros Raikos
     */
    function showModal(e) {
      e.preventDefault();
      // Create modal.
      $("#policycloud-marketplace-asset .gallery").append(
        '<div class="modal"><button class="close"><span class="fas fa-times"></span></button></div>'
      );

      // Clone image to modal.
      $(this)
        .clone()
        .appendTo("#policycloud-marketplace-asset .gallery .modal");
    }

    /**
     * Destroy the gallery image modal.
     * @param {Event} e
     */
    function killModal(e) {
      e.preventDefault();
      $("#policycloud-marketplace-asset .gallery .modal").remove();
    }

    /**
     * Show the asset editing modal.
     * @param {Event} e
     */
    function showAssetEditor(e) {
      e.preventDefault();
      $("#policycloud-marketplace-asset .editing-form").removeClass("hidden");
    }

    /**
     * Hide the asset editing modal.
     * @param {Event} e
     */
    function hideAssetEditor(e) {
      e.preventDefault();
      $("#policycloud-marketplace-asset .editing-form").addClass("hidden");
    }

    /**
     *
     * Generic interface actions & event listeners.
     *
     */

    $(".policycloud-marketplace .file-viewer > button").click(toggleFileList);
    $("#policycloud-marketplace-asset .gallery img").click(showModal);
    $(document).on(
      "click",
      "#policycloud-marketplace-asset .gallery .modal .close, #policycloud-marketplace-asset .gallery .modal *:not(img)",
      killModal
    );
    $("#policycloud-marketplace-asset header .show-editor-modal").click(
      showAssetEditor
    );
    $("#policycloud-marketplace-asset .editing-form .close").click(
      hideAssetEditor
    );
  });
})(jQuery);
