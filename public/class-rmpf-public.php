<?php

/**
 * The public-facing functionality of the plugin.
 *
 * @link       https://iqonic.design/
 * @since     1.0.7
 *
 * @package    Rmpf
 * @subpackage Rmpf/public
 */
use Elementor\Plugin;
/**
 * The public-facing functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the public-facing stylesheet and JavaScript.
 *
 * @package    Rmpf
 * @subpackage Rmpf/public
 * @author     Iqonic Design <hello@iqonic.design>
 */
class Rmpf_Public {

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
	 * @param      string    $plugin_name       The name of the plugin.
	 * @param      string    $version    The version of this plugin.
	 */
	public function __construct( $plugin_name, $version ) {

		$this->plugin_name = $plugin_name;
		$this->version = $version;

	}

	/**
	 * Register the stylesheets for the public-facing side of the site.
	 *
	 * @since   1.0.7
	 */
	public function enqueue_styles() {

		$options = get_option('wp_feedback_roadmap_general_settings');

        if(!empty($options) && !empty($options['pages'])) {
            $url = $_SERVER['REQUEST_URI'];
            $url_parts = explode('/', $url);
            $matches = array_intersect($options['pages'], $url_parts);
            if ((count($matches) > 0)) {
                wp_enqueue_style( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'css/rmpf-public.css', array(), RMPF_VERSION, 'all' );
            }
        } else {
            wp_enqueue_style( 'widget_css', RMPF_URL. 'public/css/rmpf-public-widget.css', array(), RMPF_VERSION );
        }
	}

	/**
	 * Register the JavaScript for the public-facing side of the site.
	 *
	 * @since   1.0.7
	 */
	public function enqueue_scripts() {

		$options = get_option('wp_feedback_roadmap_general_settings');

        if(!empty($options) && !empty($options['pages'])) {
            $url = $_SERVER['REQUEST_URI'];
            $url_parts = explode('/', $url);
            $matches = array_intersect($options['pages'], $url_parts);
            if ((count($matches) > 0)) {
                wp_enqueue_script( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'js/rmpf-public.js', array( 'jquery' ), RMPF_VERSION, true );
				wp_localize_script('widget_script', 'wp_roadmap_widget_localize', array('ajaxurl' => admin_url('admin-ajax.php'),'nonce' => wp_create_nonce()));
            }
        } else {
            wp_enqueue_script('widget_script',RMPF_URL.'public/js/rmpf-public-widget.js', array('jquery'), RMPF_VERSION, true);
            wp_localize_script('widget_script', 'wp_roadmap_widget_localize', array('ajaxurl' => admin_url('admin-ajax.php'),'nonce' => wp_create_nonce()));
        }
	}

    public function elementor_init() {
        Plugin::$instance->elements_manager->add_category(
            'wp-roadmap',
            [
                'title' => __('Wp Roadmap', 'wp-roadmap'),
                'icon' => 'fa fa-plug',
            ]
        );
    }

    public function load_widgets() {
        require plugin_dir_path(__FILE__) . 'widget/rmpf-elementor.php';
    }

}
add_action( 'wp_enqueue_scripts', 'rmpf_load_dashicons_front_end' );
function rmpf_load_dashicons_front_end() {
  wp_enqueue_style( 'dashicons' );
}