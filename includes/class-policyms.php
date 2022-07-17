<?php

/**
 * The file that defines the core plugin class
 *
 * A class definition that includes attributes and functions used across both the
 * public-facing side of the site and the admin area.
 *
 * @link       https://github.com/alexandrosraikos/policyms/
 * @since      1.0.0
 *
 * @package    PolicyMS
 * @subpackage PolicyMS/includes
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
 * @package    PolicyMS
 * @subpackage PolicyMS/includes
 * @author     Alexandros Raikos <alexandros@araikos.gr>
 */
class PolicyMS {


	/**
	 * The loader that's responsible for maintaining and registering all hooks that power
	 * the plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      PolicyMS_Loader    $loader    Maintains and registers all hooks for the plugin.
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
	public function __construct() {
		if ( defined( 'POLICYMS_VERSION' ) ) {
			$this->version = POLICYMS_VERSION;
		} else {
			$this->version = '1.0.0';
		}
		$this->plugin_name = 'policyms';

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
	 * - PolicyMS_Loader. Orchestrates the hooks of the plugin.
	 * - PolicyMS_i18n. Defines internationalization functionality.
	 * - PolicyMS_Admin. Defines all hooks for the admin area.
	 * - PolicyMS_Public. Defines all hooks for the public side of the site.
	 *
	 * Create an instance of the loader which will be used to register the hooks
	 * with WordPress.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function load_dependencies() {

		/**
		 * The class responsible for orchestrating the actions and filters of the
		 * core plugin.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-policyms-loader.php';

		/**
		 * The class responsible for defining internationalization functionality
		 * of the plugin.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-policyms-i18n.php';

		/**
		 * The class responsible for defining all actions that occur in the admin area.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/class-policyms-admin.php';

		/**
		 * The class responsible for defining all actions that occur in the public-facing
		 * side of the site.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'public/class-policyms-public.php';

		/**
		 * The class responsible for defining all custom exceptions thrown by the plugin.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-policyms-exceptions.php';

		/**
		 * The classes responsible for defining the controllers of the plugin.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'controller/class-policyms-communication-controller.php';
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'controller/class-policyms-oauth-controller.php';

		/**
		 * The classes responsible for defining the object model and core functionality of the plugin.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'model/class-policyms-account.php';
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'model/class-policyms-user.php';
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'model/class-policyms-asset-type.php';
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'model/class-policyms-asset.php';
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'model/class-policyms-description.php';
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'model/class-policyms-description-collection.php';
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'model/class-policyms-description-filters.php';
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'model/class-policyms-review.php';

		$this->loader = new PolicyMS_Loader();
	}

	/**
	 * Define the locale for this plugin for internationalization.
	 *
	 * Uses the PolicyMS_i18n class in order to set the domain and to register the hook
	 * with WordPress.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function set_locale() {

		$plugin_i18n = new PolicyMS_i18n();

		$this->loader->add_action( 'plugins_loaded', $plugin_i18n, 'load_plugin_textdomain' );
	}

	/**
	 * Register all of the hooks related to the admin area functionality
	 * of the plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function define_admin_hooks() {

		$plugin_admin = new PolicyMS_Admin( $this->get_plugin_name(), $this->get_version() );

		$this->loader->add_action( 'admin_enqueue_scripts', $plugin_admin, 'enqueue_styles' );
		$this->loader->add_action( 'admin_enqueue_scripts', $plugin_admin, 'enqueue_scripts' );

		$this->loader->add_action( 'admin_menu', $plugin_admin, 'add_settings_page' );
		$this->loader->add_action( 'admin_init', $plugin_admin, 'register_settings' );
	}

	/**
	 * Register all of the hooks related to the public-facing functionality
	 * of the plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function define_public_hooks() {
		$plugin_public = new PolicyMS_Public( $this->get_plugin_name(), $this->get_version() );

		$this->loader->add_action( 'wp_enqueue_scripts', $plugin_public, 'enqueue_styles' );
		$this->loader->add_action( 'wp_enqueue_scripts', $plugin_public, 'enqueue_scripts' );

		// Support for user accounts.
		$this->loader->add_action( 'init', $plugin_public, 'add_accounts_shortcodes' );
		// TODO @alexandrosraikos: Find a way to add this through the Appearance > Menus.
		$this->loader->add_filter( 'wp_nav_menu_items', $plugin_public, 'add_menu_items', 10, 2 );
		$this->loader->add_action( 'wp_ajax_policyms_account_user_registration', $plugin_public, 'account_user_registration_handler' );
		$this->loader->add_action( 'wp_ajax_nopriv_policyms_account_user_registration', $plugin_public, 'account_user_registration_handler' );
		$this->loader->add_action( 'wp_ajax_policyms_account_user_authentication', $plugin_public, 'account_user_authentication_handler' );
		$this->loader->add_action( 'wp_ajax_nopriv_policyms_account_user_authentication', $plugin_public, 'account_user_authentication_handler' );
		$this->loader->add_action( 'wp_ajax_policyms_account_user_authentication_google', $plugin_public, 'account_user_authentication_google_handler' );
		$this->loader->add_action( 'wp_ajax_nopriv_policyms_account_user_authentication_google', $plugin_public, 'account_user_authentication_google_handler' );
		$this->loader->add_action( 'wp_ajax_policyms_account_user_authentication_keycloak', $plugin_public, 'account_user_authentication_keycloak_handler' );
		$this->loader->add_action( 'wp_ajax_nopriv_policyms_account_user_authentication_keycloak', $plugin_public, 'account_user_authentication_keycloak_handler' );
		$this->loader->add_action( 'wp_ajax_policyms_account_user_registration_google', $plugin_public, 'account_user_registration_google_handler' );
		$this->loader->add_action( 'wp_ajax_nopriv_policyms_account_user_registration_google', $plugin_public, 'account_user_registration_google_handler' );
		$this->loader->add_action( 'wp_ajax_policyms_account_user_registration_keycloak', $plugin_public, 'account_user_registration_keycloak_handler' );
		$this->loader->add_action( 'wp_ajax_nopriv_policyms_account_user_registration_keycloak', $plugin_public, 'account_user_registration_keycloak_handler' );
		$this->loader->add_action( 'wp_ajax_policyms_account_disconnect_google', $plugin_public, 'account_disconnect_google_handler' );
		$this->loader->add_action( 'wp_ajax_nopriv_policyms_account_disconnect_google', $plugin_public, 'account_disconnect_google_handler' );
		$this->loader->add_action( 'wp_ajax_policyms_account_disconnect_keycloak', $plugin_public, 'account_disconnect_keycloak_handler' );
		$this->loader->add_action( 'wp_ajax_nopriv_policyms_account_disconnect_keycloak', $plugin_public, 'account_disconnect_keycloak_handler' );
		$this->loader->add_action( 'wp_ajax_policyms_account_disconnect_egi', $plugin_public, 'account_disconnect_egi_handler' );
		$this->loader->add_action( 'wp_ajax_nopriv_policyms_account_disconnect_egi', $plugin_public, 'account_disconnect_egi_handler' );
		$this->loader->add_action( 'wp_ajax_policyms_account_user_password_reset', $plugin_public, 'account_user_password_reset_handler' );
		$this->loader->add_action( 'wp_ajax_nopriv_policyms_account_user_password_reset', $plugin_public, 'account_user_password_reset_handler' );
		$this->loader->add_action( 'wp_ajax_policyms_account_user_retry_verification', $plugin_public, 'account_user_verification_retry_handler' );
		$this->loader->add_action( 'wp_ajax_nopriv_policyms_account_user_retry_verification', $plugin_public, 'account_user_verification_retry_handler' );
		$this->loader->add_action( 'wp_ajax_policyms_account_user_edit', $plugin_public, 'account_user_editing_handler' );
		$this->loader->add_action( 'wp_ajax_nopriv_policyms_account_user_edit', $plugin_public, 'account_user_editing_handler' );
		$this->loader->add_action( 'wp_ajax_policyms_account_user_data_request', $plugin_public, 'account_user_data_request_handler' );
		$this->loader->add_action( 'wp_ajax_nopriv_policyms_account_user_data_request', $plugin_public, 'account_user_data_request_handler' );
		$this->loader->add_action( 'wp_ajax_policyms_account_user_deletion', $plugin_public, 'account_user_deletion_handler' );
		$this->loader->add_action( 'wp_ajax_nopriv_policyms_account_user_deletion', $plugin_public, 'account_user_deletion_handler' );

		// Support for descriptions.
		$this->loader->add_action( 'init', $plugin_public, 'add_description_shortcodes' );
		$this->loader->add_action( 'wp_ajax_policyms_description_creation', $plugin_public, 'description_creation_handler' );
		$this->loader->add_action( 'wp_ajax_nopriv_policyms_description_creation', $plugin_public, 'description_creation_handler' );
		$this->loader->add_action( 'wp_ajax_policyms_description_editing', $plugin_public, 'description_editing_handler' );
		$this->loader->add_action( 'wp_ajax_nopriv_policyms_description_editing', $plugin_public, 'description_editing_handler' );
		$this->loader->add_action( 'wp_ajax_policyms_description_approval', $plugin_public, 'description_approval_handler' );
		$this->loader->add_action( 'wp_ajax_nopriv_policyms_description_approval', $plugin_public, 'description_approval_handler' );
		$this->loader->add_action( 'wp_ajax_policyms_description_deletion', $plugin_public, 'description_deletion_handler' );
		$this->loader->add_action( 'wp_ajax_nopriv_policyms_description_deletion', $plugin_public, 'description_deletion_handler' );

		// Support for descriptions' assets.
		$this->loader->add_action( 'wp_ajax_policyms_asset_download', $plugin_public, 'asset_download_handler' );
		$this->loader->add_action( 'wp_ajax_nopriv_policyms_asset_download', $plugin_public, 'asset_download_handler' );
		$this->loader->add_action( 'wp_ajax_policyms_asset_delete', $plugin_public, 'asset_deletion_handler' );
		$this->loader->add_action( 'wp_ajax_nopriv_policyms_asset_delete', $plugin_public, 'asset_deletion_handler' );
		$this->loader->add_action( 'wp_ajax_policyms_set_description_image', $plugin_public, 'set_description_image_handler' );
		$this->loader->add_action( 'wp_ajax_nopriv_policyms_set_description_image', $plugin_public, 'set_description_image_handler' );
		$this->loader->add_action( 'wp_ajax_policyms_remove_description_image', $plugin_public, 'remove_description_image_handler' );
		$this->loader->add_action( 'wp_ajax_nopriv_policyms_remove_description_image', $plugin_public, 'remove_description_image_handler' );

		// Support for descriptions' reviews.
		$this->loader->add_action( 'wp_ajax_policyms_get_description_reviews', $plugin_public, 'get_description_reviews_handler' );
		$this->loader->add_action( 'wp_ajax_nopriv_policyms_get_description_reviews', $plugin_public, 'get_description_reviews_handler' );
		$this->loader->add_action( 'wp_ajax_policyms_create_review', $plugin_public, 'create_review_handler' );
		$this->loader->add_action( 'wp_ajax_nopriv_policyms_create_review', $plugin_public, 'create_review_handler' );
		$this->loader->add_action( 'wp_ajax_policyms_delete_review', $plugin_public, 'delete_review_handler' );
		$this->loader->add_action( 'wp_ajax_nopriv_policyms_delete_review', $plugin_public, 'delete_review_handler' );
	}

	/**
	 * Run the loader to execute all of the hooks with WordPress.
	 *
	 * @since    1.0.0
	 */
	public function run() {
		$this->loader->run();
	}

	/**
	 * The name of the plugin used to uniquely identify it within the context of
	 * WordPress and to define internationalization functionality.
	 *
	 * @since     1.0.0
	 * @return    string    The name of the plugin.
	 */
	public function get_plugin_name() {
		return $this->plugin_name;
	}

	/**
	 * The reference to the class that orchestrates the hooks with the plugin.
	 *
	 * @since     1.0.0
	 * @return    PolicyMS_Loader    Orchestrates the hooks of the plugin.
	 */
	public function get_loader() {
		return $this->loader;
	}

	/**
	 * Retrieve the version number of the plugin.
	 *
	 * @since     1.0.0
	 * @return    string    The version number of the plugin.
	 */
	public function get_version() {
		return $this->version;
	}
}
