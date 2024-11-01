

    <div class="card iq-card">
        <div class="card-header">
            <div class="row">
                <div class="col-md-9">
                    <h3 class="iq-heading">
                        <?php esc_html_e('Add Status', 'wp-roadmap'); ?>
                    </h3>
                </div>
                <div class="col-md-3">
                    <a data-toggle="collapse" href="#statusFormCollapse" role="button" aria-expanded="true"
                        aria-controls="collapseExample" class="float-right feedback-button mr-0 text-center">
                        <?php esc_html_e('+', 'wp-roadmap'); ?>
                    </a>
                </div>
            </div>
        </div>
        <div class="card-body">
            <!-- Add Status Form -->
            <div class="col-md-12 mt-3">
                <div id="statusFormCollapse" class="collapse inner-status m-box-shadow inner-space">
                    <form id="wp_roadmap_feedback_status_form">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="name">
                                        <?php esc_html_e('Title', 'wp-roadmap'); ?>
                                    </label>
                                    <input name="name" type="text" id="name" value="<?php form_option('name'); ?>"
                                        class="input_validation form-control" required/>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="color">
                                        <?php esc_html_e('Select Color', 'wp-roadmap'); ?>
                                    </label>
                                    <input name="color" type="color" id="color" value="<?php form_option('color'); ?>"
                                        class="form-control" />
                                </div>
                            </div>
                            <input type="number" name="itemid" id="itemid" value="<?php echo $item_id ?>" hidden>
                            <div class="col-md-12 text-right">
                            <button type="button" class="btn btn-default" data-toggle="collapse"
                                    href="#statusFormCollapse">
                                    <?php esc_html_e('Cancel', 'wp-roadmap'); ?>
                                </button>
                                <button type="button" class="iq-button feedback-button wp-feedback-roadmap-save ">
                                    <?php esc_html_e('Save Status', 'wp-roadmap'); ?>
                                </button>

                            </div>
                        </div>
                    </form>
                </div>
            </div>
            <!-- End Add Status Form -->
            <!-- Status Listing -->
            <div class="col-md-12 mt-3">
                <ul id="wp-status-list">
                    <?php
                    $item_id = isset($_GET['item']) ? intval($_GET['item']) : 0;
                    global $wpdb;
                    $feedback_status = $wpdb->prefix . 'feedback_status';
                    $wp_feedback_status = $wpdb->get_results("SELECT * FROM {$feedback_status} WHERE item_id = {$item_id} ORDER BY `sequence_order` ASC"); ?>
                    <?php foreach ($wp_feedback_status as $wp_status) {
                        $wp_edit_data = $wpdb->get_row("SELECT * FROM {$feedback_status} WHERE id=" . $wp_status->id);
                        $color = esc_html($wp_edit_data->color);
                        $color = trim($color, " ");
                        ?>
                        <li class="row mt-10" data-feedback-id="<?php echo $wp_status->id; ?>">
                            <div class="col-md-12">
                                <div
                                    class="wp-roadmap-btn-hover media wow animated fadeInUp m-box-shadow  rounded border-primary-left cursor-move">
                                    <div class="media-body">
                                        <div class="d-flex align-items-center justify-content-between">
                                            <div class="iq-wrap d-flex align-items-center">
                                            <span class="dashicons dashicons-move"></span>
                                                <h3 class="mt-0 d-inline-block">
                                                    <?php echo $wp_status->title ?>
                                                </h3>
                                            </div>
                                            <p class="mb-0 float-right d-flex align-items-center wp-roadmap-status-btn">
                                                <a data-toggle="collapse" href="#editCollapse-<?php echo $wp_status->id ?>"
                                                    role="button" aria-expanded="true" aria-controls="collapseExample"><span
                                                        class="dashicons dashicons-edit-large"></span></a>
                                                <button type="button" class="wp-feedback-status-delete"
                                                    value="<?php echo esc_html($wp_status->id) ?>"><span
                                                        class="dashicons dashicons-trash"></span></button>
                                            </p>
                                        </div>
                                        <!-- Edit Status Form -->
                                        <div id="editCollapse-<?php echo $wp_status->id ?>" class="collapse inner-status">
                                            <form id="wp_roadmap_feedback_status_form_<?php echo $wp_status->id ?>">
                                                <div class="row">
                                                    <div class="col-md-6">
                                                        <input type="hidden" id="status_id" name="status_id"
                                                            value="<?php echo esc_html($wp_status->id); ?>" />
                                                        <div class="form-group">
                                                            <label for="inputName">
                                                                <?php esc_html_e('Title', 'wp-roadmap'); ?>
                                                            </label>
                                                            <input name="name" type="text" id="name" required
                                                                value="<?php echo esc_html($wp_edit_data->title); ?>"
                                                                class="form-control" />
                                                        </div>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <div class="form-group">
                                                            <label for="activetab">
                                                                <?php esc_html_e('Select Color', 'wp-roadmap'); ?>
                                                            </label>
                                                            <input name="color" type="color" id="color"
                                                                value="<?= $color; ?>" class="form-control" />
                                                        </div>
                                                    </div>
                                                    <div class="col-md-12 text-right">
                                                    <button type="button" class="btn btn-default" data-toggle="collapse"
                                                            href="#editCollapse-<?php echo $wp_status->id ?>">
                                                            <?php esc_html_e('Cancel', 'wp-roadmap'); ?>
                                                        </button>
                                                        <button type="button"
                                                            class="iq-button feedback-button wp-feedback-roadmap-update ">
                                                            <?php esc_html_e('Save Status', 'wp-roadmap'); ?>
                                                        </button>

                                                    </div>
                                                </div>
                                            </form>
                                        </div>
                                        <!-- End Edit Status Form -->
                                    </div>
                                </div>
                            </div>
                        </li>
                    <?php } ?>
                </ul>
            </div>
        </div>
    </div>
