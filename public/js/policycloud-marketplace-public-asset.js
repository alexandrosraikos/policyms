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
    function showGalleryModal(e) {
      e.preventDefault();
      // Create modal.
      $("html, body").css({ overflow: "hidden" });
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
    function hideGalleryModal(e) {
      e.preventDefault();
      $("html, body").css({ overflow: "auto" });
      $("#policycloud-marketplace-asset .gallery .modal").remove();
    }

    /**
     *
     * Generic interface actions & event listeners.
     *
     */

    // Toggle the file list visibility.
    $(".policycloud-marketplace .file-viewer > button").click(toggleFileList);

    // Toggle the gallery modal visibility.
    $("#policycloud-marketplace-asset .gallery img").click(showGalleryModal);
    $(document).on(
      "click",
      "#policycloud-marketplace-asset .gallery .modal .close, #policycloud-marketplace-asset .gallery .modal *:not(img)",
      hideGalleryModal
    );

    /**
     * Asset editing
     *
     * This section includes generic functionality
     * regarding the asset editing aspect of the shortcode.
     *
     */

    /**
     * Show the asset editing modal.
     * @param {Event} e
     */
    function showAssetEditor(e) {
      e.preventDefault();
      $("html, body").css({ overflow: "hidden" });
      $("#policycloud-marketplace-asset .editing-form").removeClass("hidden");
    }

    /**
     * Hide the asset editing modal.
     * @param {Event} e
     */
    function hideAssetEditor(e) {
      e.preventDefault();
      $("html, body").css({ overflow: "auto" });
      $("#policycloud-marketplace-asset .editing-form").addClass("hidden");
    }

    /**
     * Prepare and submit via AJAX the edited asset fields.
     *
     * @param {Event} e
     *
     * @author Alexandros Raikos <araikos@unipi.gr>
     */
    function updateAsset(e) {
      e.preventDefault();

      // Add loading class.
      $("#policycloud-marketplace-asset-editing button[type=submit]").addClass(
        "loading"
      );

      // Prepare form data.
      var formData = new FormData(
        $("#policycloud-marketplace-asset-editing")[0]
      );
      formData.append("action", "policycloud_marketplace_asset_editing");
      formData.append("nonce", ajax_properties_description_editing.nonce);
      formData.append("asset_id", ajax_properties_description_editing.asset_id);
      formData.append("subsequent_action", "asset-editing");

      // Perform AJAX request.
      $.ajax({
        url: ajax_properties_description_editing.ajax_url,
        type: "post",
        processData: false,
        contentType: false,
        data: formData,
        cache: false,
        dataType: "json",
        complete: (response) => {
          handleAJAXResponse(
            response,
            "#policycloud-marketplace-asset-editing button[type=submit]",
            (data) => {
              if (data.hasOwnProperty("message")) {
                if (data.message != "completed") {
                  showAlert(data.message);
                }
              }
              window.location.reload();
            }
          );
        },
      });
    }

    /**
     * Show password prompt and verify the deletion request
     * before sending via AJAX.
     *
     * @param {Event} e
     *
     * @author Alexandros Raikos <araikos@unipi.gr>
     */
    function approvalRequest(e) {
      e.preventDefault();

      var confirmed = false;
      if ($(this).data("response") === "disapprove") {
        var confirmed = window.confirm(
          "Are you sure you want to delete this asset?"
        );
      } else confirmed = true;

      // Perform the AJAX request for a present password value.
      if (confirmed) {
        // Add loading class
        $(
          "#policycloud-marketplace-asset-approval button[data-response=" +
            $(this).data("response") +
            "]"
        ).addClass("loading");

        $.ajax({
          url: ajax_properties_description_editing.ajax_url,
          type: "post",
          data: {
            action: "policycloud_marketplace_asset_approval",
            nonce: ajax_properties_description_editing.nonce,
            did: ajax_properties_description_editing.asset_id,
            approval: $(this).data("response"),
          },
          dataType: "json",
          complete: (response) => {
            handleAJAXResponse(
              response,
              "#policycloud-marketplace-asset-approval button[data-response=" +
                $(this).data("response") +
                "]",
              () => {
                location.reload();
              }
            );
          },
        });
      }
    }

    /**
     * Prepare and submit via AJAX the edited asset fields.
     *
     * @param {Event} e
     *
     * @author Alexandros Raikos <araikos@unipi.gr>
     */
    function deleteFile(e) {
      e.preventDefault();

      if (window.confirm("Are you sure you want to delete?")) {
        // Add loading class.
        $(this).addClass("loading");

        const type = $(this).closest(".file").data("file-type");
        const fileIdentifier = $(this).closest(".file").data("file-identifier");

        // Prepare form data.
        var formData = new FormData();
        formData.append("action", "policycloud_marketplace_asset_editing");
        formData.append("nonce", ajax_properties_description_editing.nonce);
        formData.append(
          "asset_id",
          ajax_properties_description_editing.asset_id
        );
        formData.append("subsequent_action", "file-deletion");
        formData.append("file-type", type);
        formData.append("file-identifier", fileIdentifier);

        // Perform AJAX request.
        $.ajax({
          url: ajax_properties_description_editing.ajax_url,
          type: "post",
          processData: false,
          contentType: false,
          data: formData,
          cache: false,
          dataType: "json",
          complete: (response) => {
            handleAJAXResponse(
              response,
              "#policycloud-marketplace-asset-editing button[type=submit]",
              (data) => {
                $(this).closest(".file").remove();
              }
            );
          },
        });
      }
    }

    /**
     *
     * Asset editing interface actions & event listeners.
     *
     */

    // Toggle asset editor visibility.
    $("#policycloud-marketplace-asset header .show-editor-modal").click(
      showAssetEditor
    );
    $("#policycloud-marketplace-asset .editing-form .close").click(
      hideAssetEditor
    );

    // Submit the edited information.
    $("#policycloud-marketplace-asset-editing button[type=submit]").click(
      updateAsset
    );

    // Delete file.
    $("#policycloud-marketplace-asset-editing .file button.delete").click(
      deleteFile
    );

    // Approve description (admin)
    $("#policycloud-marketplace-asset-approval button").click(approvalRequest);
  });
})(jQuery);
