<?php
function rmpf_add_upvote () {
        global $wpdb;
        $feedback_table = $wpdb->prefix.'feedback';
        $feedback_status = $wpdb->prefix.'feedback_status';
        $table_name = $wpdb->prefix . "feedback_upvote";

        $ip =sanitize_text_field($_POST['visitor_ip_address']);
        $feedback_id = sanitize_text_field($_POST['feedback_id']);
        $wp_get_data = $wpdb->get_row("SELECT * FROM {$table_name} WHERE visitor_ip_address = '".$ip."' AND feedback_id = '$feedback_id'");

        if(count((array)$wp_get_data) > 0){
            $wp_delete_record = $wpdb->delete( $table_name, array( 'id' => $wp_get_data->id ) );
            if($wp_delete_record){
                $wpdb->query("UPDATE {$feedback_table} SET total_upvote = total_upvote - 1 WHERE id = '".$feedback_id."'");
                $data = $wpdb->get_row("SELECT * FROM {$feedback_table} WHERE id = '$feedback_id'");
                wp_send_json_success($data);
            }
        }else{
            $insert_result = $wpdb->insert($table_name, array ('feedback_id' => $_POST['feedback_id'],'visitor_ip_address'=> $_POST['visitor_ip_address']));
            if($insert_result !== NULL){
                $wpdb->query("UPDATE {$feedback_table} SET total_upvote = total_upvote + 1 WHERE id = '".$feedback_id."'");
                $data = $wpdb->get_row("SELECT * FROM {$feedback_table} WHERE id = '$feedback_id'");
                wp_send_json_success($data);
            }
        }
}
add_action( 'wp_ajax_rmpf_add_upvote', 'rmpf_add_upvote' );
add_action( 'wp_ajax_nopriv_rmpf_add_upvote', 'rmpf_add_upvote' );