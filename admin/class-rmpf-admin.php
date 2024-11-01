<?php

/**
 * The admin-specific functionality of the plugin.
 *
 * @link       https://iqonic.design/
 * @since     1.0.7
 *
 * @package    Rmpf
 * @subpackage Rmpf/admin
 */

/**
 * The admin-specific functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 *
 * @package    Rmpf
 * @subpackage Rmpf/admin
 * @author     Iqonic Design <hello@iqonic.design>
 */
class Rmpf_Admin
{

    /**
     * The ID of this plugin.
     *
     * @since   1.0.7
     * @access   private
     * @var      string    $plugin_name    The ID of this plugin.
     */
    private $plugin_name;

    /**
     * The version of this plugin.
     *
     * @since   1.0.7
     * @access   private
     * @var      string    $version    The current version of this plugin.
     */
    private $version;

    /**
     * Initialize the class and set its properties.
     *
     * @since   1.0.7
     * @param      string    $plugin_name       The name of this plugin.
     * @param      string    $version    The version of this plugin.
     */
    public function __construct($plugin_name, $version)
    {
        $this->plugin_name = $plugin_name;
        $this->version = $version;
            add_action('admin_head', function() {
                echo '<style>
                    #toplevel_page_wp_roadmap_feedback ul li:nth-child(3) {
                        display: none;
                    }
                </style>';
            });
    }

    /**
     * Register the stylesheets for the admin area.
     *
     * @since   1.0.7
     */
    public function enqueue_styles()
    {
        /**
         * This function is provided for demonstration purposes only.
         *
         * An instance of this class should be passed to the run() function
         * defined in Rmpf_Loader as all of the hooks are defined
         * in that particular class.
         *
         * The Rmpf_Loader will then create the relationship
         * between the defined hooks and the functions defined in this
         * class.
         */
        if (is_admin()) {
            if (isset($_REQUEST['page']) && ($_REQUEST['page'] === "wp_roadmap_feedback_dashboard" || $_REQUEST['page'] === "wp_roadmap_settings" || $_REQUEST['page'] === "wp_roadmap_list")) {
                wp_enqueue_style('bootstrap', plugin_dir_url(__FILE__) . 'css/bootstrap.min.css', array(), $this->version, 'all');
                wp_enqueue_style('sweetalert2', plugin_dir_url(__FILE__) . 'css/dragula.css', array(), $this->version, 'all');
                wp_enqueue_style('dragula', plugin_dir_url(__FILE__) . 'css/sweetalert2.min.css', array(), $this->version, 'all');
                wp_enqueue_style($this->plugin_name, plugin_dir_url(__FILE__) . 'css/rmpf-admin.css', array(), $this->version, 'all');
            }
        }
    }

    /**
     * Register the JavaScript for the admin area.
     *
     * @since   1.0.7
     */
    public function enqueue_scripts()
    {

        /**
         * This function is provided for demonstration purposes only.
         *
         * An instance of this class should be passed to the run() function
         * defined in Rmpf_Loader as all of the hooks are defined
         * in that particular class.
         *
         * The Rmpf_Loader will then create the relationship
         * between the defined hooks and the functions defined in this
         * class.
         */
        if (is_admin()) {
            if (isset($_REQUEST['page']) && ($_REQUEST['page'] === "wp_roadmap_feedback_dashboard" || $_REQUEST['page'] === "wp_roadmap_settings" || $_REQUEST['page'] === "wp_roadmap_list")) {
                global $wpdb;
                $feedback_table = $wpdb->prefix . 'feedback';
                $feedback_status = $wpdb->prefix . 'feedback_status';
                $wp_feedback_data = $wpdb->get_results("SELECT s.id AS id, s.title, s.active_status, f.*" . "FROM {$feedback_status} AS s " . "LEFT JOIN {$feedback_table} AS f ON s.id = f.status_id " .
                    "WHERE s.active_status = '1' ");
                $script_params = array('feedback_data' => $wp_feedback_data);
                wp_enqueue_script('sweetalert2', plugin_dir_url(__FILE__) . 'js/sweetalert2.min.js', array('jquery'), $this->version, false);
                wp_enqueue_script('dragula', plugin_dir_url(__FILE__) . 'js/dragula.min.js', array('jquery'), $this->version, false);
                wp_enqueue_script('bootstrap', plugin_dir_url(__FILE__) . 'js/bootstrap.min.js', array('jquery'), $this->version, true);
                wp_enqueue_script('rmpf-admin', plugin_dir_url(__FILE__) . 'js/rmpf-admin.js', array('jquery'), $this->version, true);
               
                wp_localize_script('rmpf-admin', 'wp_roadmap_localize', array('ajaxurl' => admin_url('admin-ajax.php'), 'nonce' => wp_create_nonce()));
                wp_enqueue_script('rmpf-custom', plugin_dir_url(__FILE__) . 'js/rmpf-custom.js', array('jquery', 'jquery-ui-sortable'), $this->version, true);
                wp_localize_script('rmpf-custom', 'scriptParams', $script_params);
                wp_localize_script('rmpf-custom', 'wp_update', array('ajaxurl' => admin_url('admin-ajax.php'), 'nonce' => wp_create_nonce()));
            }
        }
    }

    //Adding plugin in sidebar.
    public function admin_menu()
    {
        add_menu_page(
            'WP Roadmap',
            'WP Roadmap',
            'rmpf-lang',
            'wp_roadmap_feedback',
            'manage_options',
            plugin_dir_url(__DIR__) . 'admin/images/icon.svg',
            60
        );
        add_submenu_page(
            'wp_roadmap_feedback',
            __('List of Board', 'wp-roadmap'),
            __('List of Board', 'wp-roadmap'),
            'manage_options',
            'wp_roadmap_list',
            [$this, 'rmpf_list']
        );
        add_submenu_page(
            'wp_roadmap_feedback',
            __('', 'wp-roadmap'),
            __('', 'wp-roadmap'),
            'manage_options',
            'wp_roadmap_feedback_dashboard',
            [$this, 'rmpf_feedback_dashboard']
        );
    }

    //Feedback status insert functionality.
    public function rmpf_status_save()
{
    global $wpdb;
    $table_name = $wpdb->prefix . "feedback_status";

    if (
        isset($_POST['action'])
        && isset($_POST['_ajax_nonce'])
        && wp_verify_nonce($_POST['_ajax_nonce'])
    ) {
        if (!empty($_POST['itemid'])) {
            $sql = "SELECT sequence_order FROM {$table_name} ORDER BY sequence_order DESC limit 0,1";
            $wp_get_feedback_status = $wpdb->get_var($sql);

            $wp_feedback_status_data = array(
                'title' => sanitize_text_field(esc_attr($_POST['name'])),
                'sequence_order' => $wp_get_feedback_status + 1,
                'color' => sanitize_text_field(esc_attr($_POST['color'])),
                'item_id' => sanitize_text_field(esc_attr($_POST['itemid'])),
            );

            $insert_result = $wpdb->insert($table_name, $wp_feedback_status_data);
            if ($insert_result) {
                wp_send_json(true);
            } else {
                wp_send_json(false); 
            }
        } else {
            wp_send_json(false); 
        }
    } else {
        wp_send_json(false); 
    }
}


    //Feedback status update functionality.
    public function rmpf_status_update()
    {
        global $wpdb;
        $table_name = $wpdb->prefix . "feedback_status";
        if (
            isset($_POST['action'])
            && isset($_POST['_ajax_nonce']) && wp_verify_nonce($_POST['_ajax_nonce'])
        ) {
            parse_str($_POST['fields'], $settings);
            sanitize_text_field($settings);

            if (!isset($_POST['fields'])) {
                wp_send_json(false);
            }

            $name = !empty($settings['name']) ? sanitize_text_field(esc_attr($settings['name'])) : '';
            $color = !empty($settings['color']) ? sanitize_text_field(esc_attr($settings['color'])) : '';

            try {
                $updated = $wpdb->update($table_name, array('title' => $name, 'color' => $color), array('id' => $settings['status_id']));
                wp_send_json(true);
            } catch (Exception $e) {
                wp_send_json(false);
            }
        }
    }

    //Update status oder
    public function rmpf_status_order()
    {
        global $wpdb;
        $table_name = $wpdb->prefix . "feedback_status";
        if (
            isset($_POST['action'])
            && isset($_POST['_ajax_nonce'])
            && wp_verify_nonce($_POST['_ajax_nonce'])
        ) {
            $wp_status_order = isset($_POST["status_order_ids"]) ? array_map('sanitize_text_field', $_POST["status_order_ids"]) : [];
            if (count($wp_status_order) > 0) {
                for ($order_no = 0; $order_no < count($wp_status_order); $order_no++) {
                    $wpdb->query("UPDATE {$table_name} SET sequence_order = '" . ($order_no + 1) . "' WHERE id = '" . $wp_status_order[$order_no] . "'");
                }
                wp_send_json(true);
            } else {
                wp_send_json(false);
            }
        }
    }

    //Feedback status delete functionality.
    public function rmpf_status_delete()
    {
        global $wpdb;
        $feedback_table = $wpdb->prefix . 'feedback';
        $feedback_status = $wpdb->prefix . 'feedback_status';
        if (
            isset($_POST['action'])
            && isset($_POST['_ajax_nonce'])
            && wp_verify_nonce($_POST['_ajax_nonce'])
        ) {
            $wp_feedback_board_status = $wpdb->get_row($wpdb->prepare("SELECT * FROM %i WHERE id = %d",$feedback_status, sanitize_text_field($_POST['id'])));
            if ($wp_feedback_board_status) {
                $wp_feedback_data = $wpdb->get_results($wpdb->prepare("SELECT * FROM %i WHERE status_id = %d",$feedback_table, $wp_feedback_board_status->id));
                if ($wp_feedback_data) {
                    $wpdb->delete($feedback_table, array('status_id' => $wp_feedback_board_status->id));
                }
                $wpdb->delete($feedback_status, array('id' => $wp_feedback_board_status->id));
                wp_send_json(true);
            }
        }
    }

    //Wp-Roadmap-feedback general settings functionality.
    public function rmpf_general_settings_save() {
        global $wpdb;
        $general_settings = $wpdb->prefix . "general_setting";
    
        if (
            !isset($_POST['action']) ||
            !isset($_POST['_ajax_nonce']) ||
            wp_verify_nonce($_POST['_ajax_nonce'], 'wp-roadmap-nonce')
        ) {
            wp_send_json_error('Invalid nonce or action');
            return;
        }
    
    
        $title = sanitize_text_field($_POST['title']??'');
        $description = sanitize_textarea_field($_POST['description']);
        $wp_roadmap_status = sanitize_text_field($_POST['wp_roadmap_status']);
        $suggestion = sanitize_text_field($_POST['suggestion']);
        $request_feature_link = sanitize_text_field($_POST['request_feature_link']);
        $selected_pages = isset($_POST['selected_pages']) ? maybe_serialize($_POST['selected_pages']) : '';
    
        $wp_board_id = isset($_POST['wp_board_id']) ? intval($_POST['wp_board_id']) : 0;
    
        // Check if item exists for update or insert
        $existing_item = $wpdb->get_row($wpdb->prepare("SELECT * FROM %i WHERE item_id = %d",$general_settings, $wp_board_id));
    
        if ($existing_item) {
            // Update existing item
            $update_result = $wpdb->update(
                $general_settings,
                array(
                    'title' => $title,
                    'description' => $description,
                    'tab' => $wp_roadmap_status,
                    'suggestion' => $suggestion,
                    'link' => $request_feature_link,
                    'pages' => $selected_pages,
                ),
                array('item_id' => $wp_board_id),
                array('%s', '%s', '%s', '%s', '%s'),
                array('%d')
            );
    
            if ($update_result !== false) {
                wp_send_json_success(); // Send success response
            } else {
                wp_send_json_error('Failed to update.');
            }
        } else {
            // Insert new item
            $insert_result = $wpdb->insert(
                $general_settings,
                array(
                    'title' => $title,
                    'description' => $description,
                    'tab' => $wp_roadmap_status,
                    'suggestion' => $suggestion,
                    'link' => $request_feature_link,
                    'pages' => $selected_pages,
                    'item_id' => $wp_board_id,
                )
            );
    
            if ($insert_result) {
                wp_send_json_success(); // Send success response
            } else {
                wp_send_json_error('Failed to insert data.');
            }
        }
    }
    
    //Feedback save functionality.
    public function rmpf_board_save()
    {
        global $wpdb;
        $table_name = $wpdb->prefix . "feedback";
        if (
            isset($_POST['action'])
            && isset($_POST['_ajax_nonce'])
            && wp_verify_nonce($_POST['_ajax_nonce'])
        ) {
            parse_str($_POST['fields'], $settings);
            sanitize_text_field($settings);
            if (!isset($_POST['fields'])) {
                return;
            }

            $title = !empty($settings['title']) ? sanitize_text_field(esc_attr($settings['title'])) : '';
            $description = !empty($settings['description']) ? sanitize_text_field(esc_attr($settings['description'])) : '';
            $wp_roadmap_status = !empty($settings['wp_roadmap_status']) ? sanitize_text_field(esc_attr($settings['wp_roadmap_status'])) : '';
            $wp_list_id = isset($settings['wp_list_id']) ? sanitize_text_field(esc_attr($settings['wp_list_id'])) : '';

            if (isset($settings['id']) && $settings['id'] !== '') {
                $updated = $wpdb->update($table_name, array('title' => $title, 'description' => $description, 'status_id' => $wp_roadmap_status), array('id' => $settings['id']));
                wp_send_json(true);
            } else {
                if ($wp_list_id !== '0') {
                    $feedback_sequence_order = $wpdb->get_var($wpdb->prepare("SELECT sequence_order FROM %i WHERE status_id = %d ORDER BY sequence_order DESC limit 0,1", $table_name,$settings['wp_roadmap_status']));
                    $wp_board_data = array(
                        'title' => $title,
                        'description' => $description,
                        'status_id' => $wp_roadmap_status,
                        'sequence_order' => $feedback_sequence_order + 1,
                        'created_date' => date("Y-m-d"),
                        'item_id' => $wp_list_id
                    );
                    $insert_result = $wpdb->insert($table_name, $wp_board_data);
                    if ($insert_result) {
                        wp_send_json(true);
                    }   
                } else {
                    wp_send_json(false);
                }
            }
        }
    }

    //Feedback Delete
    public function rmpf_board_delete()
    {
        global $wpdb;
        $table_name = $wpdb->prefix . "feedback";
        if (
            isset($_POST['action'])
            && isset($_POST['_ajax_nonce'])
            && wp_verify_nonce($_POST['_ajax_nonce'])
        ) {
            $wp_get_feedback_data = $wpdb->get_row($wpdb->prepare("SELECT * FROM %i WHERE id = %d",$table_name ,sanitize_text_field($_POST['id'])));
            if ($wp_get_feedback_data) {
                $wpdb->delete($table_name, array('id' => $wp_get_feedback_data->id));
                wp_send_json(true);
            }
        }
    }

    //Feedback reset
    public function rmpf_board_reset()
    {
        global $wpdb;
        $table_name = $wpdb->prefix . "feedback";
        $upvote_table = $wpdb->prefix . "feedback_upvote";
        if (
            isset($_POST['action'])
            && isset($_POST['_ajax_nonce'])
            && wp_verify_nonce($_POST['_ajax_nonce'])
        ) {
            $wp_get_feedback_data = $wpdb->get_row($wpdb->prepare("SELECT * FROM %i WHERE id = %d",$table_name, sanitize_text_field($_POST['id'])));
            if (!empty($wp_get_feedback_data)) {
                $wpdb->update($table_name, array('total_upvote' => 0), array('id' => $wp_get_feedback_data->id));
                $wpdb->delete($upvote_table, array('feedback_id' => $wp_get_feedback_data->id));
                wp_send_json(true);
            }
        }
    }

    //Feedback Edit
    public function rmpf_board_edit()
    {
        global $wpdb;
        $table_name = $wpdb->prefix . "feedback";

        if (
            isset($_POST['action'])
            && isset($_POST['_ajax_nonce'])
            && wp_verify_nonce($_POST['_ajax_nonce'])
        ) {
            $wp_get_feedback_data = $wpdb->get_row($wpdb->prepare("SELECT * FROM %i WHERE id = %d",$table_name ,sanitize_text_field($_POST['id'])));
            $response = array('data' => $wp_get_feedback_data);
            wp_send_json($response);
        }
    }

    //Feedback Detail
    public function rmpf_feedback_detail()
    {
        global $wpdb;
        $table_name = $wpdb->prefix . "feedback";

        if (
            isset($_POST['action'])
            && isset($_POST['_ajax_nonce'])
            && wp_verify_nonce($_POST['_ajax_nonce'])
        ) {
            $wp_get_feedback_data = $wpdb->get_row($wpdb->prepare("SELECT * FROM %i WHERE id = %d",$table_name, $_POST['id']));
            $response = array('data' => $wp_get_feedback_data);
            wp_send_json($response);
        }
    }

    public function rmpf_board_status_update() {
        global $wpdb;
        $table_name = $wpdb->prefix . "feedback";
        $response_data = array(); 
    
        if (isset($_POST['action']) && wp_verify_nonce($_POST['_ajax_nonce'])) {
            $board_id = sanitize_text_field($_POST['board_id']);
            $feedback_id = sanitize_text_field($_POST['feedback_id']);
    
            $sql = $wpdb->prepare("UPDATE %i SET status_id = %d WHERE id = %d",$table_name, $board_id, $feedback_id);
    
            $updated = $wpdb->query($sql);
    
            if ($updated !== false) {
                $response_data['success'] = true;
                $response_data['message'] = 'Status has been updated successfully.';
                $response_data['data'] = array(
                    'feedback_id' => $feedback_id,
                    'new_status_id' => $board_id
                );
            } else {
                $response_data['success'] = false;
                $response_data['message'] = 'Status update failed.';
            }
    
            if (isset($_POST["feedback_order_ids"])) {
                $wp_status_order = array_map('sanitize_text_field', $_POST["feedback_order_ids"]);
                if (count($wp_status_order) > 0) {
                    for ($order_no = 0; $order_no < count($wp_status_order); $order_no++) {
                        $wpdb->query($wpdb->prepare("UPDATE %i SET sequence_order = %s WHERE id = %s",$table_name,($order_no + 1) ,$wp_status_order[$order_no]  ));
                    }
                    $response_data['success'] = true;
                    $response_data['message'] = 'Status order has been updated successfully.';
                }
            }
    
            wp_send_json($response_data);
        }
    }
    

    


    //Dashboard Module
    public function rmpf_feedback_dashboard()
    {
        require_once(RMPF_PATH . 'admin/view/feedback-modal.php');
        require_once(RMPF_PATH . 'admin/view/feedback-board.php');
    }

    //Settings Module.
    public function rmpf_settings()
    {
        require_once(RMPF_PATH . 'admin/view/settings.php');
    }

    public function rmpf_list()
    {
    require_once(RMPF_PATH . 'admin/view/add-item-model.php');
    
    }   

    //Save List of Board 
    public function rmpf_save_list() {
        if ( ! isset( $_POST['item_name'] ) || empty( $_POST['item_name'] ) ) {
            wp_send_json_error( 'Board name is required.' );
        }
    
        global $wpdb;
        $table_name = $wpdb->prefix . 'list_table';
        $item_name = sanitize_text_field( $_POST['item_name'] );
        $current_date = current_time('Y-m-d'); 
    
        $result = $wpdb->insert( $table_name, array(
            'name' => $item_name,
            'date' => $current_date
        ) );
    
        if ( $result ) { 
            wp_send_json_success();
        } else {
            wp_send_json_error( 'Failed to insert item.' );
        }
    }
    
    function like_button() {
    global $wpdb;
    
    $feedback_table = $wpdb->prefix . 'feedback';
    $feedback_status = $wpdb->prefix . 'feedback_status';
    $table_name = $wpdb->prefix . 'feedback_upvote';
    
    $ip = sanitize_text_field($_POST['visitor_ip_address']);
    $feedback_id = sanitize_text_field($_POST['feedback_id']);
    
    
    $wp_get_data = $wpdb->get_row($wpdb->prepare("SELECT * FROM %i WHERE visitor_ip_address = %s AND feedback_id = %d",$table_name, $ip, $feedback_id));
    
    if ($wp_get_data) {
        $wp_delete_record = $wpdb->delete($table_name, array('id' => $wp_get_data->id));
        if ($wp_delete_record) {
            $wpdb->query($wpdb->prepare("UPDATE %i SET total_upvote = total_upvote - 1 WHERE id = %d",$feedback_table, $feedback_id));
            $data = $wpdb->get_row($wpdb->prepare("SELECT * FROM %i WHERE id = %d",$feedback_table, $feedback_id));
            wp_send_json_success($data);
        }
    } else {
        $insert_result = $wpdb->insert($table_name, array('feedback_id' => $feedback_id, 'visitor_ip_address' => $ip));
        if ($insert_result !== false) {
            $wpdb->query($wpdb->prepare("UPDATE %i SET total_upvote = total_upvote + 1 WHERE id = %d",$feedback_table, $feedback_id));
            $data = $wpdb->get_row($wpdb->prepare("SELECT * FROM %i WHERE id = %d",$feedback_table, $feedback_id));
            wp_send_json_success($data);
        }
    }
    
    wp_send_json_error();
}

    
}