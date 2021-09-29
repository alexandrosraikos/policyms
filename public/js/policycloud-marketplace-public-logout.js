(function ($) {
  "use strict";
  $(document).ready(() => {
    $("a.policycloud-logout, button.policycloud-logout").click((e) => {
      document.cookie =
        "ppmapi-token=; Path=/; Expires=Thu, 01 Jan 1970 00:00:01 GMT;";
      location.reload();
    });
  });
})(jQuery);
