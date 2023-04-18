(function ($) {
  $(document).ready(function () {
    $(document).on("click", ".h5ap_import_data", function (e) {
      e.preventDefault();
      $.ajax({
        url: h5apAdmin.ajaxUrl,
        data: {
          action: "h5ap_import_data",
        },
        success: function (data) {
          const result = JSON.parse(data);
          if (result.success === true) {
            location.href = location.href + "?h5ap-import=success";
          }
        },
      });
    });

    // copy to clipboard
    $(".h5ap_front_shortcode input").on("click", function (e) {
      e.preventDefault();

      let shortcode = $(this).parent().find("input")[0];
      shortcode.select();
      shortcode.setSelectionRange(0, 30);
      document.execCommand("copy");
      $(this).parent().find(".htooltip").text("Copied Successfully!");
    });

    $(".h5ap_front_shortcode input").on("mouseout", function () {
      $(this).parent().find(".htooltip").text("Copy To Clipboard");
    });
  });
})(jQuery);
