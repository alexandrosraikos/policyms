/**
 * @file Provides dynamic fields and handles form requests for forms and buttons
 * in the asset archive shortcode.
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
      if (url.searchParams.has("page")) {
        url.searchParams.set("assets-page", $(this).data("page-number"));
      } else {
        url.searchParams.append("assets-page", $(this).data("page-number"));
      }
      window.location.href = url.href;
    }

    /**
     *
     * Generic interface actions & event listeners.
     *
     */
    $("#policycloud-marketplace-asset-archive header .filters-toggle").click(
      (e) => {
        e.preventDefault();
        $("#policycloud-marketplace-asset-archive").toggleClass("inspect");
      }
    );

    $(
      "#policycloud-marketplace-asset-archive .content nav.pagination button"
    ).click(switchPage);
  });
})(jQuery);
