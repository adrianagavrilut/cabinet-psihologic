Dropzone.autoDiscover = false;
$(document).ready(function () {
  $("div#my-dropzone-docs").dropzone({
    init: function () {
      this.on("success", function (file, result) {
        // console.log(file.upload.filename);
        // console.log(result);
        const htmlContent =
          '<a href="/assets/img/portofoliu/' +
          result +
          '" target="_blank" class="file-link btn d-flex justify-content-center align-items-center flex-column m-2"><i class="fas fa-file-alt fa-2x text-gray-300 mb-2"></i><span>' +
          file.upload.filename +
          "</span></a>";
        $("#portofolio-wrapper").append(htmlContent);
        setTimeout(() => {
          document.location.reload();
        }, 3000);
      });
    },
  });
});
