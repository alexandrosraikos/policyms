(function ($) {
  "use strict";
  $(document).ready(() => {
    // Dismiss error dialogues and notices.
    $(
      ".policycloud-marketplace-error.dismissable, .policycloud-marketplace-notice.dismissable"
    ).prepend(
      '<button class="policycloud-marketplace-alert-close">Dismiss</button>'
    );
    $(".policycloud-marketplace-alert-close").click(function (e) {
      $(this.parentNode).addClass("seen");
    });

    // User log out.
    $("a.policycloud-logout, button.policycloud-logout").click((e) => {
      document.cookie =
        "ppmapi-token=; Path=/; Expires=Thu, 01 Jan 1970 00:00:01 GMT;";
      window.location.href = "/";
    });
  });
})(jQuery);
