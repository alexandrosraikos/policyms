(function ($) {
  "use strict";
  $(document).ready(function () {
    // Navigation
    $("button#policycloud-account-overview").click(function (e) {
      e.preventDefault();
      $(
        "section.policycloud-account-overview, section.policycloud-account-likes, section.policycloud-account-assets"
      ).removeClass("focused");
      $("section.policycloud-account-overview").addClass("focused");

      $(
        "button#policycloud-account-overview, button#policycloud-account-assets, button#policycloud-account-likes"
      ).removeClass("active");
      $(this).addClass("active");
    });
    $("button#policycloud-account-assets").click(function (e) {
      e.preventDefault();
      $(
        "section.policycloud-account-overview, section.policycloud-account-likes, section.policycloud-account-assets"
      ).removeClass("focused");
      $("section.policycloud-account-assets").addClass("focused");

      $(
        "button#policycloud-account-overview, button#policycloud-account-assets, button#policycloud-account-likes"
      ).removeClass("active");
      $(this).addClass("active");
    });
    $("button#policycloud-account-likes").click(function (e) {
      e.preventDefault();
      $(
        "section.policycloud-account-overview, section.policycloud-account-likes, section.policycloud-account-assets"
      ).removeClass("focused");
      $("section.policycloud-account-likes").addClass("focused");

      $(
        "button#policycloud-account-overview, button#policycloud-account-assets, button#policycloud-account-likes"
      ).removeClass("active");
      $(this).addClass("active");
    });

    // Asset collection filters
    $("#policycloud-account-asset-collection-filters button").click(function (
      e
    ) {
      e.preventDefault();
      $(this).toggleClass("active");

      // Set all active filters.
      var buttons = $("#policycloud-account-asset-collection-filters button");
      buttons.each(function (i, v) {
        if ($(v).hasClass("active")) {
          $(
            "#policycloud-account-assets-list li." + $(v).data("type-filter")
          ).addClass("visible");
        } else {
          $(
            "#policycloud-account-assets-list li." + $(v).data("type-filter")
          ).removeClass("visible");
        }
      });

      // Show all if no filters.
      if (
        $("#policycloud-account-asset-collection-filters button.active")
          .length == 0
      ) {
        $("#policycloud-account-assets-list li").addClass("visible");
      }
    });
  });
})(jQuery);
