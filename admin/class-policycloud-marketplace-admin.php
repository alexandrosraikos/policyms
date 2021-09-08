<?php

/**
 * The admin-specific functionality of the plugin.
 *
 * @link       https://dac.ds.unipi.gr/policycloud-eu/
 * @since      1.0.0
 *
 * @package    PolicyCloud_Marketplace
 * @subpackage PolicyCloud_Marketplace/admin
 */

/**
 * The admin-specific functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 *
 * @package    PolicyCloud_Marketplace
 * @subpackage PolicyCloud_Marketplace/admin
 * @author     Your Name <email@example.com>
 */
class PolicyCloud_Marketplace_Admin
{

	/**
	 * The ID of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $plugin_name    The ID of this plugin.
	 */
	private $plugin_name;

	/**
	 * The version of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $version    The current version of this plugin.
	 */
	private $version;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0.0
	 * @param      string    $plugin_name       The name of this plugin.
	 * @param      string    $version    The version of this plugin.
	 */
	public function __construct($plugin_name, $version)
	{

		$this->plugin_name = $plugin_name;
		$this->version = $version;
	}

	/**
	 * Register the stylesheets for the admin area.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_styles()
	{

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in PolicyCloud_Marketplace_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The PolicyCloud_Marketplace_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

		wp_enqueue_style($this->plugin_name, plugin_dir_url(__FILE__) . 'css/policycloud-marketplace-admin.css', array(), $this->version, 'all');
	}

	/**
	 * Register the JavaScript for the admin area.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_scripts()
	{

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in PolicyCloud_Marketplace_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The PolicyCloud_Marketplace_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

		wp_enqueue_script($this->plugin_name, plugin_dir_url(__FILE__) . 'js/policycloud-marketplace-admin.js', array('jquery'), $this->version, false);
	}

	function policycloud_marketplace_validate_plugin_settings( $input ) {
		$output['jwt_key']      = sanitize_text_field( $input['jwt_key'] );
		$output['marketplace_host'] = sanitize_text_field( $input['marketplace_host']);
		return $output;
	}

	function register_settings() {
		
		register_setting(
		  'policycloud_marketplace_plugin_settings',
		  'policycloud_marketplace_plugin_settings',
		  'policycloud_marketplace_validate_plugin_settings'
		);

		add_settings_section(
		  'section_one',
		  'Access Credentials',
		  'policycloud_marketplace_plugin_section_one',
		  'policycloud_marketplace_plugin'
		);

		add_settings_field(
		  'marketplace_host',
		  'Marketplace Host',
		  'policycloud_marketplace_plugin_host',
		  'policycloud_marketplace_plugin',
		  'section_one'
		);

		add_settings_field(
		  'jwt_key',
		  'Marketplace Key',
		  'policycloud_marketplace_plugin_jwt_key',
		  'policycloud_marketplace_plugin',
		  'section_one'
		);

	  }
	  
	public function add_settings_page()
	{
		require_once plugin_dir_path(dirname(__FILE__)) . 'admin/partials/policycloud-marketplace-admin-display.php';

		add_options_page(
			'PolicyCloud Marketplace Settings',
			'PolicyCloud Marketplace',
			'manage_options',
			'policycloud-marketplace-plugin',
			'render_settings_page'
		);
	}
}