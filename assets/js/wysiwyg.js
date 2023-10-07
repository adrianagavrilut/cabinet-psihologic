$(document).ready(function () {
  let editors = [];
  $(".editor").each(function () {
    let id = $(this).attr("id");
    editors[id] = new Quill(this, {
      placeholder: "Scrieți răspunsul...",
      theme: "snow",
    });

    editors[id].on("text-change", function (delta, oldDelta, source) {
      $.ajax({
        url: "/pacient/test/raspuns.php",
        type: "POST",
        data: {
          id_test: $("#test-container").attr("data-test-id"),
          id_intrebare: $(editors[id].container).attr("data-intrebare-id"),
          continut: editors[id].container.firstChild.innerHTML,
        },
        success: function (result) {}, //if error
      });
    });
  });
});
