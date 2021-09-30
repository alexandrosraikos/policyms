(function ($) {
  "use strict";
  $(document).ready(() => {
    $(
      ".policycloud-marketplace-error.dismissable, .policycloud-marketplace-notice.dismissable"
    ).prepend(
      '<button class="policycloud-marketplace-alert-close">Dismiss</button>'
    );
    $(".policycloud-marketplace-alert-close").click(function (e) {
      $(this.parentNode).addClass("seen");
    });
  });
})(jQuery);
