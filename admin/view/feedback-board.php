<?php
require_once(RMPF_PATH . 'admin/view/feedback-detail.php');
global $wpdb;
$feedback_table = $wpdb->prefix . 'feedback';
$feedback_status = $wpdb->prefix . 'feedback_status';
$feedback_upvote = $wpdb->prefix . 'feedback_upvote';
$item_id = isset($_GET['item']) ? intval($_GET['item']) : 0;

$wp_feedback_status = $wpdb->get_results($wpdb->prepare("SELECT fs.*, (SELECT COUNT(id) FROM %i WHERE status_id=fs.id AND item_id=%s) as total_feedback FROM %i fs WHERE fs.item_id=%s ORDER BY fs.sequence_order ASC",$feedback_table,$item_id,$feedback_status,$item_id));
$wp_board_feedback_data = $wpdb->get_results($wpdb->prepare("SELECT s.id AS id, s.title, s.active_status, f.* FROM %i AS s LEFT JOIN %i AS f ON s.id = f.status_id AND f.item_id=%s WHERE s.item_id=%s ORDER BY s.sequence_order ASC, f.sequence_order ASC",$feedback_status,$feedback_table,$item_id,$item_id));
$wp_feedback_upvote = $wpdb->get_results($wpdb->prepare("SELECT * FROM %i",$feedback_upvote));
$ip = $_SERVER['REMOTE_ADDR'];

?>
<div class="wp-feedback-roadmap">
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <div class="box-content">
                    <div class="header">
                        <div class="d-flex align-items-center justify-content-between">
                            <div class="logo-detail d-flex align-items-center">
                                <div class="logo-title ml-2">
                                    <h3 class="header-title">
                                        <?php esc_html_e("WP Roadmap - Product Feedback Board", "wp-roadmap"); ?>
                                    </h3>
                                </div>
                            </div>
                            <div class="d-flex align-items-center feedback-add">
                                <div class="iq-settings-wrap">
                                    <a href="#" class="settings settings-btn" data-toggle="modal" data-target="#settings">
                                    <i class="dashicons dashicons-admin-generic"></i> Settings
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="content-page">
                        <div class="row">
                            <div class="col-md-12 track-info">
                                <?php if (empty($wp_feedback_status)) { ?>
                                    <p class="h3 m-auto add-status"><?php esc_html_e("ADD STATUS", "wp-roadmap"); ?></p>
                                <?php } else { ?>
                                    <?php foreach ($wp_feedback_status as $k => $item) { ?>
                                        <div class="box-detail mb-3">
                                            <div class="box-header d-flex align-items-center justify-content-between text-white"
                                                style="border-top: 3px solid <?php echo esc_attr($item->color) ?>">
                                                <div class="board-title">
                                                    <?php echo esc_html($item->title) ?> 
                                                    <span class="board-count" id="count-<?php echo esc_attr($item->id) ?>">
                                                        <?php esc_html_e($item->total_feedback) ?>
                                                    </span>
                                                </div>
                                                <span class="wp-feedback-status-delete" id="trashcolor" data-status-id="<?php echo esc_attr($item->id); ?>" style="cursor: pointer;color:#d3d3d3">
                                                    <i class="dashicons dashicons-trash iq-dashicons-trash"></i>
                                                </span>
                                                <div class="header-icon" onclick="openFeedBackModal(<?php echo esc_html($item->id) ?>)"> 
                                                    <span class="dashicons dashicons-plus iq-dashicons-plus"></span>
                                                </div>
                                            </div>
                                            <ul class="feedback-board" id="feedback-board<?php echo esc_attr($k++) ?>"
                                                data-board_id="<?php echo esc_html($item->id) ?>">
                                                <?php foreach ($wp_board_feedback_data as $data) {
                                                    if ($data->status_id == $item->id) { ?>
                                                        <li class="box-info" data-boardtask_id="<?php echo esc_html($data->id) ?>">
                                                            <div class="d-flex align-items-center justify-content-between">
                                                                <div class="feedback-title">
                                                                    <?php echo esc_html($data->title); ?>
                                                                </div>
                                                                <div class="dropdown action-btn">
                                                                    <span class="wp-feedback-data-detail icon" href="#wp-detail"
                                                                        data-toggle="modal" data-value=<?php echo esc_html($data->id) ?>><i class="dashicons dashicons-visibility"></i></span>
                                                                    <span class="wp-feedback-data-edit icon iq-wp-feedback-data-edit" href="#myModal"
                                                                        data-toggle="modal" data-value=<?php echo esc_html($data->id) ?>><i class="dashicons dashicons-edit"></i></span>
                                                                    <span class="wp-feedback-data-delete icon" href="#" data-value=<?php echo esc_html($data->id) ?>><i class="dashicons dashicons-trash"></i></span>
                                                                </div>
                                                            </div>
                                                            <p class="feedback-desc text-truncate">
                                                                <?php echo esc_html($data->description); ?>
                                                            </p>
                                                            <div class="icon d-flex align-items-center justify-content-between">
                                                                <div class="date">
                                                                    <span><i class="dashicons dashicons-calendar-alt"></i></span>
                                                                    <span>
                                                                        <?php esc_html_e(date("d M", strtotime($data->created_date))); ?>
                                                                    </span>
                                                                </div>
                                                                <div>
                                                                    <span class="wp-feedback-data-reset icon" 
                                                                        title="reset votes" data-value=<?php echo esc_html($data->id) ?>><i class="dashicons dashicons-update iq-dashicons-update align-bottom"></i></span>
                                                                        <span>
                                                                        <button id="like" type="button" class="like-button <?php echo $data->total_upvote == 1 ? 'active' : ''; ?>" data-id="<?php echo esc_html($data->id); ?>" data-ip="<?php echo esc_attr($ip); ?>">
                                                                            <i class="dashicons dashicons-thumbs-up align-bottom"></i>
                                                                        </button>
                                                                    </span>
                                                                    <span class="count">
                                                                        <?php esc_html_e($data->total_upvote) ?>
                                                                    </span>
                                                                </div>
                                                            </div>
                                                        </li>
                                                    <?php }
                                                } ?>
                                            </ul>
                                            <div>
                                                <span><a class="add-task" onclick="openFeedBackModal(<?php echo $item->id ?>)">
                                                    <?php esc_html_e('+ Add New', 'wp-roadmap'); ?>
                                                </a></span>
                                            </div>
                                        </div>
                                    <?php } ?>
                                <?php } ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>


<div class="modal fade" id="settings" tabindex="-1" role="dialog" aria-labelledby="ModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form method="POST" id="wp-feedback-roadmap-settings" name="wp-feedback-roadmap-settings">
                    <div class="wp-feedback-roadmap-main">
                        <div class="wp-feedback-roadmap-tabs wp-feedback-roadmap ">
                            <ul id="main">
                                <li class="tab-active"><a class="tab" href="#status"><?php esc_html_e("Manage Status", "wp-roadmap") ?></a></li>
                                <li><a class="tab" href="#general"><?php esc_html_e("General Settings", "wp-roadmap") ?></a></li>
                            </ul>
                            <hr>
                            <div class="wp-feedback-roadmap-tab">
                                <div id="status" class="wp-feedback-roadmap-tab-detail">
                                    <?php require_once(RMPF_PATH.'admin/view/status-settings.php'); ?>
                                </div>
                                <div id="general" class="wp-feedback-roadmap-tab-detail active">
                                    <?php require_once(RMPF_PATH.'admin/view/general-settings.php'); ?>
                                </div>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
