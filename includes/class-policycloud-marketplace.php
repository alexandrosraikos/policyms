<?php

/**
 * The file that defines the core plugin class
 *
 * A class definition that includes attributes and functions used across both the
 * public-facing side of the site and the admin area.
 *
 * @link       https://dac.ds.unipi.gr/policycloud-eu/
 * @since      1.0.0
 *
 * @package    PolicyCloud_Marketplace
 * @subpackage PolicyCloud_Marketplace/includes
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
 * @since      1.0.0
 * @package    PolicyCloud_Marketplace
 * @subpackage PolicyCloud_Marketplace/includes
 * @author     Your Name <email@example.com>
 */
class PolicyCloud_Marketplace
{

	/**
	 * The loader that's responsible for maintaining and registering all hooks that power
	 * the plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      PolicyCloud_Marketplace_Loader    $loader    Maintains and registers all hooks for the plugin.
	 */
	protected $loader;

	/**
	 * The unique identifier of this plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      string    $plugin_name    The string used to uniquely identify this plugin.
	 */
	protected $plugin_name;

	/**
	 * The current version of the plugin.
	 *
	 * @since    1.0.0
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
	 * @since    1.0.0
	 */
	public function __construct()
	{
		if (defined('POLICYCLOUD_MARKETPLACE_VERSION')) {
			$this->version = POLICYCLOUD_MARKETPLACE_VERSION;
		} else {
			$this->version = '1.0.0';
		}
		$this->plugin_name = 'policycloud-marketplace';

		$this->load_dependencies();
		$this->set_locale();
		$this->define_admin_hooks();
		$this->define_public_hooks();
	}

	/**
	 * Load the required dependencies for this plugin.
	 *
	 * Include the following files that make up the plugin:
	 *
	 * - PolicyCloud_Marketplace_Loader. Orchestrates the hooks of the plugin.
	 * - PolicyCloud_Marketplace_i18n. Defines internationalization functionality.
	 * - PolicyCloud_Marketplace_Admin. Defines all hooks for the admin area.
	 * - PolicyCloud_Marketplace_Public. Defines all hooks for the public side of the site.
	 *
	 * Create an instance of the loader which will be used to register the hooks
	 * with WordPress.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function load_dependencies()
	{

		/**
		 * The class responsible for orchestrating the actions and filters of the
		 * core plugin.
		 */
		require_once plugin_dir_path(dirname(__FILE__)) . 'includes/class-policycloud-marketplace-loader.php';

		/**
		 * The class responsible for defining internationalization functionality
		 * of the plugin.
		 */
		require_once plugin_dir_path(dirname(__FILE__)) . 'includes/class-policycloud-marketplace-i18n.php';

		/**
		 * The class responsible for defining all actions that occur in the admin area.
		 */
		require_once plugin_dir_path(dirname(__FILE__)) . 'admin/class-policycloud-marketplace-admin.php';

		/**
		 * The class responsible for defining all actions that occur in the public-facing
		 * side of the site.
		 */
		require_once plugin_dir_path(dirname(__FILE__)) . 'public/class-policycloud-marketplace-public.php';

		$this->loader = new PolicyCloud_Marketplace_Loader();
	}

	/**
	 * Define the locale for this plugin for internationalization.
	 *
	 * Uses the PolicyCloud_Marketplace_i18n class in order to set the domain and to register the hook
	 * with WordPress.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function set_locale()
	{

		$plugin_i18n = new PolicyCloud_Marketplace_i18n();

		$this->loader->add_action('plugins_loaded', $plugin_i18n, 'load_plugin_textdomain');
	}

	/**
	 * Register all of the hooks related to the admin area functionality
	 * of the plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function define_admin_hooks()
	{

		$plugin_admin = new PolicyCloud_Marketplace_Admin($this->get_plugin_name(), $this->get_version());

		$this->loader->add_action('admin_enqueue_scripts', $plugin_admin, 'enqueue_styles');
		$this->loader->add_action('admin_enqueue_scripts', $plugin_admin, 'enqueue_scripts');

		$this->loader->add_action('admin_menu', $plugin_admin, 'add_settings_page');
		$this->loader->add_action('admin_init', $plugin_admin, 'register_settings');
	}

	/**
	 * Register all of the hooks related to the public-facing functionality
	 * of the plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function define_public_hooks()
	{

		$plugin_public = new PolicyCloud_Marketplace_Public($this->get_plugin_name(), $this->get_version());

		$this->loader->add_action('wp_enqueue_scripts', $plugin_public, 'enqueue_styles');
		$this->loader->add_action('wp_enqueue_scripts', $plugin_public, 'enqueue_scripts');
		$this->loader->add_action('wp_head', $plugin_public, 'enqueue_head_scripts');

		$this->loader->add_action('wp_ajax_policycloud_marketplace_account_registration', $plugin_public, 'account_registration_handler');
		$this->loader->add_action('wp_ajax_nopriv_policycloud_marketplace_account_registration', $plugin_public, 'account_registration_handler');
		$this->loader->add_action('wp_ajax_policycloud_marketplace_account_authorization', $plugin_public, 'account_authorization_handler');
		$this->loader->add_action('wp_ajax_nopriv_policycloud_marketplace_account_authorization', $plugin_public, 'account_authorization_handler');
		$this->loader->add_action('wp_ajax_policycloud_marketplace_user_email_verification_resend', $plugin_public, 'user_email_verification_resend_handler');
		$this->loader->add_action('wp_ajax_nopriv_policycloud_marketplace_user_email_verification_resend', $plugin_public, 'user_email_verification_resend_handler');
		$this->loader->add_action('wp_ajax_policycloud_marketplace_account_data_request', $plugin_public, 'account_data_request_handler');
		$this->loader->add_action('wp_ajax_nopriv_policycloud_marketplace_account_data_request', $plugin_public, 'account_data_request_handler');

		$this->loader->add_action('init', $plugin_public, 'add_accounts_shortcodes');

		// Add user access management menu item.
		$this->loader->add_filter('wp_nav_menu_items', $plugin_public, 'add_conditional_access_menu_item', 10, 2);

		// -- CRUD operations on database.
		$this->loader->add_action('init', $plugin_public, 'add_content_shortcodes');
		$this->loader->add_action('wp_ajax_description_edit', $plugin_public, 'description_edit_handler');
		$this->loader->add_action('wp_ajax_nopriv_description_edit', $plugin_public, 'description_edit_handler');
		$this->loader->add_action('wp_ajax_policycloud_marketplace_object_creation', $plugin_public, 'object_creation_handler');
		$this->loader->add_action('wp_ajax_nopriv_policycloud_marketplace_object_creation', $plugin_public, 'object_creation_handler');
		$this->loader->add_action('wp_ajax_policycloud_marketplace_account_edit', $plugin_public, 'account_edit_handler');
		$this->loader->add_action('wp_ajax_nopriv_policycloud_marketplace_account_edit', $plugin_public, 'account_edit_handler');
	}

	/**
	 * Run the loader to execute all of the hooks with WordPress.
	 *
	 * @since    1.0.0
	 */
	public function run()
	{
		$this->loader->run();
	}

	/**
	 * The name of the plugin used to uniquely identify it within the context of
	 * WordPress and to define internationalization functionality.
	 *
	 * @since     1.0.0
	 * @return    string    The name of the plugin.
	 */
	public function get_plugin_name()
	{
		return $this->plugin_name;
	}

	/**
	 * The reference to the class that orchestrates the hooks with the plugin.
	 *
	 * @since     1.0.0
	 * @return    PolicyCloud_Marketplace_Loader    Orchestrates the hooks of the plugin.
	 */
	public function get_loader()
	{
		return $this->loader;
	}

	/**
	 * Retrieve the version number of the plugin.
	 *
	 * @since     1.0.0
	 * @return    string    The version number of the plugin.
	 */
	public function get_version()
	{
		return $this->version;
	}
}
