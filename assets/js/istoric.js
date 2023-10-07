$(document).ready(function () {
  $(document).on("keyup paste", ".js-ancestor", function () {
    $.ajax({
      url: "/pacient/istoric.php",
      type: "POST",
      data: {
        coloana: $(this).attr("data-field-name"),
        valoare: $(this).val(),
      },
      success: function (result) {},
    });
  });
});
