<?php

/**
 *
 * @link              https://iqonic.design/
 * @since             1.0.7
 * @package           Rmpf
 *
 * @wordpress-plugin
 * Plugin Name:       WP Roadmap - Product Feedback Board
 * Plugin URI:        https://iqonicthemes.com
 * Description:       Show your products, events, in-process developments, concerts, future happenings by using WP Roadmap - Product Feedback Board plugin on your website.
 * Version:           2.1.1
 * Author:            Iqonic Design
 * Author URI:        https://iqonic.design/
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       wp-roadmap
 * Domain Path:       /languages
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

/**
 * Currently plugin version.
 * Start at version1.0.7 and use SemVer - https://semver.org
 * Rename this for your plugin and update it as you release new versions.
 */
define( 'RMPF_VERSION', '2.1.1' );
define( 'RMPF_PATH', plugin_dir_path(__FILE__));
define( 'RMPF_URL', plugins_url('/', __FILE__));
define( 'RMPF_SITE_URL', get_site_url());

if (!defined('RMPF_BASE_NAME'))
{
    define('RMPF_BASE_NAME', plugin_basename(__FILE__));
}
/**
 * The code that runs during plugin activation.
 * This action is documented in includes/class-rmpf-activator.php
 */
function rmpf_activate() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-rmpf-activator.php';
	Rmpf_Activator::activate();
}
function rmpf_widget_load_elements() {
    require( __DIR__ . '/utils/rmpf_wiget_helper.php');
}
add_action( 'plugins_loaded', 'rmpf_widget_load_elements' );
/**
 * The code that runs during plugin deactivation.
 * This action is documented in includes/class-rmpf-deactivator.php
 */
function rmpf_deactivate() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-rmpf-deactivator.php';
	Rmpf_Deactivator::deactivate();
}

/**
 * The code that runs during plugin activation.
 * This action is migrate tables.
 */
function rmpf_migration_tables() {
    require_once plugin_dir_path( __FILE__ ) . 'includes/class-rmpf-table-migration.php';
    RMPF_Table_Migration::rmpf_migrate_feedback();
    RMPF_Table_Migration::rmpf_migrate_upvote();
    RMPF_Table_Migration::rmpf_migrate_feedback_status();
    RMPF_Table_Migration::rmpf_list_table();
    RMPF_Table_Migration::wp_general_setting();
}
register_activation_hook( __FILE__, 'rmpf_activate' );
add_action( 'init', 'rmpf_migration_tables' );
register_deactivation_hook( __FILE__, 'rmpf_deactivate' );
require plugin_dir_path( __FILE__ ) . 'includes/api/class-rmpf-api.php';

/**
 * The core plugin class that is used to define internationalization,
 * admin-specific hooks, and public-facing site hooks.
 */
require plugin_dir_path( __FILE__ ) . 'includes/class-rmpf.php';
require plugin_dir_path(__FILE__).'includes/class-rmpf-widget.php';

/**
 * Begins execution of the plugin.
 *
 * Since everything within the plugin is registered via hooks,
 * then kicking off the plugin from this point in the file does
 * not affect the page life cycle.
 *
 * @since   1.0.7
 */
function run_rmpf() {
	$plugin = new Rmpf();
	$plugin->run();
    return ob_end_clean();
}
run_rmpf();

// Register Widget
function RMPF_Widget(){
    register_widget('RMPF_Widget');
}
// Hook in function
add_action('widgets_init', 'RMPF_Widget');
function rmpf_widget_shortcode($atts){
    $atts = shortcode_atts(
        array(
            'board_id' => '',
        ),
        $atts,
        'rmpf_roadmap_widget'
    );

    $board_id = $atts['board_id'];
    $unique_id = uniqid('widget_');
    $wp_get_roadmap_widget = new RMPF_Widget();
    $wp_get_roadmap_widget->widget_style($board_id,$unique_id);
}
add_shortcode('rmpf_roadmap_widget', function ($atts) {
    ob_start();
    rmpf_widget_shortcode($atts);
    return ob_get_clean();
});

function rmpf_enqueue_scripts() {
    echo '<script>window.SITE_URL = "' . RMPF_SITE_URL . '"</script>';
}
add_action( 'wp_enqueue_scripts', 'rmpf_enqueue_scripts');
$wp_feedback_api = new RMPF_Api();

add_action( 'enqueue_block_editor_assets', 'rmpf_widget_block_assets' );
function rmpf_widget_block_assets(){    
    $rmpf_base_url = RMPF_SITE_URL;
	wp_enqueue_script(
 		'rmpf-upvote-widget',
		plugin_dir_url( __FILE__ ) . 'admin/js/rmpf-widget-block.js',
        array('wp-blocks',
        'wp-i18n',
        'wp-element',
        'wp-components',
        'wp-editor'
         )
    );  
    wp_localize_script('rmpf-upvote-widget', 'rmpf_base_url', array($rmpf_base_url));
}
add_filter( 'block_categories_all', function ( $categories, $post ) {
    return array_merge( array(
        array(
            'slug'  => 'rmpf-widget-blocks',
            'title' => 'Roadmap',
        ), ),
        $categories
    );
}, 10, 2 );


add_action( 'plugins_loaded', function (){
    load_plugin_textdomain( 'wp-roadmap', false, dirname( RMPF_BASE_NAME ) . '/languages' );
});