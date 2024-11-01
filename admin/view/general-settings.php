<?php
$options = get_option('wp_feedback_roadmap_general_settings');

$page_list = get_pages();

$pages_list1 = (isset($options['pages'])) ? $options['pages'] : array();

$suggestion = (isset($options['suggestion'])) ? esc_html($options['suggestion']) : '';

$request_feature_link = (isset($options['request_feature_link'])) ? esc_html($options['request_feature_link']) : '';
$item_id = isset($_GET['item']) ? intval($_GET['item']) : 0;

global $wpdb;
$general_setting_table = $wpdb->prefix . 'general_setting';
$item_id = isset($_GET['item']) ? intval($_GET['item']) : 0;
$wp_general_setting = $wpdb->get_row($wpdb->prepare("SELECT * from {$general_setting_table} WHERE item_id = %d", $item_id), ARRAY_A);

?>

<div class="wrap m-4">
    <form method="post" id="feedback-settings-form">
        <table class="form-table" role="presentation">
            <tr>
                <th scope="row"><label for="title">
                        <?php esc_html_e('Title', 'wp-roadmap'); ?>
                    </label></th>
                <td>
                    <input name="title" type="text" id="title_settings" value="<?php echo isset($wp_general_setting['title']) ? esc_attr($wp_general_setting['title']) : ''; ?>"
                        class="wp-feedback-input" />
                    <p class="description">
                       <span class="iq-note">Note:</span> <?php esc_html_e(' Leave empty if you dont want show in the frontend', 'wp-roadmap'); ?>
                    </p>
                </td>
            </tr>
            <tr>
                <th scope="row"><label for="description">
                        <?php esc_html_e('Description', 'wp-roadmap'); ?>
                    </label></th>
                <td>
                    <textarea name="description" id="description_settings" aria-describedby="description"
                        class="wp-feedback-input"
                        rows="5"><?php echo isset($wp_general_setting['description']) ? esc_attr($wp_general_setting['description']) : ''; ?></textarea>
                    <p class="description">
                    <span class="iq-note">Note:</span> <?php esc_html_e('Leave empty if you dont want show in the frontend ', 'wp-roadmap'); ?>
                    </p>
                </td>
            </tr>
            <tr>
                <th scope="row"><label for="wp_roadmap_status_settings">
                        <?php esc_html_e('Set Active Tab', 'wp-roadmap'); ?>
                    </label></th>
                <td>
                    <select name="wp_roadmap_status" id="wp_roadmap_status_settings" class="wp-feedback-input">
                    <option value="roadmap" <?php selected(isset($wp_general_setting['tab']) ? esc_attr($wp_general_setting['tab']) : '', 'roadmap'); ?> ><?php esc_html_e('Road Map', 'wp-roadmap'); ?></option>
                        <option value="newest" <?php selected(isset($wp_general_setting['tab']) ? esc_attr($wp_general_setting['tab']) : '', 'newest'); ?>><?php esc_html_e('Newest', 'wp-roadmap'); ?></option>
                        <option value="mostvoted" <?php selected(isset($wp_general_setting['tab']) ? esc_attr($wp_general_setting['tab']) : '', 'mostvoted'); ?>><?php esc_html_e('Most Vote', 'wp-roadmap'); ?></option>
                    </select>

                </td>
            </tr>
            <tr>
                <th scope="row"><label for="suggestion_settings">
                        <?php esc_html_e('Suggestion', 'wp-roadmap'); ?>
                    </label></th>
                <td>
                    <input name="suggestion" type="text" id="suggestion_settings" value="<?php echo isset($wp_general_setting['suggestion']) ? esc_attr($wp_general_setting['suggestion']) : ''; ?>"
                        class="wp-feedback-input" />
                    <p class="description">
                    <span class="iq-note">Note:</span> <?php esc_html_e('Leave empty if you dont want show in the frontend ', 'wp-roadmap'); ?>
                    </p>
                </td>
            </tr>
            <tr>
                <th scope="row"><label for="request_feature_link_settings">
                        <?php esc_html_e('Request Feature Link', 'wp-roadmap'); ?>
                    </label></th>
                <td>
                    <input name="request_feature_link" type="text" id="request_feature_link_settings"
                        value="<?php echo isset($wp_general_setting['link']) ? esc_attr($wp_general_setting['link']) : ''; ?>" class="wp-feedback-input" />
                    <p class="description">
                    <span class="iq-note">Note:</span> <?php esc_html_e('Leave empty if you dont want show in the frontend', 'wp-roadmap'); ?>
                    </p>
                </td>
            </tr>
            <tr>
                <th scope="row"><label for="selected_pages_settings">
                        <?php esc_html_e('Select Pages', 'wp-roadmap'); ?>
                    </label></th>
                <td>
                <select name="selected_pages" id="selected_pages_settings" class="wp-feedback-input" multiple>
                        <?php
                        foreach ($page_list as $key => $value) {

                            if (in_array("$value->post_name", $pages_list1)) {
                                echo '<option value="' . $value->post_name . '" selected>' . $value->post_title . '</option>';
                            } else {
                                echo '<option value="' . $value->post_name . '">' . $value->post_title . '</option>';
                            }

                        }
                        ?>
                    </select>
                    <p class="description">
                    <span class="iq-note">Note:</span> <?php esc_html_e('Select pages on which you want to use the roadmap', 'wp-roadmap'); ?>
                    </p>
                    <input type="text" name="wp_board_id" id="wp_board_id" value="<?php echo esc_attr($item_id); ?>"hidden />
                </td>
            </tr>
        </table>
        <button type="submit" class="iq-button feedback-button feedback-general-setting-save">
            <?php esc_html_e('Save Changes', 'wp-roadmap'); ?>
        </button>
    </form>
</div>
