<?php

/**
 * Define the internationalization functionality
 *
 * Loads and defines the internationalization files for this plugin
 * so that it is ready for translation.
 *
 * @link       https://iqonic.design/
 * @since     1.0.7
 *
 * @package    Rmpf
 * @subpackage Rmpf/includes
 */

/**
 * Define the internationalization functionality.
 *
 * Loads and defines the internationalization files for this plugin
 * so that it is ready for translation.
 *
 * @since     1.0.7
 * @package    Rmpf
 * @subpackage Rmpf/includes
 * @author     Iqonic Design <hello@iqonic.design>
 */
class Rmpf_i18n {


	/**
	 * Load the plugin text domain for translation.
	 *
	 * @since   1.0.7
	 */
	public function load_plugin_textdomain() {

		var_dump(load_plugin_textdomain(
			'wp-roadmap',
			false,
			RMPF_BASE_NAME . '/languages/'
		));

	}



}
