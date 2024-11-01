(function ($) {
  "use strict";

  // Debounce function
  function debounce(func, delay) {
    let timer;
    return function () {
      const context = this;
      const args = arguments;
      clearTimeout(timer);
      timer = setTimeout(function () {
        func.apply(context, args);
      }, delay);
    };
  }

  // Saving Feedback Board Status With Ajax Request
  $(".wp-feedback-roadmap-save").on(
    "click",
    debounce(function (event) {
      event.preventDefault();
      const _this = $(this);
      const valid = true;
      if (!$("#name").val()) {
        $(".input_validation").css("border", "1px solid red");
        valid = false;
        _this.prop('disabled', false);
        return valid;
    } else {
        $(".input_validation").css("border", ""); 
    }
      const name = $("#name").val();
      const color = $("#color").val();
      const itemid = $("#itemid").val();
      $.ajax({
        url: wp_roadmap_localize.ajaxurl,
        type: "post",
        data: {
          action: "save_feedback_roadmap_settings",
          _ajax_nonce: wp_roadmap_localize.nonce,
          name: name,
          color: color,
          itemid: itemid
        },
        success: function (response) {
          _this.html("Save Changes");
          if (response === true) {
            $(".input_validation").css("border", ""); 
            setTimeout(function () {
              Swal.fire({
                icon: "success",
                title: "Status has been saved!",
                showConfirmButton: false,
                timer: 2000,
              }).then(() => {
                location.reload();
            });;
            }, 500);
          } else {
            Swal.fire({
              icon: "error",
              title: "Oops...",
              text: "Something went wrong!",
            });
          }
        },
        error: function () {
          _this.html("Save Changes");
          Swal.fire({
            icon: "error",
            title: "Oops...",
            text: "Something went wrong!",
          });
        },
      });
    }, 1000) // Adjust the debounce delay as needed
  );
  
  $("#name").on("input", function () {
    if ($(this).val()) {
        $(".input_validation").css("border", ""); // Clear the validation message
        $(".wp-feedback-roadmap-save").prop('disabled', false); // Re-enable the button if title is written
    }
});

  $('.wp-feedback-status-edit').on('click', function () {
    var statusId = $(this).data('status-id');
    $('#editModal-' + statusId).modal('show');
  });

  // Edit Feedback Board Status With Ajax Request
  $(".wp-feedback-roadmap-update").on("click", function (event) {
    event.preventDefault();
    
    var _this = $(this);
    var form = _this.closest("form"); 
    var nameField = form.find("#name"); 
    
    if (nameField.val().trim() === "") {
      nameField.css("border", "1px solid red");
      return; 
  } else {
      nameField.css("border", ""); 
  }

    var form_data = form.serialize(); 
    
    $.ajax({
        url: wp_roadmap_localize.ajaxurl,
        type: "post",
        data: {
            action: "update_feedback_roadmap_settings",
            _ajax_nonce: wp_roadmap_localize.nonce,
            fields: form_data,
        },
        beforeSend: function () {
            _this.html("Save Changes..."); 
        },
        success: function (response) {
            _this.html("Save Changes");
            if (response === true) {
                setTimeout(function () {
                    Swal.fire({
                        icon: "success",
                        title: "Settings have been saved!",
                        showConfirmButton: false,
                        timer: 2000,
                    }).then(function() {
                        location.reload(); 
                    });
                }, 500);
            } else {
                Swal.fire({
                    icon: "error",
                    title: "Oops...",
                    text: "Something went wrong!",
                });
            }
        },
        error: function () {
            _this.html("Save Status"); // Reset button text after error
            Swal.fire({
                icon: "error",
                title: "Oops...",
                text: "Something went wrong!",
            });
        },
    });
});


  
  // Delete Status Data With Ajax Request
  $(".wp-feedback-status-delete").on("click", function (event) {
    event.preventDefault();
    var feedback_board_id = $(this).data('status-id');
    if (typeof feedback_board_id === 'undefined') {
      feedback_board_id = $(this).val();
    }
    Swal.fire({
      title: "Are you sure you want to delete?",
      icon: "warning",
      showCancelButton: true,
      confirmButtonColor: "#3085d6",
      cancelButtonColor: "#d33",
      confirmButtonText: "Yes, delete it!",
    }).then((result) => {
      if (result.value) {
        $.ajax({
          url: wp_roadmap_localize.ajaxurl,
          type: "post",
          data: {
            action: "delete_feedback_roadmap_settings",
            _ajax_nonce: wp_roadmap_localize.nonce,
            id: feedback_board_id,
          },
          success: function (response) {
            if (response === true) {
              setTimeout(function () {
                Swal.fire({
                  icon: "success",
                  title: "Status has been Deleted!",
                  showConfirmButton: false,
                  timer: 2000,
                }).then(() => {
                  location.reload(); 
              });
              }, 500);
            } else {
              Swal.fire({
                icon: "error",
                title: "Oops...",
                text: "Something went wrong!",
              });
            }
          },
        });
      }
    });
  });
  // Saving General Setting With Ajax Request
  $("#feedback-settings-form").on("submit", function (event) {
    event.preventDefault();
    var _this = $(this);
    var title_settings = $("#title_settings").val();
    var description_settings = $("#description_settings").val();
    var wp_roadmap_status_settings = $("#wp_roadmap_status_settings").val();
    var suggestion_settings = $("#suggestion_settings").val();
    var request_feature_link_settings = $("#request_feature_link_settings").val();
    var selected_pages_settings = $("#selected_pages_settings").val();
    var wp_board_id = $('#wp_board_id').val();
    var nonce = wp_roadmap_localize.nonce; 

    console.log("Data : ", title_settings, description_settings, wp_roadmap_status_settings, suggestion_settings, request_feature_link_settings, selected_pages_settings, wp_board_id);

    $.ajax({
        url: wp_roadmap_localize.ajaxurl,
        type: "post",
        data: {
            action: "save_feedback_roadmap_general_settings",
            title: title_settings,
            description: description_settings,
            wp_roadmap_status: wp_roadmap_status_settings,
            suggestion: suggestion_settings,
            request_feature_link: request_feature_link_settings,
            selected_pages: selected_pages_settings,
            wp_board_id: wp_board_id,
            _ajax_nonce: nonce, 
        },
        success: function (response) {
            if (response.success) {
                Swal.fire({
                    icon: "success",
                    title: "Settings have been saved!",
                    showConfirmButton: false,
                    timer: 2000,
                });
            } else {
                Swal.fire({
                    icon: "error",
                    title: "Oops...",
                    text: response.data || "Something went wrong!",
                });
            }
        },
        error: function () {
            _this.html("Save Changes");
            Swal.fire({
                icon: "error",
                title: "Oops...",
                text: "Something went wrong!",
            });
        },
    });
});

  // Saving Feedback Data With Ajax Request
  $(".wp-feedback-data-save").on("click", function (event) {
    event.preventDefault();
    var _this = $(this);
    _this.prop('disabled', true);
    var valid = true;
    if (!$("#title").val()) {
        $(".input_validation").css("border", "1px solid red");
        valid = false;
        _this.prop('disabled', false);
        return valid;
    } else {
        $(".input_validation").css("border", ""); 
    }

    $.ajax({
        url: wp_roadmap_localize.ajaxurl,
        type: "post",
        data: {
            action: "save_feedback_board_data",
            _ajax_nonce: wp_roadmap_localize.nonce,
            fields: $("form#wp_roadmap_feedback_add_form").serialize(),
        },
        success: function (response) {
            _this.html("Save Changes");
            $("#wp_roadmap_feedback_add_form")[0].reset();
            $("#wp-roadmap-feedback-modal").hide();

            if (response === true) {
                setTimeout(function () {
                    Swal.fire({
                        icon: "success",
                        title: "Feedback Task has been saved!",
                        showConfirmButton: false,
                        timer: 2000,
                    }).then(() => {
                        location.reload();
                    });
                }, 500);
            } else {
                Swal.fire({
                    icon: "error",
                    title: "Oops...",
                    text: "Something went wrong!",
                });
            }
        },
        error: function () {
            _this.html("Save Changes");
            Swal.fire({
                icon: "error",
                title: "Oops...",
                text: "Something went wrong!",
            });
        },
        complete: function () {
            _this.prop('disabled', false); 
        }
    });
});

$("#title").on("input", function () {
    if ($(this).val()) {
        $(".input_validation").css("border", ""); // Clear the validation message
        $(".wp-feedback-data-save").prop('disabled', false); // Re-enable the button if title is written
    }
});

  // Get Feedback Data With Ajax Request
  $(".wp-feedback-data-edit ").on("click", function (event) {
    event.preventDefault();
    var _this = $(this);
    var id = $(this).attr("data-value");
    $.ajax({
      url: wp_roadmap_localize.ajaxurl,
      type: "post",
      data: {
        action: "edit_feedback_board_data",
        _ajax_nonce: wp_roadmap_localize.nonce,
        id: id,
      },
      success: function (response) {
        $("#id").val(response.data.id);
        $("#title").val(response.data.title);
        $("#description").val(response.data.description);
        $("#wp_roadmap_status").val(response.data.status_id);
        $("#myModal").modal("show");
      },
    });
  });
  // Feedback Data Detail Modal
  $(".wp-feedback-data-detail").on("click", function (event) {
    event.preventDefault();
    var id = $(this).attr("data-value");
    $.ajax({
      url: wp_roadmap_localize.ajaxurl,
      type: "post",
      data: {
        action: "wp_feedback_detail",
        _ajax_nonce: wp_roadmap_localize.nonce,
        id: id,
      },
      success: function (response) {
        $("#wp_detail_feedback").text(response.data.title);
        $("#wp_detail_feedback_description").text(response.data.description);
        $("#wp_detail_feedback_status").text(response.data.status_id);
        $("#wp_detail_feedback_date").text(response.data.created_date);
        $("#wp_detail_feedback_upvote").text(response.data.total_upvote);
        $("#wp-detail").modal("show");
      },
    });
  });
  // Deleting Feedback Data With Ajax Request
  $(".wp-feedback-data-delete").on("click", function (event) {
    event.preventDefault();
    var feedback_id = $(this).attr("data-value");
    Swal.fire({
      title: "Are you sure you want to delete?",
      icon: "warning",
      showCancelButton: true,
      confirmButtonColor: "#3085d6",
      cancelButtonColor: "#d33",
      confirmButtonText: "Yes, delete it!",
    }).then((result) => {
      if (result.value) {
        $.ajax({
          url: wp_roadmap_localize.ajaxurl,
          type: "post",
          data: {
            action: "delete_feedback_board_data",
            _ajax_nonce: wp_roadmap_localize.nonce,
            id: feedback_id,
          },
          success: function (response) {
            if (response === true) {
              Swal.fire({
                icon: "success",
                title: "Feedback Task has been deleted!",
                showConfirmButton: false,
                timer: 2000,
              });
              setTimeout(function () {
                window.location.reload();
              }, 500);
            } else {
              Swal.fire({
                icon: "error",
                title: "Oops...",
                text: "Something went wrong!",
              });
            }
          },
          error: function () {
            Swal.fire({
              icon: "error",
              title: "Oops...",
              text: "Something went wrong!",
            });
          },
        });
      }
    });
  });

  // Reset Feedback Likes With Ajax Request
  $(".wp-feedback-data-reset").on("click", function (event) {
    event.preventDefault();
    var feedback_id = $(this).attr("data-value");
   // console.log("id : ", feedback_id)
    Swal.fire({
      title: "Are you sure you want to reset?",
      icon: "warning",
      showCancelButton: true,
      confirmButtonColor: "#3085d6",
      cancelButtonColor: "#d33",
      confirmButtonText: "Yes, reset it!",
    }).then((result) => {
      if (result.value) {
        $.ajax({
          url: wp_roadmap_localize.ajaxurl,
          type: "post",
          data: {
            action: "reset_feedback_board_data",
            _ajax_nonce: wp_roadmap_localize.nonce,
            id: feedback_id,
          },
          success: function (response) {
            if (response === true) {
              Swal.fire({
                icon: "success",
                title: "Feedback Task has been reset!",
                showConfirmButton: false,
                timer: 2000,
              });
              setTimeout(function () {
                window.location.reload();
              }, 2000);
            } else {
              Swal.fire({
                icon: "error",
                title: "Oops...",
                text: "Something went wrong!",
              });
            }
          },
          error: function () {
            Swal.fire({
              icon: "error",
              title: "Oops...",
              text: "Something went wrong!",
            });
          },
        });
      }
    });
  });

  jQuery(document).ready(function($) {
    $('#addNewItemForm').on('submit', function(e) {
        e.preventDefault();
        $("#addform").attr("disabled", "disabled");
        var itemName = $('#new_item_name').val();

        $.ajax({
            url: ajaxurl,
            type: 'POST',
            data: {
                action: 'wp_save_list',
                item_name: itemName,
            },
            success: function(response) {
                if (response.success) {
                    Swal.fire({
                        icon: "success",
                        title: "Board added successfully!",
                        showConfirmButton: false,
                        timer: 1000,
                    });
                    setTimeout(function() {
                        location.reload();
                    }, 1000); // Reload after 2 seconds
                } else {
                    Swal.fire({
                        icon: "error",
                        title: "Oops...",
                        text: response.data || "Failed to add item!",
                    });
                }
            },
            error: function() {
                Swal.fire({
                    icon: "error",
                    title: "Oops...",
                    text: "Something went wrong!",
                });
            }
        });
    });
});

// Like Button click insert
jQuery(document).ready(function($) {
  $('.like-button').on('click', function() {
      var button = $(this);
      var feedbackId = button.data('id');
      var visitorIp = button.data('ip');

      $.ajax({
          url: ajaxurl,
          type: 'POST',
          data: {
              action: 'wp_like_button_insert', 
              feedback_id: feedbackId,
              visitor_ip_address: visitorIp
          },
          success: function(response) {
              if (response.success) {
                  if (response.data.total_upvote == 1) {
                      button.addClass('active').show();
                  } else {
                      button.removeClass('active').show();
                  }
                  setTimeout(function() {
                    location.reload();
                }, 1);
              }
          }
      });
  });
});




})(jQuery);


