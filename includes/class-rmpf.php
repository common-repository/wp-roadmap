<?php

/**
 * The file that defines the core plugin class
 *
 * A class definition that includes attributes and functions used across both the
 * public-facing side of the site and the admin area.
 *
 * @link       https://iqonic.design/
 * @since     1.0.7
 *
 * @package    Rmpf
 * @subpackage Rmpf/includes
 */

/**
 * The core plugin class.
 *
 * This is used to define internationalization, admin-specific hooks, and
 * public-facing site hooks.
 *
 * Also maintains the unique identifier of this plugin as well as the current
 * version of the plugin.
 *
 * @since     1.0.7
 * @package    Rmpf
 * @subpackage Rmpf/includes
 * @author     Iqonic Design <hello@iqonic.design>
 */
class Rmpf {

	/**
	 * The loader that's responsible for maintaining and registering all hooks that power
	 * the plugin.
	 *
	 * @since   1.0.7
	 * @access   protected
	 * @var      Rmpf_Loader    $loader    Maintains and registers all hooks for the plugin.
	 */
	protected $loader;

	/**
	 * The unique identifier of this plugin.
	 *
	 * @since   1.0.7
	 * @access   protected
	 * @var      string    $plugin_name    The string used to uniquely identify this plugin.
	 */
	protected $plugin_name;

	/**
	 * The current version of the plugin.
	 *
	 * @since   1.0.7
	 * @access   protected
	 * @var      string    $version    The current version of the plugin.
	 */
	protected $version;

	/**
	 * Define the core functionality of the plugin.
	 *
	 * Set the plugin name and the plugin version that can be used throughout the plugin.
	 * Load the dependencies, define the locale, and set the hooks for the admin area and
	 * the public-facing side of the site.
	 *
	 * @since   1.0.7
	 */
	public function __construct() {
		if ( defined( 'RMPF_VERSION' ) ) {
			$this->version = RMPF_VERSION;
		} else {
			$this->version = '1.0.1';
		}
		$this->plugin_name = 'rmpf';

		$this->load_dependencies();
		// $this->set_locale();
		$this->define_admin_hooks();
		$this->define_public_hooks();

	}

	/**
	 * Load the required dependencies for this plugin.
	 *
	 * Include the following files that make up the plugin:
	 *
	 * - Rmpf_Loader. Orchestrates the hooks of the plugin.
	 * - Rmpf_i18n. Defines internationalization functionality.
	 * - Rmpf_Admin. Defines all hooks for the admin area.
	 * - Rmpf_Public. Defines all hooks for the public side of the site.
	 *
	 * Create an instance of the loader which will be used to register the hooks
	 * with WordPress.
	 *
	 * @since   1.0.7
	 * @access   private
	 */
	private function load_dependencies() {

		/**
		 * The class responsible for orchestrating the actions and filters of the
		 * core plugin.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-rmpf-loader.php';

		/**
		 * The class responsible for defining internationalization functionality
		 * of the plugin.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-rmpf-i18n.php';

		/**
		 * The class responsible for defining all actions that occur in the admin area.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/class-rmpf-admin.php';

		/**
		 * The class responsible for defining all actions that occur in the public-facing
		 * side of the site.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'public/class-rmpf-public.php';

		$this->loader = new Rmpf_Loader();

	}

	/**
	 * Define the locale for this plugin for internationalization.
	 *
	 * Uses the Rmpf_i18n class in order to set the domain and to register the hook
	 * with WordPress.
	 *
	 * @since   1.0.7
	 * @access   private
	 */
	private function set_locale() {

		$plugin_i18n = new Rmpf_i18n();

		$this->loader->add_action( 'plugins_loaded', $plugin_i18n, 'load_plugin_textdomain' );

	}

	/**
	 * Register all of the hooks related to the admin area functionality
	 * of the plugin.
	 *
	 * @since   1.0.7
	 * @access   private
	 */
	private function define_admin_hooks() {
		$plugin_admin = new Rmpf_Admin( $this->get_plugin_name(), $this->get_version() );

        $this->loader->add_action('admin_enqueue_scripts', $plugin_admin, 'enqueue_styles' );
        $this->loader->add_action('admin_enqueue_scripts', $plugin_admin, 'enqueue_scripts' );
        $this->loader->add_action('admin_menu', $plugin_admin, 'admin_menu');
        $this->loader->add_action('wp_ajax_save_feedback_roadmap_settings', $plugin_admin, 'rmpf_status_save');
        $this->loader->add_action('wp_ajax_update_feedback_roadmap_settings', $plugin_admin, 'rmpf_status_update');
        $this->loader->add_action('wp_ajax_update_feedback_status_order', $plugin_admin, 'rmpf_status_order');
        $this->loader->add_action('wp_ajax_delete_feedback_roadmap_settings', $plugin_admin, 'rmpf_status_delete');
		$this->loader->add_action('wp_ajax_save_feedback_roadmap_general_settings', $plugin_admin, 'rmpf_general_settings_save');
        $this->loader->add_action('wp_ajax_save_feedback_board_data', $plugin_admin, 'rmpf_board_save');
        $this->loader->add_action('wp_ajax_delete_feedback_board_data', $plugin_admin, 'rmpf_board_delete');
		$this->loader->add_action('wp_ajax_reset_feedback_board_data', $plugin_admin, 'rmpf_board_reset');
        $this->loader->add_action('wp_ajax_edit_feedback_board_data', $plugin_admin, 'rmpf_board_edit');
        $this->loader->add_action('wp_ajax_wp_feedback_detail', $plugin_admin, 'rmpf_feedback_detail');
        $this->loader->add_action('wp_ajax_wp_update_board_status', $plugin_admin, 'rmpf_board_status_update');
		$this->loader->add_action('wp_ajax_wp_save_list', $plugin_admin, 'rmpf_save_list');
		$this->loader->add_action('wp_ajax_wp_like_button_insert', $plugin_admin, 'like_button');
	}

	/**
	 * Register all of the hooks related to the public-facing functionality
	 * of the plugin.
	 *
	 * @since   1.0.7
	 * @access   private
	 */
	private function define_public_hooks() {
		$plugin_public = new Rmpf_Public( $this->get_plugin_name(), $this->get_version() );
		$this->loader->add_action( 'wp_enqueue_scripts', $plugin_public, 'enqueue_styles' );
		$this->loader->add_action( 'wp_enqueue_scripts', $plugin_public, 'enqueue_scripts' );
        $this->loader->add_action( 'elementor/init', $plugin_public, 'elementor_init' );
        $this->loader->add_action( 'elementor/widgets/register', $plugin_public, 'load_widgets' );
	}

	/**
	 * Run the loader to execute all of the hooks with WordPress.
	 *
	 * @since   1.0.7
	 */
	public function run() {
		$this->loader->run();
	}

	/**
	 * The name of the plugin used to uniquely identify it within the context of
	 * WordPress and to define internationalization functionality.
	 *
	 * @since    1.0.7
	 * @return    string    The name of the plugin.
	 */
	public function get_plugin_name() {
		return $this->plugin_name;
	}

	/**
	 * The reference to the class that orchestrates the hooks with the plugin.
	 *
	 * @since    1.0.7
	 * @return    Rmpf_Loader    Orchestrates the hooks of the plugin.
	 */
	public function get_loader() {
		return $this->loader;
	}

	/**
	 * Retrieve the version number of the plugin.
	 *
	 * @since    1.0.7
	 * @return    string    The version number of the plugin.
	 */
	public function get_version() {
		return $this->version;
	}

}
