/**
 * @file Provides dynamic fields and handles form requests for forms and buttons
 * in the description archive shortcode.
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
     * regarding the usage of the description archive shortcode.
     *
     */

    /**
     * Refreshes with a new page query.
     *
     * @param {Event} e The click event
     *
     * @author Alexandros Raikos <araikos@unipi.gr>
     * @author Eleftheria Kouremenou <elkour@unipi.gr>
     */
    function switchPage(e) {
      e.preventDefault();
      var url = new URL(window.location.href);
      if (url.searchParams.has("descriptions-page")) {
        url.searchParams.set("descriptions-page", $(this).data("page-number"));
      } else {
        url.searchParams.append(
          "descriptions-page",
          $(this).data("page-number")
        );
      }
      window.location.href = url.href;
    }

    /**
     * Refreshes with a new view sorting query.
     *
     * @param {Event} e The click event
     *
     * @author Alexandros Raikos <araikos@unipi.gr>
     * @author Eleftheria Kouremenou <elkour@unipi.gr>
     */
    function addSortFilter(e) {
      e.preventDefault();
      var url = new URL(window.location.href);
      if (url.searchParams.has("sort-by")) {
        url.searchParams.set("sort-by", $(this).val());
      } else {
        url.searchParams.append("sort-by", $(this).val());
      }
      window.location.href = url.href;
    }

    /**
     * Refreshes with a new view sizing query.
     *
     * @param {Event} e The click event
     *
     * @author Alexandros Raikos <araikos@unipi.gr>
     * @author Eleftheria Kouremenou <elkour@unipi.gr>
     */
    function addSizeFilter(e) {
      e.preventDefault();
      var url = new URL(window.location.href);
      if (url.searchParams.has("items-per-page")) {
        url.searchParams.set("items-per-page", $(this).val());
      } else {
        url.searchParams.append("items-per-page", $(this).val());
      }
      window.location.href = url.href;
    }

    /**
     *
     * Generic interface actions & event listeners.
     *
     */

    // Toggle the filter view.
    $(".policyms.descriptions.archive .filters-toggle").click(
      (e) => {
        e.preventDefault();
        $(".policyms.descriptions.archive").toggleClass(
          "inspect"
        );
        if (
          !$(".policyms.descriptions.archive").hasClass("inspect")
        ) {
          $("body").css("overflowY", "hidden");
        } else {
          $("body").css("overflowY", "auto");
        }
      }
    );

    $(
      ".policyms.descriptions.archive .content nav.pagination button"
    ).click(switchPage);

    $(
      '.policyms.descriptions.archive header select[name="sort-by"]'
    ).change(addSortFilter);

    $(
      '.policyms.descriptions.archive header select[name="items-per-page"]'
    ).change(addSizeFilter);
  });
})(jQuery);
