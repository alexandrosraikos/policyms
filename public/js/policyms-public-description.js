/**
 * @file Provides dynamic fields and handles form requests for forms and buttons
 * in the asset shortcode.
 *
 * @author Alexandros Raikos <alexandros@araikos.gr>
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
    const itemReference = Array.from(
      $(".policyms.description .gallery .item").map((index, element) => {
        return {
          category: $(element).data("asset-category"),
          id: $(element).data("asset-id")
        };
      })
    );

    /**
     *
     * Generic interface actions & event listeners.
     *
     */

    // Toggle the file list visibility.
    $(".policyms .file-viewer > button").click(toggleFileList);

    // Toggle the gallery modal visibility.
    $(".policyms.description .gallery .item").click((e) => {
      e.preventDefault();
      new Modal(
        "gallery",
        itemReference.map(
          (itemData) => {
            return `<div data-asset-category="` + itemData.category + `" data-asset-id="` + itemData.id + `">
            </div>`;
          }
        ),
        itemReference.findIndex((itemData) => {
          return itemData.id == $(e.target).parent('.item').data('asset-id')
        }),
        (itemContainer) => {
          const type = $(itemContainer).data('asset-category');
          const fileIdentifier = $(itemContainer).data('asset-id');
          const toolbar = $('.policyms.description .gallery .item[data-asset-id="' + fileIdentifier + '"] .toolbar');

          if (type == 'images') {
            makeWPRequest(
              this,
              "policyms_asset_download",
              DescriptionEditingProperties.assetDownloadNonce,
              {
                'description_id': DescriptionEditingProperties.descriptionID,
                'category': type,
                'file_id': fileIdentifier,
                'download': false,
              },
              (data) => {
                const fullQualityImage = '<img src="' + data.url + '" />';
                $(itemContainer).prepend(fullQualityImage);
              }
            );
          }
          else if (type == 'videos') {
            const videoPlayer = `
            <video src="`+ DescriptionEditingProperties.videoURL + '/videos/' + fileIdentifier + `" class="large" data-asset-category="videos" data-asset-id="` + fileIdentifier + `" controls preload="none" />
            `;
            $(itemContainer).prepend(videoPlayer);
          }
          $(itemContainer).append(toolbar.clone());
        }
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
     * @author Alexandros Raikos <alexandros@araikos.gr>
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
        ".policyms.description.editor button[type=submit]",
        "policyms_description_editing",
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
     * @author Alexandros Raikos <alexandros@araikos.gr>
     */
    function deleteDescription(e) {
      e.preventDefault();

      if (window.confirm("Are you sure you want to delete this description?")) {
        makeWPRequest(
          '.policyms.description.editor button[data-action="delete-description"]',
          "policyms_description_deletion",
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
     * @author Alexandros Raikos <alexandros@araikos.gr>
     */
    function deleteAsset(e) {
      e.preventDefault();
      if (window.confirm("Are you sure you want to delete this asset?")) {
        // Add loading class.
        $(this).addClass("loading");
        const assetID = $(this).data("asset-id");
        const assetCategory = $(this).data("asset-category");

        makeWPRequest(
          '.policyms.description.editor .file[data-file-identifier="' +
          assetID +
          '"] button.delete',
          "policyms_asset_delete",
          DescriptionEditingProperties.assetDeleteNonce,
          {
            'description_id': DescriptionEditingProperties.descriptionID,
            'asset_category': assetCategory,
            'asset_id': assetID
          },
          () => {
            Modal.kill('gallery');
            $('*[data-asset-id="' + assetID + '"').remove();
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
      formData.append("category", type);
      formData.append("file_id", fileIdentifier);
      formData.append("download", true);

      makeWPRequest(
        this,
        "policyms_asset_download",
        DescriptionEditingProperties.assetDownloadNonce,
        formData,
        (data) => {
          var a = document.createElement("a");
          a.href = new URL(data.url);
          a.setAttribute(
            "id",
            "policyms-file-" + fileIdentifier + "-download"
          );
          $(".policyms.description .file-viewer").append(a);
          $(
            "a#policyms-file-" + fileIdentifier + "-download"
          ).attr("download", "");
          $("a#policyms-file-" + fileIdentifier + "-download")
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
     * @author Alexandros Raikos <alexandros@araikos.gr>
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
          "#policyms-description-approval button[data-response=" +
          $(this).data("response") +
          "]",
          "policyms_description_approval",
          DescriptionEditingProperties.approvalNonce,
          {
            description_id: DescriptionEditingProperties.descriptionID,
            approval: $(this).data("response"),
          },
          () => {
            if ($(this).data("response") == 'approve') {
              window.location.reload();
            } else {
              window.location.href = DescriptionEditingProperties.deleteRedirect;
            }
          }
        );
      }
    }

    function highlightRatingStars(e) {
      const eventStar = $(e.target);
      eventStar.attr('checked', true);
      $('.policyms.description .reviews .stars input[type="radio"]').each((index, element) => {
        $(element).removeClass('checked');
        if ($(element).val() <= eventStar.val()) {
          $(element).addClass('checked');
        }
      });
    }

    function changeReviewPage(e) {
      e.preventDefault();
      if (!$(e.target).hasClass('active')) {
        $(e.target).data('page-number');
        $(".policyms.description .reviews .pagination button").removeClass('active');
        $(e.target).addClass("active");
        makeWPRequest(
          e.target,
          'policyms_get_description_reviews',
          DescriptionEditingProperties.reviewsNonce,
          {
            description_id: DescriptionEditingProperties.descriptionID,
            page: $(e.target).data('page-number')
          },
          (data) => {
            $(".policyms.description .reviews ul").remove();
            $(".policyms.description .reviews:last-child").prepend(data);
          }
        )
      }
    }

    function createReview(e) {
      e.preventDefault();
      var formData = new FormData($(e.target)[0]);
      formData.append(
        "description_id",
        DescriptionEditingProperties.descriptionID
      );

      makeWPRequest(
        ".policyms .reviews form button[type=\"submit\"]",
        'policyms_create_review',
        DescriptionEditingProperties.createReviewNonce,
        formData,
        () => {
          window.location.reload()
        }
      )
    }

    function deleteReview(e) {
      e.preventDefault();
      if (window.confirm('Are you sure you would like to delete this review?')) {
        makeWPRequest(
          e.target,
          'policyms_delete_review',
          DescriptionEditingProperties.deleteReviewNonce,
          {
            "description_id": DescriptionEditingProperties.descriptionID,
            "author_id": $(this).data("author-id")
          },
          () => {
            window.location.reload()
          }
        )
      }
    }

    function setDefaultImage(e) {
      e.preventDefault();
      makeWPRequest(
        e.target,
        'policyms_set_description_image',
        DescriptionEditingProperties.setDefaultImageNonce,
        {
          "description_id": DescriptionEditingProperties.descriptionID,
          "image_id": $(e.target).data('asset-id')
        },
        () => {
          // Unset other remove buttons.
          $('.gallery .toolbar button[data-action="remove-default"]').each(
            (index, element) => {
              $(element).data("action", "set-default");
              $(element).html('Set as cover image');
            }
          );
          // Transform current button.
          const setButton = $('.gallery .toolbar button[data-action="set-default"][data-asset-id="' + $(e.target).data('asset-id') + '"]').each(
            (index, element) => {
              $(element).attr("data-action", "remove-default");
              $(element).html('Remove cover image');
            }
          );
        }
      )
    }

    function removeDefaultImage(e) {
      e.preventDefault();
      makeWPRequest(
        e.target,
        'policyms_remove_description_image',
        DescriptionEditingProperties.removeDefaultImageNonce,
        {
          "description_id": DescriptionEditingProperties.descriptionID
        },
        () => {
          // Set all "set" buttons
          $('.gallery .toolbar button[data-action*="default"]').each(
            (index, element) => {
              $(element).attr("data-action", "set-default");
              $(element).html('Set as cover image');
            }
          );
        }
      )
    }

    /**
     *
     * Asset editing interface actions & event listeners.
     *
     */

    // Toggle asset editor visibility.
    $(".policyms.description button[data-action=\"edit\"]").click((e) => {
      e.preventDefault();
      new Modal(
        "description-editor",
        $(".policyms.description.editor").clone()[0]
      );
    });

    // Submit the edited information.
    $(document).on(
      "submit",
      ".policyms.description.editor form",
      updateDescription
    );

    $(document).on(
      "click",
      '.policyms.description.editor button[data-action="delete-description"]',
      deleteDescription
    );

    // Approve description (admin)
    $(document).on(
      "click",
      "#policyms-description-approval button",
      approvalRequest
    );

    // Download file.
    $(document).on(
      "click",
      ".policyms.description .file-viewer .download",
      downloadAsset
    );

    // Change rating page.
    $(document).on(
      'click',
      '.policyms.description .reviews button[data-action="change-review-page"]',
      changeReviewPage
    )

    // Highlight rating stars.
    $(document).on(
      "click",
      '.policyms.description .reviews .stars input[type="radio"]',
      highlightRatingStars
    )

    // Submit new review.
    $(document).on(
      'submit',
      '.policyms .reviews form',
      createReview
    )

    // Delete a review.
    $(document).on(
      'click',
      '.policyms .reviews button[data-action="delete-review"]',
      deleteReview
    )

    // Set cover image.
    $(document).on(
      "click",
      ".policyms.modal.gallery .toolbar button[data-action=\"set-default\"]",
      setDefaultImage
    );

    // Remove cover image.
    $(document).on(
      "click",
      ".policyms.modal.gallery .toolbar button[data-action=\"remove-default\"]",
      removeDefaultImage
    );

    // Delete file.
    $(document).on(
      "click",
      ".policyms.modal.gallery .toolbar button[data-action=\"delete\"], .policyms.description.editor .asset-editor button[data-action=\"delete\"]",
      deleteAsset
    );
  });
})(jQuery);
