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
      $(".policycloud-marketplace.description .gallery img").map((index, element) => {
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
    $(".policycloud-marketplace.description .gallery img").click((e) => {
      new Modal(
        "gallery",
        // TODO @alexandrosraikos: Create array of <img> loaders on show. #68
        Array.from(
          $(".policycloud-marketplace.description .gallery img").map(
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
    function updateDescription(e) {
      e.preventDefault();

      // Prepare form data.
      var formData = new FormData($(e.target)[0]);
      formData.append(
        "description_id",
        DescriptionEditingProperties.descriptionID
      );
      formData.append("subsequent_action", "description-editing");

      makeWPRequest(
        ".policycloud-marketplace.description.editor button[type=submit]",
        "policycloud_marketplace_description_editing",
        DescriptionEditingProperties.nonce,
        formData,
        () => {
          window.location.reload();
        }
      );
    }
    /**
     * Delete the description via an AJAX request.
     *
     * @param {Event} e
     *
     * @author Alexandros Raikos <araikos@unipi.gr>
     */
    function deleteDescription(e) {
      e.preventDefault();

      if (window.confirm("Are you sure you want to delete this description?")) {
        makeWPRequest(
          '.policycloud-marketplace.description.editor button[data-action="delete-description"]',
          "policycloud_marketplace_description_deletion",
          DescriptionEditingProperties.deletionNonce,
          {
            description_id: DescriptionEditingProperties.descriptionID,
          },
          () => {
            window.location.href = DescriptionEditingProperties.deleteRedirect;
          }
        );
      }
    }

    /**
     * Prepare and submit via AJAX the edited asset fields.
     *
     * @param {Event} e
     *
     * @author Alexandros Raikos <araikos@unipi.gr>
     */
    function deleteAsset(e) {
      e.preventDefault();
      if (window.confirm("Are you sure you want to delete this asset?")) {
        // Add loading class.
        $(this).addClass("loading");

        const type = $(this).closest(".file").data("file-type");
        const fileIdentifier = $(this).closest(".file").data("file-identifier");

        // Prepare form data.
        var formData = new FormData();
        formData.append(
          "description_id",
          DescriptionEditingProperties.descriptionID
        );
        formData.append("subsequent_action", "asset-deletion");
        formData.append("file-type", type);
        formData.append("file-identifier", fileIdentifier);

        makeWPRequest(
          '.policycloud-marketplace.description.editor .file[data-file-identifier="' +
          fileIdentifier +
          '"] button.delete',
          "policycloud_marketplace_description_editing",
          DescriptionEditingProperties.nonce,
          formData,
          () => {
            $('img[data-image-id="' + fileIdentifier + '"').remove();
            $('*[data-file-id="' + fileIdentifier + '"').remove();
            $(this).closest(".file").remove();
          }
        );
      }
    }

    function downloadAsset(e) {
      e.preventDefault();

      // Add loading class.
      $(this).addClass("loading");

      const type = $(this).data("type");
      const fileIdentifier = $(this).data("file-id");

      // Prepare form data.
      var formData = new FormData();
      formData.append(
        "description_id",
        DescriptionEditingProperties.descriptionID
      );
      formData.append("subsequent_action", "asset-download");
      formData.append("file-type", type);
      formData.append("file-identifier", fileIdentifier);

      makeWPRequest(
        this,
        "policycloud_marketplace_description_editing",
        DescriptionEditingProperties.nonce,
        formData,
        (data) => {
          var a = document.createElement("a");
          a.href = new URL(data.url);
          a.setAttribute(
            "id",
            "policycloud-marketplace-file-" + fileIdentifier + "-download"
          );
          $(".policycloud-marketplace.description .file-viewer").append(a);
          $(
            "a#policycloud-marketplace-file-" + fileIdentifier + "-download"
          ).attr("download", "");
          $("a#policycloud-marketplace-file-" + fileIdentifier + "-download")
            .get(0)
            .click();
        }
      );
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
        makeWPRequest(
          "#policycloud-marketplace-description-approval button[data-response=" +
          $(this).data("response") +
          "]",
          "policycloud_marketplace_description_approval",
          DescriptionEditingProperties.approvalNonce,
          {
            description_id: DescriptionEditingProperties.descriptionID,
            approval: $(this).data("response"),
          },
          () => {
            window.location.reload();
          }
        );
      }
    }

    function highlightRatingStars(e) {
      e.preventDefault();
      const eventStar = $(e.target);
      $('.policycloud-marketplace.description .reviews .stars input[type="radio"]').each((index, element) => {
        if (e.type == 'click' || e.type == 'mouseover') {
          if ($(element).val() <= eventStar.val()) {
            $(element).addClass('checked');
          } else {
            $(element).removeClass('checked');
          }
        }
        else if (e.type == 'mouseout') {
        }
      });
    }

    /**
     *
     * Asset editing interface actions & event listeners.
     *
     */

    // Toggle asset editor visibility.
    $(".policycloud-marketplace.description button[data-action=\"edit\"]").click((e) => {
      e.preventDefault();
      new Modal(
        "description-editor",
        $(".policycloud-marketplace.description.editor").clone()[0]
      );
    });

    // Submit the edited information.
    $(document).on(
      "submit",
      ".policycloud-marketplace.description.editor form",
      updateDescription
    );

    $(document).on(
      "click",
      '.policycloud-marketplace.description.editor button[data-action="delete-description"]',
      deleteDescription
    );

    // Delete file.
    $(document).on(
      "click",
      ".policycloud-marketplace.description.editor .file button.delete",
      deleteAsset
    );

    // Approve description (admin)
    $(document).on(
      "click",
      "#policycloud-marketplace-description-approval button",
      approvalRequest
    );

    // Download file.
    $(document).on(
      "click",
      "#policycloud-marketplace-description .file-viewer .download",
      downloadAsset
    );

    // Highlight rating stars.
    $(document).on(
      "click mouseover mouseout",
      '.policycloud-marketplace.description .reviews .stars input[type="radio"]',
      highlightRatingStars
    )
  });
})(jQuery);
