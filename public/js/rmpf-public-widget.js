(function( $ ) {
    'use strict';

    //Toggle Upvote Button in Frontend Widget
    $('.button-box').click(function(){
        $(this).toggleClass('btn-active');
    });

    $(document).on('click', '.view-more', function(){
        const loadmore = $(this).attr('data-value')
        const wrapper = $(this).closest("li").find("#wp-roadmap-loadmore-"+loadmore)
        const showing =  wrapper.find('.wp-roadmap-loadmore:visible').length;
        wrapper.find('.wp-roadmap-loadmore').slice(showing - 1, showing + 5).slideDown();
        if(wrapper.find('.wp-roadmap-loadmore:hidden').length == 0) {
            $(this).hide()
        }
    })
   
    //Ajax Request for Upvote Adding Functionality.
    $(document).on("click",".wp_roadmap_add_upvote", function (event) { 
        let upvoteBtn= event.currentTarget;
        var feedback_id = $(this).attr("data-feedback-id");
        var visitor_ip_address = $(this).attr("data-ip");
        var total_vote = $(this).attr("data-total_vote");
        $.ajax({
            url: wp_roadmap_widget_localize.ajaxurl,
            method: "POST",
            data: {
                action: "rmpf_add_upvote",
                _ajax_nonce: wp_roadmap_widget_localize.nonce,
                feedback_id:feedback_id,
                visitor_ip_address:visitor_ip_address
            },
            success: function (response) {
                $(upvoteBtn).find('.wp_roadmap_vote_count').text(response.data.total_upvote)
                // console.log((total_vote<response.data.total_upvote && $(upvoteBtn).find('.dashicons-saved').length === 0 ));
                // if(total_vote<response.data.total_upvote && $(upvoteBtn).find('.dashicons-saved').length === 0 ){
                if(total_vote<response.data.total_upvote){
                    $(upvoteBtn).find('.dashicons-saved').css('display','inline');
                    $(upvoteBtn).find('.dashicons-arrow-up').css('display','none');
                }
                // console.log((total_vote==response.data.total_upvote && $(upvoteBtn).find('.dashicons-arrow-up').length === 0));
                // if(total_vote==response.data.total_upvote && $(upvoteBtn).find('.dashicons-arrow-up').length === 0){
                if(total_vote==response.data.total_upvote){
                    // $(upvoteBtn).append('<i class="dashicons  dashicons-arrow-up pr-2"></i>');
                    $(upvoteBtn).find('.dashicons-saved').css('display','none');
                    $(upvoteBtn).find('.dashicons-arrow-up').css('display','inline');
                    // $(upvoteBtn).find('.dashicons-arrow-up').style = 'display:flex !important';
                }
            }
        });
    });
    $(document).on('click', function(event) {
        if (!$(event.target).closest('[id^="wp_feedback_total_new"]').length) {
            $('[id^="wp_feedback_total_new"]').removeClass('btn-active');
        }
        if (!$(event.target).closest('[id^="wp_feedback_total_most"]').length) {
            $('[id^="wp_feedback_total_most"]').removeClass('btn-active');
        }
    });
    $(document).on('click', '[id^="wp_feedback_total_new"]', function() {
        $('[id^="wp_feedback_total_new"]').removeClass('btn-active');
        var feedbackId = $(this).data('feedback-id');
        var buttonId = '#wp_feedback_total_new' + feedbackId;
        $(buttonId).addClass('btn-active');
    });
    $(document).on('click', '[id^="wp_feedback_total_most"]', function() {
        $('[id^="wp_feedback_total_most"]').removeClass('btn-active');
        var feedbackId = $(this).data('feedback-id');
        var buttonId = '#wp_feedback_total_most' + feedbackId;
        $(buttonId).addClass('btn-active');
    });
    
})( jQuery );

//Frontend Widget Tab Active Js.
    // function openTab(evt, cityName) {
    //     var i, tabcontent, tablinks;
    //     tabcontent = document.getElementsByClassName("tabcontent");
    //     for (i = 0; i < tabcontent.length; i++) {
    //         tabcontent[i].style.display = "none";
    //     }
    //     tablinks = document.getElementsByClassName("tablinks");
    //     for (i = 0; i < tablinks.length; i++) {
    //         tablinks[i].className = tablinks[i].className.replace(" active", "");
    //     }
    //     document.getElementById(cityName).style.display = "block";
    //     evt.currentTarget.className += " active";
    // }

    function openTab(evt, tabName, uniqueId) {
        var widget = document.getElementById("wp-roadmap-" + uniqueId);
        var tabcontent = widget.getElementsByClassName("tabcontent");
        var tablinks = widget.getElementsByClassName("tablinks");
    
        // Hide all tab contents within the same widget
        for (var i = 0; i < tabcontent.length; i++) {
            tabcontent[i].style.display = "none";
        }
    
        // Remove the 'active' class from all tab links within the same widget
        for (var i = 0; i < tablinks.length; i++) {
            tablinks[i].className = tablinks[i].className.replace(" active", "");
        }
    
        // Display the current tab and add the 'active' class to the clicked tab link
        document.getElementById(tabName).style.display = "block";
        evt.currentTarget.className += " active";
    }
    