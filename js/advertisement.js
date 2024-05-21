(function (document, $, Drupal, once) {
  Drupal.behaviors.advertisement = {
    attach: function (context, settings) {
      $(once("render-advertisements", "ad-content", context)).each(function () {
        let placeholder = $(this);
        let url = Drupal.url("advertisement/render");
        $.get(
          url,
          { id: placeholder.attr("id") },
          function (responseData) {
            for (let id in responseData) {
              if (responseData.hasOwnProperty(id)) {
                $("#" + id).html(responseData[id]);
              }
            }
          },
          "json"
        );
      });
    },
  };
})(document, jQuery, Drupal, once);
