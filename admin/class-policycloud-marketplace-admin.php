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
 * @author     Alexandros Raikos <araikos@unipi.gr>
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
		wp_enqueue_style($this->plugin_name, plugin_dir_url(__FILE__) . 'css/policycloud-marketplace-admin.css', array(), $this->version, 'all');
	}

	/**
	 * Register the JavaScript for the admin area.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_scripts()
	{
		wp_enqueue_script($this->plugin_name, plugin_dir_url(__FILE__) . 'js/policycloud-marketplace-admin.js', array('jquery'), $this->version, false);
	}

	function policycloud_marketplace_validate_plugin_settings( $input ) {
		$output['marketplace_host'] = sanitize_text_field( $input['marketplace_host']);
		$output['api_access_token'] = sanitize_text_field( $input['api_access_token']);
		$output['jwt_key'] = sanitize_text_field( $input['jwt_key'] );
		$output['encryption_key'] = sanitize_text_field( $input['encryption_key'] );
		$output['login_page'] = esc_url($input['login_page']);
		$output['registration_page'] = esc_url($input['registration_page']);
		$output['tos_url'] = esc_url($input['tos_url']);
		$output['account_page'] = esc_url($input['account_page']);
		$output['selected_menu'] = sanitize_text_field($input['selected_menu']);
		$output['description_page'] = esc_url($input['description_page']);
		$output['upload_page'] = esc_url($input['upload_page']);
		$output['archive_page'] = esc_url($input['archive_page']);
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
		  'api_access_token',
		  'Marketplace API Access Token',
		  'policycloud_marketplace_plugin_api_access_token',
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

		add_settings_field(
		  'encryption_key',
		  'Encryption Key',
		  'policycloud_marketplace_plugin_encryption_key',
		  'policycloud_marketplace_plugin',
		  'section_one'
		);

		add_settings_section(
		  'section_two',
		  'Menu Settings',
		  'policycloud_marketplace_plugin_section_two',
		  'policycloud_marketplace_plugin'
		);

		add_settings_field(
			'selected_menu',
			'Selected Menu',
			'policycloud_marketplace_plugin_menu_selector',
			'policycloud_marketplace_plugin',
			'section_two'
		);

		add_settings_field(
			'login_page',
			'Redirect to Log In',
			'policycloud_marketplace_plugin_login_page_selector',
			'policycloud_marketplace_plugin',
			'section_two'
		);

		add_settings_field(
			'registration_page',
			'Redirect to Registration',
			'policycloud_marketplace_plugin_registration_page_selector',
			'policycloud_marketplace_plugin',
			'section_two'
		);

		add_settings_field(
			'account_page',
			'Redirect to My Account',
			'policycloud_marketplace_plugin_account_page_selector',
			'policycloud_marketplace_plugin',
			'section_two'
		);

		add_settings_section(
		  'section_three',
		  'Content Settings',
		  'policycloud_marketplace_plugin_section_three',
		  'policycloud_marketplace_plugin'
		);

		add_settings_field(
			'tos_url',
			'Terms of Service URL',
			'policycloud_marketplace_plugin_tos_url',
			'policycloud_marketplace_plugin',
			'section_three'
		);

		add_settings_field(
			'description_page',
			'Redirect to single Description page',
			'policycloud_marketplace_plugin_description_page_selector',
			'policycloud_marketplace_plugin',
			'section_three'
		);

		add_settings_field(
			'archive_page',
			'Redirect to Assets archive page',
			'policycloud_marketplace_plugin_archive_page_selector',
			'policycloud_marketplace_plugin',
			'section_three'
		);

		add_settings_field(
			'upload_page',
			'Redirect to Description upload',
			'policycloud_marketplace_plugin_upload_page_selector',
			'policycloud_marketplace_plugin',
			'section_three'
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
