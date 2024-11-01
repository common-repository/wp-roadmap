<?php
/**
 * Adds Youtube_Subs widget.
 */
class RMPF_Widget extends WP_Widget
{
    protected $scripts = array();
    protected $style = array();

    function __construct()
    {
        parent::__construct(
            // Base ID of your widget
            'rmpf_widget',
            // Widget name will appear in UI
            __('WP_Roadmap Widget', 'rmpf_widget'),
            // Widget description
            array('description' => __('Sample Feedback Roadmap Widget', 'rmpf_widget'), )
        );

        $options = get_option('wp_feedback_roadmap_general_settings');
        $request_feature_link = (!empty($options['request_feature_link'])) ? esc_html($options['request_feature_link']) : '';


        if (!empty($options) && !empty($options['pages'])) {
            $url = $_SERVER['REQUEST_URI'];
            $url_parts = explode('/', $url);
            $matches = array_intersect($options['pages'], $url_parts);
            if ((count($matches) > 0)) {
                wp_enqueue_script('widget_script', RMPF_URL . 'public/js/rmpf-public-widget.js', array('jquery'), RMPF_VERSION, true);
                wp_localize_script('widget_script', 'wp_roadmap_widget_localize', array('ajaxurl' => admin_url('admin-ajax.php'), 'nonce' => wp_create_nonce()));
                wp_enqueue_style('widget_css', RMPF_URL . 'public/css/rmpf-public-widget.css', array(), RMPF_VERSION);
                //wp_enqueue_style('bootstrap', RMPF_URL . 'admin/css/bootstrap.min.css', array(), RMPF_VERSION, 'all');
            }
        } else {
           // wp_enqueue_style('bootstrap', RMPF_URL . 'admin/css/bootstrap.min.css', array(), RMPF_VERSION, 'all');
            wp_enqueue_script('widget_script', RMPF_URL . 'public/js/rmpf-public-widget.js', array('jquery'), RMPF_VERSION, true);
            wp_localize_script('widget_script', 'wp_roadmap_widget_localize', array('ajaxurl' => admin_url('admin-ajax.php'), 'nonce' => wp_create_nonce()));
            wp_enqueue_style('widget_css', RMPF_URL . 'public/css/rmpf-public-widget.css', array(), RMPF_VERSION);
        }
    }

    /**
     * Front-end display of widget.
     *
     * @see WP_Widget::widget()
     *
     * @param array $args     Widget arguments.
     * @param array $instance Saved values from database.
     */
    public function widget($args, $instance)
    {
        
    }

    public function widget_style($board_id='',$unique_id='')
{
    global $wpdb;
    $feedback_table = $wpdb->prefix . 'feedback';
    $feedback_status = $wpdb->prefix . 'feedback_status';
    $feedback_upvote = $wpdb->prefix . 'feedback_upvote';
    $general_setting = $wpdb->prefix . 'general_setting';

    $ip = $_SERVER['REMOTE_ADDR'];
    $wp_feedback_status = $wpdb->get_results("SELECT * FROM {$feedback_status} WHERE item_id={$board_id} AND id IN (SELECT DISTINCT(status_id) FROM {$feedback_table}) ORDER BY sequence_order");
    $wp_board_feedback_data = $wpdb->get_results("SELECT s.id AS id, s.title, s.active_status, f.*" . "FROM {$feedback_status} AS s " . "LEFT JOIN {$feedback_table} AS f ON s.id = f.status_id ORDER BY sequence_order");
    $wp_most_voted_data = $wpdb->get_results("SELECT * FROM {$feedback_table} WHERE item_id={$board_id} ORDER BY total_upvote DESC");
    $wp_newest_feedback_data = $wpdb->get_results("SELECT * FROM {$feedback_table} WHERE item_id={$board_id} ORDER BY id DESC");
    $wp_general_setting_data = $wpdb->get_results("SELECT * FROM {$general_setting} WHERE item_id={$board_id}");

    if (!empty($wp_general_setting_data)) {
        $link = $wp_general_setting_data[0]->link;
    }
    $wp_get_active_tab = get_option('wp_feedback_roadmap_general_settings'); 

    //$link = !empty($wp_get_active_tab['request_feature_link']) ? $wp_get_active_tab['request_feature_link'] : '';
    $display_button = !empty($link) ? '' : 'display:none';

    wp_enqueue_style('bootstrap', RMPF_URL . 'admin/css/bootstrap_modifier.css', array(), RMPF_VERSION, 'all');

?>
<div class="wp-roadmap" id="wp-roadmap-<?php echo esc_attr($unique_id); ?>">
    <div class="container">
        <div class="row">
            <div class="box-content wp-roadmap-box">
                <?php if (!empty($wp_general_setting_data)) {
                    foreach ($wp_general_setting_data as $item) {
                        $title = isset($item->title) ? $item->title : '';
                        $description = isset($item->description) ? $item->description : '';
                        ?>
                        <?php if(!(empty($title) && empty($description)) ) : ?>
                <div class="header wp-roadmap-content">
                    <h1 class="title wp-roadmap-title">
                        <?php echo esc_html($title); ?>
                    </h1>
                    <p class="mb-0 wp-roadmap-description">
                        <?php echo esc_html($description); ?>
                    </p>
                </div>
                <?php endif ; ?>
                <?php
                    }
                } else { ?>
                <div class="header">
                    <h1 class="title wp-roadmap-title">
                        <?php 
                            if (is_array($wp_get_active_tab) && isset($wp_get_active_tab['title'])) {
                                echo esc_html($wp_get_active_tab['title']);
                            } else {
                                esc_html_e('Default Title'); 
                            }
                        ?>
                    </h1>
                    <p class="mb-0 wp-roadmap-description">
                        <?php 
                            if (is_array($wp_get_active_tab) && isset($wp_get_active_tab['description'])) {
                                echo esc_html($wp_get_active_tab['description']);
                            } else {
                                esc_html_e('Default Description'); 
                            }
                            ?>
                    </p>
                </div>
                <?php } ?>
                <div class="content-page">
                    <div class="tab-info">
                        <ul class="tab list-inline m-0 p-0 d-flex align-items-center">
                        <li class="tablinks roadmap-tab active" onclick="openTab(event, 'tab-timeline-<?php echo esc_attr($unique_id); ?>','<?php echo esc_attr($unique_id); ?>')">
                            <?php esc_html_e('Roadmap', 'wp-roadmap'); ?>
                        </li>
                        <li class="tablinks roadmap-tab" onclick="openTab(event, 'tab-mostvoted-<?php echo esc_attr($unique_id); ?>','<?php echo esc_attr($unique_id); ?>')">
                            <?php esc_html_e('Most voted', 'wp-roadmap'); ?>
                        </li>
                        <li class="tablinks roadmap-tab" onclick="openTab(event, 'tab-newest-<?php echo esc_attr($unique_id); ?>','<?php echo esc_attr($unique_id); ?>')">
                            <?php esc_html_e('Newest', 'wp-roadmap'); ?>
                        </li>

                        </ul>
                        <?php if (!empty($link)) { ?>
                        <a href="<?php echo esc_attr($link); ?>" type="button" class="button iq-feature-link"
                            id="feature_link" target="_blank" style="<?php echo esc_attr($display_button); ?>">
                            <?php esc_html_e('Feature Request', 'wp-roadmap'); ?>
                        </a>
                        <?php } ?>
                    </div>

                    <div id="tab-timeline-<?php echo esc_attr($unique_id); ?>" class="tabcontent tab-roadmap tabfade">
                        <ul class="iq-timeline list-inline p-0 m-0 position-relative">
                            <?php foreach ($wp_feedback_status as $data) {
                                    $i = 0;
                                    ?>
                            <li >
                            <h2 class="title iq-heading">
                                            
                                            <?php esc_html_e($data->title); ?>
                                        </h2>
                                <ul class="iq-timeline-wrap">
                                    
                                    <li id="wp-roadmap-loadmore-<?php echo esc_attr($data->id); ?>">
                                        <?php 
                                            foreach ($wp_board_feedback_data as $feedback) {
                                                if ($feedback->status_id == $data->id) {
                                                    $i++;
                                                    ?>
                                        <div class=" timeline-title">
                                        <span class="timeline-dots timeline-dot-title"
                                        style="border-color: <?php echo esc_attr($data->color); ?>;"></span>
                                            <div id="wp-roadmap-loadmore-board-feedback-<?php echo esc_attr($feedback->id); ?>"
                                                class="wp-roadmap-loadmore"
                                                style="<?php echo ($i > 5) ? 'display:none' : ''; ?>"
                                                data-value="<?php echo esc_attr($data->id); ?>">
                                                <div class="iq-feedback-title">
                                                    <?php echo esc_html($feedback->title); ?>
                                                    <span>
                                                        #<?php esc_html_e($i); ?>
                                                    </span>
                                                </div>
                                            </div>
                                        </div>
                                        <?php }
                                            } ?>
                                    </li>

                                    <?php if ($i > 5) { ?>
                                <div class="view-more" data-value="<?php echo esc_attr($data->id); ?>">
                                    <?php esc_html_e('View More', 'wp-roadmap'); ?>
                                </div>
                                <?php } ?>
                                </ul>
                            </li>
                            <?php } ?>
                        </ul>
                    </div>
                    <div id="tab-mostvoted-<?php echo esc_attr($unique_id); ?>" class="tabcontent task-list tab-most-voted">
                        <ul class="list-inline p-0 m-0">
                            <?php foreach ($wp_most_voted_data as $voted) {
                                    $is_user_voted = $wpdb->get_results("SELECT * FROM {$feedback_upvote} WHERE visitor_ip_address = '" . $ip . "' AND feedback_id = '" . $voted->id . "' ");
                                    $is_voted = count($is_user_voted) > 0;
                                    $checked = ($is_voted > 0) ? 'btn-active' : '';
                                    ?>
                            <li class="iq-most-voted-list-item">
                                <div class="d-flex align-items-top justify-content-between">
                                    <div class=" col-9 col-md-10 pl-3 pl-sm-4">
                                        <ul class="p-0">
                                            <li>
                                                <h4 class="task-title">
                                                    <span
                                                        class="iq-most_title"><?php echo esc_html($voted->title); ?></span>

                                                    <span
                                                        class="task-most-id"
                                                        id="wp_feedback_total_vote_<?php echo esc_attr($voted->id); ?>">#
                                                        <?php esc_html_e($voted->id); ?>
                                                    </span>
                                                </h4>
                                                <?php if ($voted->description) {
                                                    ?>
                                                    <p class="m-0 task-description">
                                                        <?php esc_html_e($voted->description); ?>
                                                    </p>
                                                    <?php
                                                }?>
                                            </li>
                                        </ul>
                                    </div>
                                    <div class=" col-3 col-md-2 px-sm-auto px-0 feedback-vote-btn text-right">
                                        <button type="button" id="wp_feedback_total_most<?php echo esc_attr($voted->id); ?>"
                                            class="button-box wp_roadmap_add_upvote <?php echo esc_attr($checked); ?>"
                                            data-ip="<?php echo esc_attr($ip); ?>"
                                            data-total_vote = "<?php esc_html_e($voted->total_upvote); ?>"
                                            data-feedback-id="<?php echo esc_attr($voted->id); ?>">
                                            <i class="dashicons dashicons-arrow-up pr-2"></i>
                                            <i class="dashicons dashicons-saved pr-2"></i>
                                            <span class="wp_roadmap_vote_count">
                                                <?php esc_html_e($voted->total_upvote); ?>
                                            </span>
                                        </button>
                                    </div>
                                </div>
                            </li>
                            <?php } ?>
                        </ul>
                    </div>
                    <div id="tab-newest-<?php echo esc_attr($unique_id); ?>" class="tabcontent tab-newest">
                        <ul class="list-inline p-0 m-0">
                            <?php foreach ($wp_newest_feedback_data as $newest) {
                                      $is_user_voted = $wpdb->get_results("SELECT * FROM {$feedback_upvote} WHERE visitor_ip_address = '" . $ip . "' and feedback_id = '" . $newest->id . "' ");
                                      $is_voted = count($is_user_voted) > 0;
                                      $checked = ($is_voted > 0) ? 'btn-active' : '';
                                    ?>
                            <li class="iq-newest-list-item">
                                <div class="d-flex align-items-top justify-content-between">
                                    <div class=" col-9 col-md-10">
                                        <ul class="p-0">
                                            <li>
                                                <h4 class="task-title">
                                                    <span class="iq-new-title">
                                                        <?php echo esc_html($newest->title); ?></span>
                                                    <span
                                                        class="task-new-id"
                                                        id="wp_feedback_total_vote_<?php echo esc_attr($newest->id); ?>">#
                                                        <?php esc_html_e($newest->id); ?>
                                                    </span>
                                                </h4>
                                                <p class="m-0 task-description">
                                                    <?php esc_html_e($newest->description); ?>
                                                </p>
                                    </div>
                                    <div class="col-3 col-md-2 px-sm-auto px-0 feedback-vote-btn text-right">
                                        <button type="button"
                                            id="wp_feedback_total_new<?php echo esc_attr($newest->id); ?>"
                                            data-total_vote = "<?php esc_html_e($newest->total_upvote); ?>"
                                            class="button-box wp_roadmap_add_upvote <?php echo esc_attr($checked); ?>"
                                            data-ip="<?php echo esc_attr($ip); ?>"
                                            data-feedback-id="<?php echo esc_attr($newest->id); ?>">
                                            <i class="dashicons dashicons-arrow-up pr-2"></i>
                                            <i class="dashicons dashicons-saved"></i>
                                            <span class="wp_roadmap_vote_count">
                                                <?php esc_html_e($newest->total_upvote); ?>
                                            </span>
                                        </button>
                                    </div>
                                </div>
                            </li>
                            <?php } ?>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<?php }

}