<?php

/**
 * The plugin bootstrap file
 *
 * This file is read by WordPress to generate the plugin information in the plugin
 * admin area. This file also includes all of the dependencies used by the plugin,
 * registers the activation and deactivation functions, and defines a function
 * that starts the plugin.
 *
 * @link              https://github.com/alexandrosraikos/policyms/
 * @since             1.0.0
 * @package           PolicyMS
 *
 * @wordpress-plugin
 * Plugin Name:       PolicyMS for WordPress
 * Plugin URI:        https://github.com/alexandrosraikos/policyms/
 * Description:       The official plugin for the PolicyMS, enabling front-end access to the PolicyMS API.
 * Version:           1.4.0
 * Author:            University of Piraeus Research Center
 * Author URI:        https://dac.ds.unipi.gr/
 * Text Domain:       policyms
 * Domain Path:       /languages
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

define( 'POLICYCLOUD_MARKETPLACE_VERSION', '1.0.2' );

/**
 * The code that runs during plugin activation.
 * This action is documented in includes/class-policyms-activator.php
 */
function activate_plugin_name() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-policyms-activator.php';
	PolicyMS_Activator::activate();
}

/**
 * The code that runs during plugin deactivation.
 * This action is documented in includes/class-policyms-deactivator.php
 */
function deactivate_plugin_name() {
	 require_once plugin_dir_path( __FILE__ ) . 'includes/class-policyms-deactivator.php';
	PolicyMS_Deactivator::deactivate();
}

register_activation_hook( __FILE__, 'activate_plugin_name' );
register_deactivation_hook( __FILE__, 'deactivate_plugin_name' );

/**
 * The core plugin class that is used to define internationalization,
 * admin-specific hooks, and public-facing site hooks.
 */
require plugin_dir_path( __FILE__ ) . 'includes/class-policyms.php';

/**
 * Begins execution of the plugin.
 *
 * Since everything within the plugin is registered via hooks,
 * then kicking off the plugin from this point in the file does
 * not affect the page life cycle.
 *
 * @since    1.0.0
 */
function run_plugin_name() {
	$plugin = new PolicyMS();
	$plugin->run();
}
run_plugin_name();
