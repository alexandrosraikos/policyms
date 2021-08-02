(function ($) {
  "use strict";
  $(document).ready(() => {
    $("#policycloud-registration").submit((e) => {
      e.preventDefault();

      // Perform AJAX request.
      $.ajax({
        url: ajax_prop.ajax_url,
        type: "post",
        data: {
          action: "registration",
          nonce: ajax_prop.nonce,
          username: $("input[name=username]").val(),
          password: $("input[name=password]").val(),
          email: $("input[name=email]").val(),
          name: $("input[name=name]").val(),
          surname: $("input[name=surname]").val(),
          phone: $("input[name=phone]").val(),
          organization: $("input[name=organization]").val(),
          title: $("select[name=title]").val(),
          gender: $("select[name=gender]").val(),
        },

        // Handle response.
        complete: function (response) {
          /*
          TODO@alexandrosraikos: Χειρισμός σφαλμάτων εγγραφής.
          */
          // if (response.responseText.includes("error")) {
          //   var url = new URL(window.location.href);
          //   var params = url.searchParams;
          //   params.set("error", response.responseText);
          //   url.search = params.toString();
          //   window.location.href = url.toString();
          // } else {
          //   window.location.reload();
          // }
        },
        dataType: "json",
      });
    });
  });
})(jQuery);
