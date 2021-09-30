<?php

/**
 * The public-facing functionality of the plugin.
 *
 * @link       https://dac.ds.unipi.gr/policycloud-eu/
 * @since      1.0.0
 *
 * @package    PolicyCloud_Marketplace
 * @subpackage PolicyCloud_Marketplace/public
 */

/**
 * The public-facing functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the public-facing stylesheet and JavaScript.
 *
 * @package    PolicyCloud_Marketplace
 * @subpackage PolicyCloud_Marketplace/public
 * @author     Your Name <email@example.com>
 */
class PolicyCloud_Marketplace_Public
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
	 * @since 	1.0.0
	 * @param	string    $plugin_name       The name of the plugin.
	 * @param	string    $version    The version of this plugin.
	 */
	public function __construct($plugin_name, $version)
	{
		$this->plugin_name = $plugin_name;
		$this->version = $version;
	}

	/**
	 * Register the stylesheets for the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_styles()
	{
		wp_enqueue_style($this->plugin_name, plugin_dir_url(__FILE__) . 'css/policycloud-marketplace-public.css', array(), $this->version, 'all');
	}

	/**
	 * Register the JavaScript for the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_scripts()
	{
		// Generic script.
		wp_enqueue_script($this->plugin_name, plugin_dir_url(__FILE__) . 'js/policycloud-marketplace-public.js', array('jquery'), $this->version, false);

		// Authorization related scripts.
		wp_register_script("policycloud-marketplace-registration", plugin_dir_url(__FILE__) . 'js/policycloud-marketplace-public-registration.js', array('jquery'), $this->version, false);
		wp_register_script("policycloud-marketplace-login", plugin_dir_url(__FILE__) . 'js/policycloud-marketplace-public-login.js', array('jquery'), $this->version, false);
		wp_enqueue_script("policycloud-marketplace-logout", plugin_dir_url(__FILE__) . 'js/policycloud-marketplace-public-logout.js', array('jquery'), $this->version, false);

		// Content related scripts.
		wp_register_script("policycloud-marketplace-create-description", plugin_dir_url(__FILE__) . 'js/policycloud-marketplace-public-create.js', array('jquery'), $this->version, false);
		wp_register_script("policycloud-marketplace-read-single", plugin_dir_url(__FILE__) . 'js/policycloud-marketplace-public-read-single.js', array('jquery'), $this->version, false);
		wp_register_script("policycloud-marketplace-account", plugin_dir_url(__FILE__) . 'js/policycloud-marketplace-public-account.js', array('jquery'), $this->version, false);
	}

	/**
	 * Register all the shortcodes concerning user authentication.
	 *
	 * @since    1.0.0
	 */
	public function add_authentication_shortcodes()
	{
		// Registration sequence.
		add_shortcode('policycloud-marketplace-registration', 'PolicyCloud_Marketplace_Public::registration_shortcode');

		// Log in sequence.
		add_shortcode('policycloud-marketplace-login', 'PolicyCloud_Marketplace_Public::login_shortcode');
	}

	/**
	 * Register the shortcodes for user registration.
	 *
	 * @since    1.0.0
	 */
	public static function registration_shortcode()
	{
		wp_enqueue_script("policycloud-marketplace-registration");

		// Check for existing token.
		// TODO: TEST.
		require_once plugin_dir_path(dirname(__FILE__)) . 'public/partials/policycloud-marketplace-authorization.php';
		try {
			if (retrieve_token()) {
				wp_localize_script('policycloud-marketplace-registration', 'ajax_prop', array(
					'error' => 'existing-token'
				));
			}
		} catch (\Exception $e) {
			$errorMessage =  $e->getMessage();
		}

		wp_localize_script('policycloud-marketplace-registration', 'ajax_prop', array(
			'ajax_url' => admin_url('admin-ajax.php'),
			'nonce' => wp_create_nonce('ajax_registration'),
			'error' => $errorMessage ?? ''
		));

		require_once plugin_dir_path(dirname(__FILE__)) . 'public/partials/policycloud-marketplace-public-display.php';
		registration_form_html();
	}

	/**
	 * Handle user registration AJAX requests.
	 *
	 * @uses 	PolicyCloud_Marketplace_Public::user_registration()
	 * @since	1.0.0
	 */
	public function user_registration_handler()
	{
		require_once plugin_dir_path(dirname(__FILE__)) . 'public/partials/policycloud-marketplace-authorization.php';

		// Verify WordPress generated nonce.
		if (!wp_verify_nonce($_POST['nonce'], 'ajax_registration')) {
			die("Unverified request to register user.");
		}

		// Attempt to register the user using POST data.
		try {
			die(json_encode([
				'status' => 'success',
				'data' => user_registration($_POST),
			]));
		} catch (Exception $e) {
			die(json_encode([
				'status' => 'failure',
				'data' => $e->getMessage()
			]));
		}
	}

	/**
	 * Handle user verification email AJAX requests.
	 *
	 * @uses 	user_email_verification_resend()
	 * @since	1.0.0
	 */
	public function user_email_verification_resend_handler()
	{
		require_once plugin_dir_path(dirname(__FILE__)) . 'public/partials/policycloud-marketplace-authorization.php';

		// Verify WordPress generated nonce.
		if (!wp_verify_nonce($_POST['nonce'], 'ajax_policycloud_account_editing_verification')) {
			die(json_encode([
				'status' => 'failure',
				'data' => "Unverified request to verify user email."
			]));
		}

		try {
			$token = retrieve_token(true);
			if (!empty($token)) {
				user_email_verification_resend($token['decoded']->account->verified ?? '', $token['decoded']->info->email ?? '');
				die(json_encode([
					'status' => 'success',
				]));
			} else throw new Exception("User token not found.");
		} catch (Exception $e) {
			die(json_encode([
				'status' => 'failure',
				'data' => $e->getMessage()
			]));
		}
	}


	/**
	 * Register the shortcode for user login.
	 *
	 * @since    1.0.0
	 */
	public static function login_shortcode()
	{
		wp_enqueue_script("policycloud-marketplace-login");

		// Check for existing token.
		// TODO: TEST.
		require_once plugin_dir_path(dirname(__FILE__)) . 'public/partials/policycloud-marketplace-authorization.php';
		try {
			if (retrieve_token()) {
				wp_localize_script('policycloud-marketplace-login', 'ajax_prop', array(
					'error' => 'existing-token'
				));
			}
		} catch (\Exception $e) {
			$errorMessage =  $e->getMessage();
		}

		wp_enqueue_script('policycloud-marketplace-login');
		wp_localize_script('policycloud-marketplace-login', 'ajax_prop', array(
			'ajax_url' => admin_url('admin-ajax.php'),
			'nonce' => wp_create_nonce('ajax_login'),
			'error' => $errorMessage ?? ''
		));

		require_once plugin_dir_path(dirname(__FILE__)) . 'public/partials/policycloud-marketplace-public-display.php';
		login_form_html();
	}

	/**
	 * Handle user login AJAX requests.
	 *
	 * @uses	PolicyCloud_Marketplace_Public::user_login()
	 * @since	1.0.0
	 */
	public function user_login_handler()
	{
		require_once plugin_dir_path(dirname(__FILE__)) . 'public/partials/policycloud-marketplace-authorization.php';

		// Verify WordPress generated nonce.
		if (!wp_verify_nonce($_POST['nonce'], 'ajax_login')) {
			die("Unverified request to register user.");
		}

		// Attempt to authorize the user using POST data.
		try {
			die(json_encode([
				'status' => 'success',
				'data' => user_login($_POST)
			]));
		} catch (Exception $e) {
			die(json_encode([
				'status' => 'failure',
				'data' => $e->getMessage()
			]));
		}
	}


	/**
	 * Add a menu item to a selected menu, which conditionally switches
	 * from log in to log out actions.
	 * 
	 * @since    1.0.0
	 */
	public static function add_conditional_access_menu_item($items, $args)
	{
		require_once plugin_dir_path(dirname(__FILE__)) . 'public/partials/policycloud-marketplace-authorization.php';

		// Retrieve credentials.
		$options = get_option('policycloud_marketplace_plugin_settings');
		if (empty($options['selected_menu']) || empty($options['login_page']) || empty($options['account_page']) || empty($options['registration_page'])) return $items;

		// Add conditional menu item.
		if ($args->theme_location == $options['selected_menu']) {
			try {
				if (!empty(retrieve_token())) {
					$link = '<a class="menu-link elementor-item" href="' . $options['account_page'] . '">My Account</a>';
					$link .= '<a class="menu-link elementor-item policycloud-logout">Log out</a>';
				} else {
					$link = '<a class="menu-link elementor-item" href="' . $options['login_page'] . '">Log In</a>';
					$link .= '<a class="menu-link elementor-item" href="' . $options['registration_page'] . '">Register</a>';
				}
			} catch (\Exception $e) {
				$link = '<a class="menu-link elementor-item" href="' . $options['login_page'] . '">Log In</a>';
			}
			return $items . '<li class="menu-item menu-item-type-post_type menu-item-object-page policycloud-access-button">' . $link . '</li>';
		} else return $items;
	}

	/**
	 * Register all the shortcodes concerning content handling.
	 *
	 * @since    1.0.0
	 */
	public function add_content_shortcodes()
	{

		// Read multiple objects sequence.
		add_shortcode('policycloud-marketplace-read-multiple', 'PolicyCloud_Marketplace_Public::read_multiple_objects');

		// Read single object sequence.
		add_shortcode('policycloud-marketplace-read-single', 'PolicyCloud_Marketplace_Public::read_single_object');

		// Create object sequence.
		add_shortcode('policycloud-marketplace-create-object', 'PolicyCloud_Marketplace_Public::create_object');

		// Account page shortcode.
		add_shortcode('policycloud-marketplace-account', 'PolicyCloud_Marketplace_Public::user_account');
	}

	/**
	 * Handle description editing AJAX requests.
	 *
	 * @uses	PolicyCloud_Marketplace_Public::description_editing()
	 * @since	1.0.0
	 */
	public function description_edit_handler()
	{
		// TODO @alexandrosraikos: Create AJAX script for description object editing.

		// Verify WordPress generated nonce.
		if (!wp_verify_nonce($_POST['nonce'], 'ajax_policycloud_description_editing_verification')) {
			die("Unverified request to edit description object.");
		}

		// Attempt to edit the description using POST data.
		try {
			require_once plugin_dir_path(dirname(__FILE__)) . 'public/partials/policycloud-marketplace-content.php';

			description_editing($_POST);
			die(json_encode([
				'status' => 'success'
			]));
		} catch (Exception $e) {
			die(json_encode([
				'status' => 'failure',
				'data' => $e->getMessage()
			]));
		}
	}

	/**
	 * Handle description creation AJAX requests.
	 *
	 * @uses	PolicyCloud_Marketplace_Public::description_creation()
	 * @since	1.0.0
	 */
	public function create_description_handler()
	{
		// TODO @alexandrosraikos: Create AJAX script for description object creation.

		// Verify WordPress generated nonce.
		if (!wp_verify_nonce($_POST['nonce'], 'ajax_policycloud_description_creation_verification')) {
			die("Unverified request to create description object.");
		}

		// Attempt to edit the description using POST data.
		try {
			require_once plugin_dir_path(dirname(__FILE__)) . 'public/partials/policycloud-marketplace-content.php';
			create_description($_POST);
			die(json_encode([
				'status' => 'success'
			]));
		} catch (Exception $e) {
			die(json_encode([
				'status' => 'failure',
				'data' => $e->getMessage()
			]));
		}
	}

	/**
	 * Display Description Object creation form for authenticated users.
	 *
	 * @since    1.0.0
	 */
	public static function create_object()
	{

		require_once plugin_dir_path(dirname(__FILE__)) . 'public/partials/policycloud-marketplace-authorization.php';
		try {
			// Get specific Description data for authorized users.
			$token = retrieve_token();
			if (empty($token)) $error = "You need to be logged in to create a Description Object.";
		} catch (Exception $e) {
			$error = $e->getMessage();
		}

		wp_enqueue_script("policycloud-marketplace-create-description");
		wp_localize_script('policycloud-marketplace-create-description', 'ajax_properties_description_creation', array(
			'ajax_url' => admin_url('admin-ajax.php'),
			'nonce' => wp_create_nonce('ajax_policycloud_description_creation_verification'),
		));

		require_once plugin_dir_path(dirname(__FILE__)) . 'public/partials/policycloud-marketplace-public-display.php';
		create_object_html($error ?? '');
	}

	/**
	 * Display multiple Description Objects for visitors and authenticated users.
	 *
	 * @since    1.0.0
	 */
	public static function read_multiple_objects()
	{
		require_once plugin_dir_path(dirname(__FILE__)) . 'public/partials/policycloud-marketplace-authorization.php';
		require_once plugin_dir_path(dirname(__FILE__)) . 'public/partials/policycloud-marketplace-content.php';

		try {
			// Retrieve all public descriptions based on GET parameter filtering.
			$descriptions = get_descriptions($_GET);
		} catch (ErrorException $e) {
			$notice = $e->getMessage();
		} catch (Exception $e) {
			$error = $e->getMessage();
		}

		// Retrieve description page URL.
		$options = get_option('policycloud_marketplace_plugin_settings');
		if (empty($options['description_page'])) $error = "You have not set a Description page in your PolicyCloud Marketplace settings.";

		// Print response data to front end.
		wp_enqueue_script("policycloud-marketplace-read-multiple", plugin_dir_url(__FILE__) . 'js/policycloud-marketplace-public-read-multiple.js', array('jquery'));
		require_once plugin_dir_path(dirname(__FILE__)) . 'public/partials/policycloud-marketplace-public-display.php';


		// TODO @elefkour: handle 'notice' arg.
		read_multiple_html($descriptions, [
			"authenticated" => $authenticated ?? false,
			"description_url" => $options['description_page'],
			"error" => $error ?? null,
			"notice" => $notice ?? null
		]);
	}

	/**
	 * Display a single description object for authenticated users.
	 *
	 * @since    1.0.0
	 */
	public static function read_single_object()
	{
		require_once plugin_dir_path(dirname(__FILE__)) . 'public/partials/policycloud-marketplace-authorization.php';
		require_once plugin_dir_path(dirname(__FILE__)) . 'public/partials/policycloud-marketplace-content.php';

		try {
			// Get specific Description data for authorized users.
			$token = retrieve_token(true);
			if (!empty($token)) {
				$description = get_specific_description($_GET['did'], $token['encoded']);

				// Specify Description ownership.
				$owner = ($description['info']['provider'] == $token['decoded']->username);
			} else $description = get_specific_description($_GET['did']);
		} catch (Exception $e) {
			$error = $e->getMessage();
		}

		require_once plugin_dir_path(dirname(__FILE__)) . 'public/partials/policycloud-marketplace-public-display.php';
		wp_enqueue_script('policycloud-marketplace-read-single');
		wp_localize_script('policycloud-marketplace-read-single', 'ajax_properties_description_editing', array(
			'ajax_url' => admin_url('admin-ajax.php'),
			'nonce' => wp_create_nonce('ajax_policycloud_description_editing_verification'),
			'description_id' => $_GET['did']
		));

		read_single_html($description, [
			"authenticated" => !empty($token ?? null),
			"is_owner" => $owner ?? false,
			"error" => $error ?? '',
		]);
	}

	/**
	 * Display the account page for authenticated users.
	 *
	 * @since    1.0.0
	 */
	public static function user_account()
	{
		require_once plugin_dir_path(dirname(__FILE__)) . 'public/partials/policycloud-marketplace-authorization.php';

		try {
			// Get specific Description data for authorized users.
			$token = retrieve_token(true);
			if (!empty($token)) {
				require_once plugin_dir_path(dirname(__FILE__)) . 'public/partials/policycloud-marketplace-content.php';

				// Check for verification email.
				if (!empty($_GET['verification-code'])) {
					if ($_GET['verification-code'] == $token['decoded']->account->verified) {
						$verified_token = verify_user($_GET['verification-code']);
					} else if ($token['decoded']->account->verified == 1) {
						throw new Exception("This account is already verified.");
					}
				}

				// Specify Description ownership.
				$descriptions = get_descriptions([
					'provider' => $token['decoded']->username
				]);
			} else {
				$error = "not-logged-in";
			}
		} catch (ErrorException $e) {
		} catch (Exception $e) {
			$error = $e->getMessage();
		}

		// Retrieve credentials.
		$options = get_option('policycloud_marketplace_plugin_settings');

		wp_enqueue_script('policycloud-marketplace-account');
		wp_localize_script('policycloud-marketplace-account', 'ajax_properties_account_editing', array(
			'ajax_url' => admin_url('admin-ajax.php'),
			'nonce' => wp_create_nonce('ajax_policycloud_account_editing_verification'),
			'verified_token' => $verified_token ?? null,
		));
		require_once plugin_dir_path(dirname(__FILE__)) . 'public/partials/policycloud-marketplace-public-display.php';
		user_account_html($token['decoded'] ?? false, $descriptions ?? null, [
			"error" => $error ?? '',
			"notice" => $notice ?? 'TEST!',
			"login_page" => $options['login_page'] ?? '',
			"registration_page" => $options['registration_page'] ?? '',
			"description_page" => $options['description_page']
		]);
	}

	/**
	 * Handle user account editing AJAX requests.
	 *
	 * @uses 	PolicyCloud_Marketplace_Public::user_registration()
	 * @since	1.0.0
	 */
	public function account_edit_handler()
	{
		require_once plugin_dir_path(dirname(__FILE__)) . 'public/partials/policycloud-marketplace-authorization.php';

		// Verify WordPress generated nonce.
		if (!wp_verify_nonce($_POST['nonce'], 'ajax_policycloud_account_editing_verification')) {
			die("Unverified request to edit account.");
		}

		// Attempt to register the user using POST data.
		try {
			die(json_encode([
				'status' => 'success',
				'data' => account_edit($_POST)
			]));
		} catch (Exception $e) {
			die(json_encode([
				'status' => 'failure',
				'data' => $e->getMessage()
			]));
		}
	}
}
