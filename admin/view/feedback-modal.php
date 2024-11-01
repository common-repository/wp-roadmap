<?php
$item_id = isset($_GET['item']) ? intval($_GET['item']) : 0;
?>
<div class="bs-example">
    <!-- Feedback Modal -->
    <div id="myModal" class="modal fade">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header display">
                    <h4 class="modal-title">
                        <?php esc_html_e('Add Feedback', 'wp-roadmap'); ?>
                    </h4>
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                </div>
                <div class="modal-body">
                    <form id="wp_roadmap_feedback_add_form">
                        <input name="id" type="hidden" id="id" value="<?php form_option('id'); ?>"
                            class="form-control" />
                        <div class="form-group">
                            <label for="inputName">
                                <?php esc_html_e('Title', 'wp-roadmap'); ?>
                            </label>
                            <input name="title" type="text" id="title" value="<?php form_option('title'); ?>"
                                class="form-control  input_validation" />
                        </div>
                        <div class="form-group">
                            <label for="description">
                                <?php esc_html_e('Description', 'wp-roadmap'); ?>
                            </label>
                            <textarea name="description" id="description" rows="6" aria-describedby="description"
                                value="<?php form_option('description'); ?>" class="form-control "></textarea>
                        </div>
                        <div class="form-group">
                            <label for="activetab">
                                <?php esc_html_e('Select Status', 'wp-roadmap'); ?>
                            </label>
                            <select name="wp_roadmap_status" id="wp_roadmap_status" class="form-control select">
                                <?php
                                global $wpdb;
                                $feedback_status = $wpdb->prefix . 'feedback_status';
                                $wp_feedback_status = $wpdb->get_results("SELECT * FROM {$feedback_status} WHERE item_id={$item_id}");
                                foreach ($wp_feedback_status as $wp_status) {
                                    ?>
                                    <option value=<?php echo esc_html($wp_status->id) ?>><?php esc_html_e($wp_status->title) ?></option>
                                <?php } ?>
                            </select>
                            <input type="number" name="wp_list_id" id="wp_list_id" value="<?php echo $item_id ?>" hidden>
                        </div>
                    </form>
                </div>
                <div class="modal-footer wp-feedback-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">
                        <?php esc_html_e('Cancel', 'wp-roadmap'); ?>
                    </button>
                    <button type="submit" class="feedback-button iq-button wp-feedback-data-save " id ="feedback_button ">
                        <?php esc_html_e('Save Changes', 'wp-roadmap'); ?>
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>