<?php

/**
 * Define the internationalization functionality
 *
 * Loads and defines the internationalization files for this plugin
 * so that it is ready for translation.
 *
 * @link       https://github.com/alexandrosraikos/policyms/
 * @since      1.0.0
 *
 * @package    PolicyMS
 * @subpackage PolicyMS/includes
 */

/**
 * Define the internationalization functionality.
 *
 * Loads and defines the internationalization files for this plugin
 * so that it is ready for translation.
 *
 * @since      1.0.0
 * @package    PolicyMS
 * @subpackage PolicyMS/includes
 * @author     Alexandros Raikos <alexandros@araikos.gr>
 */
class PolicyMS_i18n {



	/**
	 * Load the plugin text domain for translation.
	 *
	 * @since    1.0.0
	 */
	public function load_plugin_textdomain() {

		load_plugin_textdomain(
			'policyms',
			false,
			dirname( dirname( plugin_basename( __FILE__ ) ) ) . '/languages/'
		);
	}
}
