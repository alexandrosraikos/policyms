/**
 * @file Provides dynamic fields and handles form requests for forms and buttons
 * in the asset shortcode.
 *
 * @author Alexandros Raikos <alexandros@araikos.gr>
 * @author Eleftheria Kouremenou <elkour@unipi.gr>
 */

var presetElementQueries = {

  descriptionContainer: '.policyms-description-single',

  descriptionContainer: '.policyms-description-single',

  descriptionRejectionButton: descriptionContainer + ' > form > button[data-action="policyms-reject-description"]',

  descriptionApprovalButton: descriptionContainer + ' > form > button[data-action="policyms-approve-description"]',

  descriptionAssetTableToggle: presetElementQueries.descriptionContainer + ' > .content > aside > .policyms-asset-information-table > button[data-action="policyms-toggle-file-table-vibility"]',

  descriptionGalleryItem: presetElementQueries.descriptionContainer + '.content > .information > .description > .gallery > .slider > .item',

  assetDownloadButton: descriptionContainer + ' > .content > aside > .policyms-asset-information-table > tr > td > a.download',

  descriptionReviewPageButton: descriptionContainer + ' > .reviews > .policyms-description-reviews > nav.pagination > button[data-action="policyms-description-change-review-page"]',

  descriptionReviewStarButton: presetElementQueries.descriptionReviewForm + ' > .stars > input[type="radio"]',

  descriptionReviewForm: presetElementQueries.descriptionContainer + ' > .reviews > .policyms-description-reviews > form[data-action="policyms-add-review"]',

  descriptionReviewDeleteButton: presetElementQueries.descriptionReviewForm + ' > .actions > button[data-action="policyms-delete-review"]',

  descriptionEditingFormContainer: '.policyms-description-editor',

  descriptionEditingForm: descriptionEditingFormContainer + 'form[data-action="policyms-edit-description"]',

  descriptionEditButton: descriptionContainer + '> header > .title > button[data-action="policyms-edit-description"]',

  descriptionSetCoverButton: presetElementQueries.descriptionEditingForm + ' > fieldset[name="assets"] > .asset-editor > button[data-action="policyms-set-cover-asset"]',

  descriptionRemoveCoverButton: presetElementQueries.descriptionEditingForm + ' > fieldset[name="assets"] > .asset-editor > button[data-action="policyms-remove-cover-asset"]',

  descriptionDeleteAssetButton: presetElementQueries.descriptionEditingForm + ' > fieldset[name="assets"] > .asset-editor > div > button[data-action="policyms-delete-asset"]',

  descriptionDeletionButton: descriptionEditingForm + '> .actions > button[data-action="delete-description"]',

};
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
  makeWPRequest(
    presetElementQueries.descriptionEditingForm,
    "policyms_description_editing",
    $(presetElementQueries.descriptionEditingForm).data('nonce'),
    new FormData($(e.target)[0]),
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
      presetElementQueries.descriptionDeletionButton,
      "policyms_description_deletion",
      $(presetElementQueries.descriptionDeletionButton).data('nonce'),
      {
        description_id: $(presetElementQueries.descriptionEditingForm).data('description-id'),
      },
      () => {
        window.location.href = $(presetElementQueries.descriptionDeletionButton).data('redirect');
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
      $(e.target),
      "policyms_asset_delete",
      $(presetElementQueries.descriptionDeleteAssetButton).data('nonce'),
      {
        'description_id': $(presetElementQueries.descriptionEditingForm).data('description-id'),
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
  $(e.target).addClass("loading");

  const type = $(e.target).parent().parent().data("asset-type");
  const fileIdentifier = $(e.target).parent().parent().data("asset-identifier");

  // Prepare form data.
  var formData = new FormData();
  formData.append(
    "description_id",
    $(presetElementQueries.descriptionEditingForm).data('description-id')
  );
  formData.append("category", type);
  formData.append("file_id", fileIdentifier);
  formData.append("download", true);

  makeWPRequest(
    this,
    "policyms_asset_download",
    $(e.target).data('nonce'),
    formData,
    (data) => {
      var a = document.createElement("a");
      a.href = new URL(data.url);
      a.setAttribute(
        "id",
        "policyms-file-" + fileIdentifier + "-download"
      );
      $(presetElementQueries.assetDownloadButton).append(a);
      $(
        "a#policyms-file-" + fileIdentifier + "-download"
      ).attr("download", "");
      $("a#policyms-file-" + fileIdentifier + "-download")
        .get(0)
        .click();
      setTimeout(() => {
        $("a#policyms-file-" + fileIdentifier + "-download").remove();
      }, 200);
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
function approveDescription(e) {
  e.preventDefault();
  makeWPRequest(
    presetElementQueries.descriptionApprovalButton,
    "policyms_description_approval",
    $(presetElementQueries.descriptionApprovalButton).data('nonce'),
    {
      description_id: $(presetElementQueries.descriptionEditingForm).data('description-id'),
      decision: 'approve'
    },
    () => {
      window.location.reload();
    }
  );
}

function rejectDescription(e) {
  e.preventDefault();

  var reason = window.prompt(
    "What is the reason for this description's rejection?"
  );

  makeWPRequest(
    presetElementQueries.descriptionApprovalButton,
    "policyms_description_approval",
    $(presetElementQueries.descriptionApprovalButton).data('nonce'),
    {
      description_id: $(presetElementQueries.descriptionEditingForm).data('description-id'),
      decision: 'approve',
      reason: reason
    },
    () => {
      window.location.href = $(presetElementQueries.descriptionDeletionButton).data('redirect');
    }
  );
}

function highlightRatingStars(e) {
  const eventStar = $(e.target);
  eventStar.attr('checked', true);
  $(presetElementQueries.descriptionReviewStarButton).each((index, element) => {
    $(element).prop('checked', false);
    if ($(element).val() <= eventStar.val()) {
      $(element).prop('checked', true);
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
      $(e.target).data('nonce'),
      {
        description_id: $(presetElementQueries.descriptionEditingForm).data('description-id'),
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
    $(presetElementQueries.descriptionEditingForm).data('description-id')
  );

  makeWPRequest(
    presetElementQueries.descriptionReviewForm,
    'policyms_create_review',
    $(presetElementQueries.descriptionReviewForm).data('nonce'),
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
      $(presetElementQueries.descriptionReviewDeleteButton).data('nonce'),
      {
        "description_id": $(presetElementQueries.descriptionEditingForm).data('description-id'),
        "author_id": $(this).data("author-id")
      },
      () => {
        window.location.reload()
      }
    );
  }
}

function setDefaultImage(e) {
  e.preventDefault();
  makeWPRequest(
    e.target,
    'policyms_set_description_image',
    $(presetElementQueries.descriptionSetCoverButton).data('nonce'),
    {
      "description_id": $(presetElementQueries.descriptionEditingForm).data('description-id'),
      "image_id": $(e.target).data('asset-id')
    },
    () => {
      // Unset other remove buttons.
      $(presetElementQueries.descriptionRemoveCoverButton).each(
        (_, element) => {
          $(element).data("action", "policyms-set-cover-asset");
          $(element).html('Set as cover image');
        }
      );
      // Transform current button.
      $(presetElementQueries.descriptionSetCoverButton + '[data-asset-id="' + $(e.target).data('asset-id') + '"]').each(
        (_, element) => {
          $(element).attr("data-action", "policyms-remove-cover-asset");
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
    $(presetElementQueries.descriptionRemoveCoverButton).data('nonce'),
    {
      "description_id": $(presetElementQueries.descriptionEditingForm).data('description-id')
    },
    () => {
      // Set all "set" buttons
      $('.gallery .toolbar button[data-action*="default"]').each(
        (_, element) => {
          $(element).attr("data-action", "set-default");
          $(element).html('Set as cover image');
        }
      );
    }
  )
}

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
    $(presetElementQueries.descriptionAssetTableToggle).click(toggleFileList);

    // Toggle the gallery modal visibility.
    $(presetElementQueries.descriptionGalleryItem).click((e) => {
      e.preventDefault();
      new Modal(
        "gallery",
        itemReference.map(
          (itemData) => {
            return `
            <div 
              data-asset-category="` + itemData.category + `" 
              data-asset-id="` + itemData.id + `">
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
              $(presetElementQueries.assetDownloadButton).data('nonce'),
              {
                'description_id': $(presetElementQueries.descriptionEditingForm).data('description-id'),
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
     *
     * Asset editing interface actions & event listeners.
     *
     */

    // Toggle asset editor visibility.
    $(presetElementQueries.descriptionEditButton).click((e) => {
      e.preventDefault();
      new Modal(
        "description-editor",
        $(presetElementQueries.descriptionEditingFormContainer).clone()[0]
      );
    });

    // Submit the edited information.
    $(document).on(
      "submit",
      presetElementQueries.descriptionEditingForm,
      updateDescription
    );

    $(document).on(
      "click",
      presetElementQueries.descriptionDeletionButton,
      deleteDescription
    );

    // Reject description (admin)
    $(document).on(
      "click",
      presetElementQueries.descriptionRejectionButton,
      rejectDescription
    );

    // Approve description (admin)
    $(document).on(
      "click",
      presetElementQueries.descriptionApprovalButton,
      approveDescription
    );

    // Download file.
    $(document).on(
      "click",
      presetElementQueries.assetDownloadButton,
      downloadAsset
    );

    // Change rating page.
    $(document).on(
      'click',
      presetElementQueries.descriptionReviewPageButton,
      changeReviewPage
    )

    // Highlight rating stars.
    $(document).on(
      "click",
      presetElementQueries.descriptionReviewStarButton,
      highlightRatingStars
    )

    // Submit new review.
    $(document).on(
      'submit',
      presetElementQueries.descriptionReviewForm,
      createReview
    )

    // Delete a review.
    $(document).on(
      'click',
      presetElementQueries.descriptionReviewDeleteButton,
      deleteReview
    )

    // Set cover image.
    $(document).on(
      "click",
      presetElementQueries.descriptionSetCoverButton,
      setDefaultImage
    );

    // Remove cover image.
    $(document).on(
      "click",
      presetElementQueries.descriptionRemoveCoverButton,
      removeDefaultImage
    );

    // Delete file.
    $(document).on(
      "click",
      presetElementQueries.descriptionDeleteAssetButton,
      deleteAsset
    );
  });
})(jQuery);
