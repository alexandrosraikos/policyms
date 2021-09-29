(function ($) {
  "use strict";

  $(function () {
    $(".checkbox").on("change", function () {
      $("#checkbox1").submit();
    });
  });

  $(document).ready(() => {
    /* Loop through all dropdown buttons to toggle between hiding and showing its dropdown content - This allows the user to have multiple dropdowns without any conflict */
    $(".dropdown-btn1").on("click", function () {
      $(this).toggleClass("active");

      var dropdownContent = this.nextElementSibling;
      if (dropdownContent.style.display === "block") {
        dropdownContent.style.display = "none";
      } else {
        dropdownContent.style.display = "block";
      }
    });
    $("body").on("focus", ".datepicker", function () {
      $(this).datepicker();
    });

    $(document).ready(function () {
      var max_fields = 10; //maximum input boxes allowed
      var wrapper = $(".input_fields_wrap"); //Fields wrapper
      var add_button = $(".add_field_button"); //Add button ID

      var x = 1; //initlal text box count
      $(add_button).click(function (e) {
        //on add input button click
        e.preventDefault();
        if (x < max_fields) {
          //max input box allowed
          x++; //text box increment
          $(wrapper).append(
            '<div class="input-line-control removeMe"><div class="col-1"><div class="form"><input type="text"  class="myinp" placeholder="Add Key"> <input  class="myinp"type="text" placeholder="Value"> <button class="btn btn-danger remove"><i class="fa fa-remove"></i> Remove</button></div></div></div>'
          ); //add input box
        }
      });

      $(wrapper).on("click", ".remove", function (e) {
        //user click on remove text
        e.preventDefault();
        $(this).closest("div.removeMe").remove();
        x--;
      });
    });
  });
  $(document).on("input change", "#pcslider", function () {
    $("#pcslider_value").html($(this).val());
  });
})(jQuery);
