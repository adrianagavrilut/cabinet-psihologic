Dropzone.autoDiscover = false;
let toAdd = true;
$(document).ready(function () {
  $("div#my-dropzone").dropzone({
    url: "/admin/chat/send.php",
    autoProcessQueue: false,
    uploadMultiple: true,
    init: function () {
      this.on("sending", function (file, xhr, formData) {
        toAdd = true;
        formData.append("content", $("#chat-input").val());
        formData.append(
          "conversation_id",
          $("#chat-content").attr("data-conversation-id")
        );
      });
      this.on("success", function (file, result) {
        if (toAdd) {
          $("#chat-content").append(result);
          $("#chat-input").val("");
          setTimeout(function () {
            Dropzone.forElement("#my-dropzone").removeAllFiles(true);
          }, 4000);
          toAdd = false;
        }
      });
    },
  });

  function showCardFooter() {
    $(".card-footer").removeClass("d-none");
  }

  $(document).on("click", "#chat-send", function () {
    var myDropzone = Dropzone.forElement("#my-dropzone");

    if (myDropzone.getAcceptedFiles().length > 0) {
      myDropzone.processQueue();
    } else {
      $.ajax({
        url: "/admin/chat/send.php",
        type: "POST",
        data: {
          content: $("#chat-input").val(),
          conversation_id: $("#chat-content").attr("data-conversation-id"),
        },
        success: function (result) {
          $("#chat-content").append(result);
          $("#chat-input").val("");
        },
      });
    }
  });

  $(document).on("click", "#chat-conversation-create", function (e) {
    e.preventDefault();
    $(this).hide();
    $.ajax({
      url: "/admin/chat/create-conversation.php",
      type: "GET",
      success: function (result) {
        $("#chat-conversation-create-content").html(result);
        $(this).show();
      },
      error: function () {
        $(this).show();
      },
    });
  });

  $(document).on("click", "#chat-conversation-pacient-create", function (e) {
    e.preventDefault();
    $.ajax({
      url: "/admin/chat/create-conversation-pacient.php",
      type: "POST",
      success: function (result) {
        $("#chat-content").attr("data-conversation-id", result);
        $("#chat-conversation-pacient-create").addClass("d-none");
        $("#show-conversation").removeClass("d-none");
      },
    });
  });

  $(document).on("click", "#chat-conversation-save", function () {
    let element = $(this);
    element.addClass("d-none");
    element.siblings(".js-loader").removeClass("d-none");
    $.ajax({
      url: "/admin/chat/create-conversation.php",
      type: "POST",
      data: {
        id_pacient: $("#chat-pacient").val(),
      },
      success: function (result) {
        $("#chat-conversation-create").html(
          '<button id="chat-conversation-create" class="btn btn-success">Conversatie noua</button>'
        );
        $("#chat-conversations-content").append(result);
        element.removeClass("d-none");
        element.siblings(".js-loader").addClass("d-none");
      },
      error: function () {
        element.removeClass("d-none");
        element.siblings(".js-loader").addClass("d-none");
      },
    });
  });

  $(document).on("click", ".js-item-conversation", function () {
    let conversationId = $(this).attr("data-conversation-id");
    $.ajax({
      url: "/admin/chat/get-conversation.php?id=" + conversationId,
      type: "GET",
      success: function (result) {
        $("#chat-content").html(result);
        $("#chat-content").attr("data-conversation-id", conversationId);
      },
    });
    showCardFooter();
  });

  let chatInterval = setInterval(function () {
    let idConversation = $("#chat-content").attr("data-conversation-id");
    if (
      typeof idConversation !== "undefined" &&
      idConversation &&
      idConversation.trim()
    ) {
      $.ajax({
        url: "/admin/chat/receive.php?conversation_id=" + idConversation,
        type: "GET",
        success: function (result) {
          $("#chat-content").append(result);
        },
      });
    }
  }, 3000);
});
