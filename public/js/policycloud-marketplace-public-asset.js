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

    // Get image id array.
    const imageReference = Array.from(
      $("#policycloud-marketplace-asset .gallery img").map((index, element) => {
        return $(element).data("image-id");
      })
    );

    /**
     *
     * Generic interface actions & event listeners.
     *
     */

    // Toggle the file list visibility.
    $(".policycloud-marketplace .file-viewer > button").click(toggleFileList);

    // Toggle the gallery modal visibility.
    $("#policycloud-marketplace-asset .gallery img").click((e) => {
      new Modal(
        "gallery",
        Array.from(
          $("#policycloud-marketplace-asset .gallery img").map(
            (index, element) => {
              return $(element).clone()[0];
            }
          )
        ),
        imageReference.indexOf($(e.target).data("image-id"))
      );
    });

    /**
     * Asset editing
     *
     * This section includes generic functionality
     * regarding the asset editing aspect of the shortcode.
     *
     */

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
      var formData = new FormData($(e.target)[0]);
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

    function downloadFile(e) {
      e.preventDefault();

      // Add loading class.
      $(this).addClass("loading");

      const type = $(this).data("type");
      const fileIdentifier = $(this).data("file-id");

      // Prepare form data.
      var formData = new FormData();
      formData.append("action", "policycloud_marketplace_asset_editing");
      formData.append("nonce", ajax_properties_description_editing.nonce);
      formData.append("asset_id", ajax_properties_description_editing.asset_id);
      formData.append("subsequent_action", "file-download");
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
              var a = document.createElement("a");
              a.href = new URL(data.url);
              a.setAttribute(
                "id",
                "policycloud-marketplace-file-" + fileIdentifier + "-download"
              );
              $("#policycloud-marketplace-asset .file-viewer").append(a);
              $(
                "a#policycloud-marketplace-file-" + fileIdentifier + "-download"
              ).attr("download", "");
              $(
                "a#policycloud-marketplace-file-" + fileIdentifier + "-download"
              )
                .get(0)
                .click();
            }
          );
        },
      });
    }

    /**
     *
     * Asset editing interface actions & event listeners.
     *
     */

    // Toggle asset editor visibility.
    $("#policycloud-marketplace-asset header .show-editor-modal").click((e) => {
      e.preventDefault();
      new Modal(
        "information-editing",
        $("#policycloud-marketplace-asset-editing").clone()[0]
      );
    });

    // Submit the edited information.
    $(document).on(
      "submit",
      "#policycloud-marketplace-asset-editing form",
      updateAsset
    );

    // Delete file.
    $(document).on(
      "click",
      "#policycloud-marketplace-asset-editing .file button.delete",
      deleteFile
    );

    // Approve description (admin)
    $(document).on(
      "click",
      "#policycloud-marketplace-asset-approval button",
      approvalRequest
    );

    // Download file.
    $(document).on(
      "click",
      "#policycloud-marketplace-asset .file-viewer .download",
      downloadFile
    );
  });
})(jQuery);
