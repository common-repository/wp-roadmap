<?php
global $wpdb;
$feedback_table = $wpdb->prefix . 'feedback';
$feedback_status = $wpdb->prefix . 'feedback_status';
$feedback_upvote = $wpdb->prefix . 'feedback_upvote';


$ip = $_SERVER['REMOTE_ADDR'];
$wp_feedback_status = $wpdb->get_results("SELECT * from {$wp_feedback_status} WHERE id IN( SELECT DISTINCT(status_id) FROM `{$feedback_table}`) ORDER BY sequence_order DESC");
$wp_board_feedback_data = $wpdb->get_results("SELECT s.id AS id, s.title, s.active_status, f.*" . "FROM {$feedback_status} AS s " . "LEFT JOIN {$feedback_table} AS f ON s.id = f.status_id ");
$wp_most_voted_data = $wpdb->get_results("SELECT * FROM {$feedback_table} ORDER BY total_upvote DESC");
$wp_newest_feedback_data = $wpdb->get_results("SELECT * FROM {$feedback_table} ORDER BY id DESC");
$wp_get_active_tab = get_option('wp_feedback_roadmap_general_settings');

$string = '<div class="wp-roadmap">
    <div class="container">
        <div class="row">
            <div class="box-content wp-roadmap-box">
                <div class="header">
                    <h1 class="title wp-roadmap-title">' . esc_html($wp_get_active_tab['title']) . '</h1>
                    <p class="mb-0 wp-roadmap-description"> ' . esc_html($wp_get_active_tab['description']) . '</p>
                </div>
                <div class="content-page">
                <div class="tab-info d-flex align-items-center justify-content-between">
                    <ul class="tab list-inline m-0 p-0 d-flex align-items-center">
                        <li class="tablinks ' . (($wp_get_active_tab['wp_roadmap_status'] === 'roadmap') ? 'active' : "") . '"  onclick="openTab(event, \'tab-timeline\');" >' . esc_html__('Roadmap', 'wp-roadmap') . '</li>
                        <li class="tablinks ' . (($wp_get_active_tab['wp_roadmap_status'] === 'mostvoted') ? 'active' : "") . '"  onclick="openTab(event, \'tab-mostvoted\')">' . esc_html__('Most Voted', 'wp-roadmap') . '</li>
                        <li class="tablinks ' . (($wp_get_active_tab['wp_roadmap_status'] === 'newest') ? 'active' : '') . '"  onclick="openTab(event, \'tab-newest\')">' . esc_html__('Newest', 'wp-roadmap') . '</li>
                    </ul>
                </div>';

$string .= '<div id="tab-timeline" class="tabcontent tab-roadmap ' . (($wp_get_active_tab['wp_roadmap_status'] === 'roadmap') ? 'tabfade' : '') . '">
                        <ul class="iq-timeline list-inline p-0 m-0 position-relative">';
foreach ($wp_feedback_status as $data) {
    $i = 0;
    $string .= '<li class="mb-5">
                                    <div class="mb-5">
                                        <div class="timeline-dots timeline-dot-title" style="border-color: ' . $data->color . ';"></div>
                                        <small class="title">' . esc_html($data->title) . '</small>
                                    </div>
                                    <div id="wp-roadmap-loadmore-' . $data->id . '">';
    foreach ($wp_board_feedback_data as $feedback) {
        if ($feedback->status_id == $data->id) {
            $i++;
            $string .= '<div class="mb-3 timeline-title">
                                                    <div class="timeline-dots timeline-dot1" style="background: ' . esc_attr($data->color) . ';"></div>
                                                        <div id="wp-roadmap-loadmore-board-feedback-' . esc_attr($feedback->id) . '" class="wp-roadmap-loadmore " style="' . (($i > 5) ? 'display:none' : '') . ';" data-value="' . esc_attr($data->id) . '">
                                                            <h6>' . esc_html($feedback->title) . '<span>#' . esc_html($feedback->id) . '</span></h6>
                                                        </div>
                                                </div>';
        }
    }
    $string .= '</div>';
    if ($i > 5) {
        $string .= '<div class="view-more" data-value="' . esc_attr($data->id) . '">' . esc_html('View More', 'wp-roadmap') . '</div>';
    }
    $string .= '</li>';
}
$string .= '</ul>
                </div>';

$string .= '<div id="tab-mostvoted" class="tabcontent task-list tab-most-voted ' . (($wp_get_active_tab['wp_roadmap_status'] === 'mostvoted') ? 'tabfade' : '') . ' ">
                <ul class="list-inline p-0 m-0">';
foreach ($wp_most_voted_data as $voted) {
    $is_user_voted = $wpdb->get_results("SELECT * FROM {$feedback_upvote} WHERE visitor_ip_address = '" . $ip . "' AND feedback_id = '" . $voted->id . "' ");
    $is_voted = count($is_user_voted) > 0;
    $checked = $is_voted ? 'btn-active' : '';
    $string .= '<li>
                            <div class="d-flex align-items-top justify-content-between">
                                <div class="col-10">
                                    <h4 class="task-title">' . $voted->title . '<span id="wp_feedback_total_vote_' . esc_attr($voted->id) . '">#' . esc_html($voted->id) . '</span></h4>
                                    <p class="m-0 task-description">' . esc_html($voted->description) . '</p>
                                </div>
                                <div class="col-2 feedback-vote-btn text-right">
                                    <button type="button" id="wp_feedback_total_' . esc_attr($voted->id) . '" class="button-box wp_roadmap_add_upvote ' . esc_attr($checked) . '" data-ip="' . esc_attr($ip) . '" data-feedback-id="' . esc_attr($voted->id) . '">
                                        <i class="dashicons dashicons-arrow-up"></i>
                                        <i class="dashicons dashicons-saved"></i>
                                        <span id="feedback-upvote-count-vote-' . esc_attr($voted->id) . '">' . esc_html($voted->total_upvote) . '</span>
                                    </button>
                                </div>
                            </div>
                        </li>';
}
$string .= '</ul>
            </div>';

$string .= '<div id="tab-newest" class="tabcontent task-list tab-newest ' . (($wp_get_active_tab['wp_roadmap_status'] === 'newest') ? 'tabfade' : '') . '">
            <ul class="list-inline p-0 m-0">';
foreach ($wp_newest_feedback_data as $newest) {
    $is_user_voted = $wpdb->get_results("SELECT * FROM {$feedback_upvote} WHERE visitor_ip_address = '" . $ip . "' AND feedback_id = '" . $newest->id . "' ");
    $is_voted = count($is_user_voted) > 0;
    $checked = $is_voted ? 'btn-active' : '';

    $string .= '<li>
                        <div class="d-flex align-items-top justify-content-between">
                            <div class="col-10">
                                <h4 class="task-title">' . esc_attr($newest->title) . '<span id="wp_feedback_total_vote_new_' . esc_attr($newest->id) . '">#' . esc_html($newest->id) . '</span></h4>
                                <p class="m-0 task-description">' . esc_html($newest->description) . '</p>
                            </div>
                            <div class="col-2 feedback-vote-btn text-right">
                                <button type="button" id="wp_feedback_total_new_' . esc_attr($newest->id) . '" class="button-box wp_roadmap_add_upvote ' .esc_attr($checked) . '" data-ip="' . esc_attr($ip) . '" data-feedback-id="' . esc_attr($newest->id) . '">
                                    <i class="dashicons dashicons-arrow-up"></i>
                                    <i class="dashicons dashicons-saved"></i>
                                    <span id="feedback-upvote-count-' . esc_attr($newest->id) . '">' . esc_html($newest->total_upvote) . '</span>
                                </button>
                            </div>
                        </div>
                    </li>';
}
$string .= '</ul>
        </div>';

$string .= '</div>
        </div>
    </div>
</div>';
return $string;