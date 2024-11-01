(function ($) {
  "use strict";
  //Settings Tab Js
  $(".wp-feedback-roadmap-tabs .tab").on("click", function (event) {
    event.preventDefault();
    $(".wp-feedback-roadmap-tabs li").removeClass("tab-active");
    $(this).parent().addClass("tab-active");
    $(".wp-feedback-roadmap-tab .wp-feedback-roadmap-tab-detail").hide();
    $($(this).attr("href")).show();
  });

  $("#status-cancel").on("click", function (event) {
    $("#statusFormCollapse").removeClass("show");
  });
  //Drag and Drop Kanban Feedback
  var board = scriptParams.feedback_data;
  var selector = [];
  if (board.length > 0) {
    for (var i = 0; i < board.length; i++) {
      selector.push(document.querySelector("#feedback-board" + i));
    }
  }
  dragula(selector).on("drop", function (el, target, source) {
    var main_ul = $(el).parent();
    var ul_id = main_ul[0].id;
    var board_id = $(el).parent().attr("data-board_id");
    var source_id = $(source).attr("data-board_id");
    $("#count-" + source_id).text(
      parseInt($("#count-" + source_id).text()) - 1
    );
    $("#count-" + board_id).text(parseInt($("#count-" + board_id).text()) + 1);
    var feedback_id = el.dataset.boardtask_id;
    var feedback_order_ids = [];
    $("#" + ul_id + " li").each(function () {
      feedback_order_ids.push($(this).data("boardtask_id"));
    });
    $.ajax({
      url: wp_update.ajaxurl,
      type: "post",
      data: {
        action: "wp_update_board_status",
        _ajax_nonce: wp_update.nonce,
        board_id: board_id,
        feedback_id: feedback_id,
        feedback_order_ids: feedback_order_ids,
      },
      success: function (response) {
        if (response === true) {
        } else {
        }
      },
    });
  });
  //Update Kanban Status Order.
  $("#wp-status-list").sortable({
    placeholder: "ui-state-highlight",
    update: function (event, ui) {
      var status_order_ids = new Array();
      $("#wp-status-list li").each(function () {
		status_order_ids.push($(this).data("feedback-id"));
	});
      $.ajax({
        url: wp_update.ajaxurl,
        type: "post",
        data: {
          action: "update_feedback_status_order",
          _ajax_nonce: wp_roadmap_localize.nonce,
          status_order_ids: status_order_ids,
        },
        success: function (data) {
          if (data) {
            $(".alert-danger").hide();
            $(".alert-success ").show();
          } else {
            $(".alert-success").hide();
            $(".alert-danger").show();
          }
        },
      });
    },
  });
})(jQuery);

$ = jQuery;
function openFeedBackModal(id) {
  $("#wp_roadmap_feedback_add_form").trigger("reset");
  $("#wp_roadmap_status").val(id);
  $("#myModal").modal();
}
