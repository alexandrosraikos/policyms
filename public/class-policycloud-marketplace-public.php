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
 * @author     Alexandros Raikos <araikos@unipi.gr>
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
	 * 
	 * Generic
	 * 
	 * This section refers to global functionality.
	 * 
	 */

	public function enqueue_head_scripts()
	{
		echo '<script>FontAwesomeConfig = { autoA11y: true }</script><script src="https://use.fontawesome.com/releases/v5.15.4/js/all.js"></script>';
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
	 * @since	1.0.0
	 * @author	Alexandros Raikos <araikos@unipi.gr>
	 */
	public function enqueue_scripts()
	{
		// Generic script.
		wp_enqueue_script("policycloud-marketplace", plugin_dir_url(__FILE__) . 'js/policycloud-marketplace-public.js', array('jquery'), $this->version, false);
		wp_localize_script("policycloud-marketplace", 'GlobalProperties', array(
			"rootURLPath" => (empty(parse_url(get_site_url())['path']) ? "/" : parse_url(get_site_url())['path'])
		));

		// Accounts related scripts.
		wp_register_script("policycloud-marketplace-account-registration", plugin_dir_url(__FILE__) . 'js/policycloud-marketplace-public-account-registration.js', array('jquery', 'policycloud-marketplace'), $this->version, false);
		wp_register_script("policycloud-marketplace-account-authorization", plugin_dir_url(__FILE__) . 'js/policycloud-marketplace-public-account-authorization.js', array('jquery', 'policycloud-marketplace'), $this->version, false);
		wp_register_script("policycloud-marketplace-account", plugin_dir_url(__FILE__) . 'js/policycloud-marketplace-public-account.js', array('jquery', 'policycloud-marketplace'), $this->version, false);

		// Content related scripts.
		wp_register_script("policycloud-marketplace-asset", plugin_dir_url(__FILE__) . 'js/policycloud-marketplace-public-asset.js', array('jquery', 'policycloud-marketplace'), $this->version, false);
		wp_register_script("policycloud-marketplace-asset-archive", plugin_dir_url(__FILE__) . 'js/policycloud-marketplace-public-asset-archive.js', array('jquery', 'policycloud-marketplace'), $this->version, false);
		wp_register_script("policycloud-marketplace-asset-creation", plugin_dir_url(__FILE__) . 'js/policycloud-marketplace-public-asset-creation.js', array('jquery', 'policycloud-marketplace'), $this->version, false);
	}

	/**
	 * 
	 * Accounts
	 * 
	 * This section refers to functionality and shortcodes relevant to user accounts.
	 * 
	 */

	/**
	 * Register all the shortcodes concerning user authorization.
	 *
	 * @since	1.0.0
	 * @author	Alexandros Raikos <araikos@unipi.gr>
	 */
	public function add_accounts_shortcodes()
	{
		// Registration sequence.
		add_shortcode('policycloud-marketplace-registration', 'PolicyCloud_Marketplace_Public::account_registration_shortcode');

		// Log in sequence.
		add_shortcode('policycloud-marketplace-login', 'PolicyCloud_Marketplace_Public::account_authorization_shortcode');
	}

	/**
	 * Add a menu item to a selected menu, which conditionally switches
	 * from log in to log out actions.
	 *
	 * @since	1.0.0
	 * @author	Alexandros Raikos <araikos@unipi.gr>
	 */
	public static function add_conditional_access_menu_item($items, $args)
	{
		require_once plugin_dir_path(dirname(__FILE__)) . 'public/class-policycloud-marketplace-user.php';

		// Retrieve credentials.
		$options = get_option('policycloud_marketplace_plugin_settings');
		if (
			empty($options['selected_menu']) ||
			empty($options['login_page']) ||
			empty($options['account_page']) ||
			empty($options['registration_page'] ||
				empty($options['upload_page']))
		) return $items;

		if (!function_exists('list_url_wrap')) {
			function list_url_wrap($url)
			{
				$random_id = rand(1000, 10000);
				return '<li id="menu-item-' . $random_id . '" class="menu-item menu-item-type-post_type menu-item-object-page menu-item-' . $random_id . '">' . $url . '</li>';
			}
		}

		// Add conditional menu item.
		if ($args->theme_location == $options['selected_menu']) {
			try {
				if (!empty(retrieve_token())) {
					$links = list_url_wrap('<a href="' . $options['upload_page'] . '">Create</a>');
					$links .= list_url_wrap('<a href="' . $options['account_page'] . '">My Account</a>');
					$links .= list_url_wrap('<a class="policycloud-logout">Log out</a>');
				} else {
					$links = list_url_wrap('<a href="' . $options['login_page'] . '">Log In</a>');
					$links .= list_url_wrap('<a href="' . $options['registration_page'] . '">Register</a>');
				}
			} catch (\Exception $e) {
				$links = list_url_wrap('<a href="' . $options['login_page'] . '">Log In</a>');
				$links .= list_url_wrap('<a href="' . $options['registration_page'] . '">Register</a>');
			}
			return $items . $links;
		} else return $items;
	}

	/**
	 * Register the shortcodes for user registration.
	 *
	 * @since	1.0.0
	 * @author	Alexandros Raikos <araikos@unipi.gr>
	 */
	public static function account_registration_shortcode()
	{

		// Check for existing token.
		require_once plugin_dir_path(dirname(__FILE__)) . 'public/class-policycloud-marketplace-user.php';
		try {
			// Retrieve credentials.
			$options = get_option('policycloud_marketplace_plugin_settings');
			if (empty($options['account_page'])) throw new Exception("There is no account page set in the PolicyCloud Marketplace settings, please contact your administrator.");
			if (empty($options['login_page'])) throw new Exception("There is no log in page set in the PolicyCloud Marketplace settings, please contact your administrator.");
			if (empty($options['tos_url'])) throw new Exception("There is no Terms of Service URL set in the PolicyCloud Marketplace settings, please contact your administrator.");
			if (retrieve_token()) {
				$logged_in = true;
			}
		} catch (\Exception $e) {
			$logged_in = false;
			$error = $e->getMessage();
		}

		wp_enqueue_script("policycloud-marketplace-account-registration");
		wp_localize_script('policycloud-marketplace-account-registration', 'ajax_properties_account_registration', array(
			'ajax_url' => admin_url('admin-ajax.php'),
			'nonce' => wp_create_nonce('ajax_registration'),
			'redirect_page' => $options['account_page']
		));

		require_once plugin_dir_path(dirname(__FILE__)) . 'public/partials/policycloud-marketplace-public-display.php';
		account_registration_html($options['login_page'], $logged_in ?? false, $options['tos_url'] ?? '', $error ?? '');
	}

	/**
	 * Handle user registration AJAX requests.
	 *
	 * @uses 	PolicyCloud_Marketplace_Public::account_registration()
	 *
	 * @since	1.0.0
	 * @author	Alexandros Raikos <araikos@unipi.gr>
	 */
	public function account_registration_handler()
	{
		require_once plugin_dir_path(dirname(__FILE__)) . 'public/class-policycloud-marketplace-user.php';

		// Verify WordPress generated nonce.
		if (!wp_verify_nonce($_POST['nonce'], 'ajax_registration')) {
			http_response_code(403);
			die("Unverified request to register user.");
		}

		try {
			// Respond with data.
			$registration = account_registration([
				'username' => $_POST['username'],
				'password' => stripslashes($_POST['password']),
				'password-confirm' => stripslashes($_POST['password-confirm']),
				'name' => stripslashes($_POST['name']),
				'surname' => stripslashes($_POST['surname']),
				'title' => $_POST['title'] ?? '',
				'gender' => $_POST['gender'] ?? '',
				'organization' => stripslashes($_POST['organization'] ?? ''),
				'email' => $_POST['email'],
				'phone' => $_POST['phone'] ?? '',
				'social-title' => stripslashes($_POST['social-title'] ?? ''),
				'social-url' => $_POST['social-url'] ?? '',
				'about' => $_POST['about'] ?? '',
			]);
			http_response_code(200);
			die(json_encode([
				"newToken" => $registration['new_token'],
				"warningMessage" => $registration['warning']
			]));
		} catch (RuntimeException $e) {
			http_response_code(400);
			die($e->getMessage());
		} catch (InvalidArgumentException $e) {
			http_response_code(404);
			die($e->getMessage());
		} catch (ErrorException $e) {
			http_response_code(500);
			die($e->getMessage());
		}
	}


	/**
	 * Register the shortcode for account authorization.
	 *
	 * @since	1.0.0
	 * @author	Alexandros Raikos <araikos@unipi.gr>
	 */
	public static function account_authorization_shortcode()
	{

		$options = get_option('policycloud_marketplace_plugin_settings');
		if (empty($options['registration_page'])) throw new Exception("There is no log in page set in the PolicyCloud Marketplace settings, please contact your administrator.");


		// Check for existing token.
		require_once plugin_dir_path(dirname(__FILE__)) . 'public/class-policycloud-marketplace-user.php';
		try {
			if (retrieve_token()) {
				$logged_in = true;
			}
		} catch (\Exception $e) {
			$logged_in = false;
		}

		wp_enqueue_script("policycloud-marketplace-account-authorization");
		wp_localize_script('policycloud-marketplace-account-authorization', 'ajax_properties_account_authorization', array(
			'ajax_url' => admin_url('admin-ajax.php'),
			'nonce' => wp_create_nonce('ajax_login')
		));

		require_once plugin_dir_path(dirname(__FILE__)) . 'public/partials/policycloud-marketplace-public-display.php';
		account_authorization_html($options['registration_page'], $logged_in ?? false);
	}

	/**
	 * Handle user login AJAX requests.
	 *
	 * @uses	PolicyCloud_Marketplace_Public::account_authorization()
	 *
	 * @since	1.0.0
	 * @author	Alexandros Raikos <araikos@unipi.gr>
	 */
	public function account_authorization_handler()
	{
		require_once plugin_dir_path(dirname(__FILE__)) . 'public/class-policycloud-marketplace-user.php';

		// Verify WordPress generated nonce.
		if (!wp_verify_nonce($_POST['nonce'], 'ajax_login')) {
			http_response_code(403);
			die("Unverified request to register user.");
		}

		// Attempt to authorize the user using POST data.
		try {
			http_response_code(200);
			die(json_encode(account_authorization([
				'username-email' => stripslashes($_POST['username-email']),
				'password' => stripslashes($_POST['password'])
			])));
		} catch (InvalidArgumentException $e) {
			http_response_code(404);
			die($e->getMessage());
		} catch (ErrorException $e) {
			http_response_code(500);
			die($e->getMessage());
		} catch (Exception $e) {
			http_response_code(501);
			die($e->getMessage());
		}
	}

	/**
	 * Requests account related content to display for authenticated users.
	 * 
	 * @uses	retrieve_token()
	 * @uses	verify_user()
	 * @uses	get_user_information()
	 * @uses	get_user_descriptions()
	 * @uses	get_user_statistics()
	 * @uses	account_html()
	 *
	 * @since	1.0.0
	 * @author	Alexandros Raikos <araikos@unipi.gr>
	 */
	public static function account_shortcode()
	{
		require_once plugin_dir_path(dirname(__FILE__)) . 'public/class-policycloud-marketplace-user.php';

		// Retrieve credentials.
		$options = get_option('policycloud_marketplace_plugin_settings');
		if (
			empty($options['login_page']) ||
			empty($options['registration_page']) ||
			empty($options['description_page']) ||
			empty($options['archive_page']) ||
			empty($options['upload_page'])
		) {
			$error = 'Please update your PolicyCloud Marketplace settings in the WordPress Dashboard.';
		}

		try {
			// Authorize.
			$token = retrieve_token(true);
			if (!empty($token)) {
				require_once plugin_dir_path(dirname(__FILE__)) . 'public/partials/policycloud-marketplace-content.php';

				// Get information.
				if (empty($_GET['user'])) {

					// Check for verification code email redirect.
					if (!empty($_GET['verification-code'])) {
						if ($_GET['verification-code'] == $token['decoded']['account']['verified']) {
							$verified_token = verify_user($_GET['verification-code']);
							if (!empty($verified_token)) {
								$notice = "Your email address was successfully verified.";
							}
						} else if ($token['decoded']['account']['verified'] == 1) {
							throw new Exception("This account is already verified.");
						}
					}

					$account_information = $token['decoded'];
					$visiting = false;
				} else {
					$visiting = true;
					$account_information = get_user_information($_GET['user'], $token['encoded']);
				}
				$is_admin = (($token['decoded']['account']['role'] ?? '') == 'admin');

				// Get content.
				if (!empty($account_information)) {

					// Get user profile picture.
					if ($account_information['profile_parameters']['profile_image'] != 'default_image_users') {
						$picture = get_user_picture($account_information['profile_parameters']['profile_image'], $token['encoded']);
					}

					// Get user descriptions.
					$descriptions = get_account_assets($account_information['username'], $token['encoded'] ?? null, [
						'page' => $_GET['page'] ?? null,
						'items_per_page' => $_GET['items_per_page'] ?? null,
						'sort_by' => $_GET['sort_by'] ?? null,
					]);

					// Get user reviews.
					$reviews = get_account_reviews($account_information['username'], $token['encoded'] ?? null, [
						'page' => $_GET['page'] ?? null,
						'items_per_page' => $_GET['items_per_page'] ?? null,
						'sort_by' => $_GET['sort_by'] ?? null,
					]);

					if ($is_admin && !$visiting) {
						// Get admin approvals.
						$approvals = get_pending_assets($token['encoded']);
					}
				}
			} else $notice = 'You are not logged in, please <a href="' . $options['login_page'] . '">log in</a> to your account. Don\'t have an account yet? You can <a href="' . $options['registration_page'] . '">register</a> here.';
			if (!empty($account_information)) {
				$statistics = get_user_statistics(($visiting) ? $_GET['user'] : $token['decoded']['username'], $token['encoded'] ?? null);
			}
		} catch (Exception $e) {
			$error = $e->getMessage();
		}

		// Localize script.
		wp_enqueue_script('policycloud-marketplace-account');
		wp_localize_script('policycloud-marketplace-account', 'ajax_properties_account_editing', array(
			'ajax_url' => admin_url('admin-ajax.php'),
			'nonce' => wp_create_nonce('ajax_policycloud_account_editing_verification'),
			'verified_token' => $verified_token ?? null,
			'user_id' => $_GET['user'] ?? $token['decoded']['username'] ?? '',
		));

		// Print shortcode HTML.
		require_once plugin_dir_path(dirname(__FILE__)) . 'public/partials/policycloud-marketplace-public-display.php';
		account_html(
			$account_information ?? [],
			$picture ?? null,
			$statistics ?? [],
			$descriptions ?? [],
			$reviews ?? [],
			$approvals ?? [],
			[
				"is_admin" => $is_admin ?? false,
				"visiting" => $visiting ?? false,
				"error" => $error ?? '',
				"notice" => $notice ?? '',
				"description_page" => $options['description_page'],
				"archive_page" => $options['archive_page'],
				"upload_page" => $options['upload_page']
			]
		);
	}

	/**
	 * Handle user account editing AJAX requests.
	 *
	 * @uses 	PolicyCloud_Marketplace_Public::account_registration()
	 * 
	 * @since	1.0.0
	 * @author 	Alexandros Raikos <araikos@unipi.gr>
	 */
	public function account_editing_handler()
	{
		require_once plugin_dir_path(dirname(__FILE__)) . 'public/class-policycloud-marketplace-user.php';

		// Verify WordPress generated nonce.
		if (!wp_verify_nonce($_POST['nonce'], 'ajax_policycloud_account_editing_verification')) {
			http_response_code(403);
			die("Unverified request to edit account.");
		}

		try {
			$token = retrieve_token(true);
			if (!empty($token)) {
				$is_admin = ($token['decoded']['account']['role'] == 'admin');
				$visiting = ($token['decoded']['username'] != $_POST['username']);

				if (!empty($_POST['subsequent_action'])) {
					if ($_POST['subsequent_action'] == 'edit_account') {
						// Respond with data.
						$updated_token = account_edit($_POST['username'], $token['encoded']);
						http_response_code(200);
						if ($is_admin && $visiting) die();
						else die(json_encode($updated_token));
					}
					if ($_POST['subsequent_action'] == 'delete_profile_picture') {
						$updated_token = delete_user_picture($_POST['username'], $token['encoded']);
						http_response_code(200);
						if ($is_admin && $visiting) {
							die();
						} else {
							die(json_encode($updated_token));
						}
					}
				} else {
					throw new RuntimeException("No subsequent action was defined.");
				}
			} else {
				http_response_code(404);
				die("User token not found.");
			}
		} catch (RuntimeException $e) {
			http_response_code(400);
			die($e->getMessage());
		} catch (InvalidArgumentException $e) {
			http_response_code(404);
			die($e->getMessage());
		} catch (JsonException $e) {
			http_response_code(440);
			die();
		} catch (ErrorException $e) {
			http_response_code(500);
			die($e->getMessage());
		} catch (LogicException $e) {
			http_response_code(501);
			die($e->getMessage());
		}
	}

	/**
	 * Handle user verification email AJAX requests.
	 *
	 * @uses 	user_email_verification_resend()
	 *
	 * @since	1.0.0
	 * @author	Alexandros Raikos <araikos@unipi.gr>
	 */
	public function account_email_verification_resend_handler()
	{
		require_once plugin_dir_path(dirname(__FILE__)) . 'public/class-policycloud-marketplace-user.php';

		// Verify WordPress generated nonce.
		if (!wp_verify_nonce($_POST['nonce'], 'ajax_policycloud_account_editing_verification')) {
			http_response_code(403);
			die("Unverified request to verify user email.");
		}

		try {
			$token = retrieve_token(true);
			if (!empty($token)) {
				user_email_verification_resend($token['decoded']['account']['verified'] ?? '', $token['decoded']['info']['email'] ?? '');
				http_response_code(200);
			} else {
				http_response_code(404);
				die("User token not found.");
			}
		} catch (RuntimeException $e) {
			http_response_code(400);
			die($e->getMessage());
		} catch (InvalidArgumentException $e) {
			http_response_code(404);
			die($e->getMessage());
		} catch (JsonException $e) {
			http_response_code(440);
			die();
		} catch (ErrorException $e) {
			http_response_code(500);
			die($e->getMessage());
		}
	}

	/**
	 * Handle user account editing AJAX requests.
	 *
	 * @since	1.0.0
	 * @author	Alexandros Raikos <araikos@unipi.gr>
	 */
	public function account_data_request_handler()
	{
		require_once plugin_dir_path(dirname(__FILE__)) . 'public/class-policycloud-marketplace-user.php';

		// Verify WordPress generated nonce (using the same as account editing).
		if (!wp_verify_nonce($_POST['nonce'], 'ajax_policycloud_account_editing_verification')) {
			http_response_code(403);
			die("Unverified request of account data.");
		}

		try {
			$token = retrieve_token(true);
			if (!empty($token)) {
				require_once plugin_dir_path(dirname(__FILE__)) . 'public/partials/policycloud-marketplace-content.php';

				// Respond with data.
				http_response_code(200);
				die(json_encode([
					'information' => $token['decoded'],
					'assets' => get_account_assets($token['decoded']['username'], $token['encoded'])
				]));
			} else {
				http_response_code(404);
				die("User token not found.");
			}
		} catch (InvalidArgumentException $e) {
			http_response_code(404);
			die($e->getMessage());
		} catch (JsonException $e) {
			http_response_code(440);
			die();
		} catch (ErrorException $e) {
			http_response_code(500);
			die($e->getMessage());
		} catch (LogicException $e) {
			http_response_code(501);
			die($e->getMessage());
		}
	}

	/**
	 * Handle user account deletion AJAX requests.
	 *
	 * @since	1.0.0
	 * @author	Alexandros Raikos <araikos@unipi.gr>
	 */
	public function account_deletion_handler()
	{
		require_once plugin_dir_path(dirname(__FILE__)) . 'public/class-policycloud-marketplace-user.php';

		// Verify WordPress generated nonce (using the same as account editing due to them being in the same page).
		if (!wp_verify_nonce($_POST['nonce'], 'ajax_policycloud_account_editing_verification')) {
			http_response_code(403);
			die("Unverified request to delete the account.");
		}

		try {
			$token = retrieve_token(true);
			if (!empty($token)) {
				// Prepare data from $_POST
				if (account_deletion($token['decoded']['username'], $token['encoded'], $_POST['current_password'])) {
					http_response_code(200);
				}
			} else {
				http_response_code(404);
				die("User token not found.");
			}
		} catch (InvalidArgumentException $e) {
			http_response_code(404);
			die($e->getMessage());
		} catch (JsonException $e) {
			http_response_code(440);
			die();
		} catch (ErrorException $e) {
			http_response_code(500);
			die($e->getMessage());
		} catch (LogicException $e) {
			http_response_code(501);
			die($e->getMessage());
		}
	}

	/**
	 * 
	 * Content
	 * 
	 * This section refers to functionality and shortcodes relevant to content.
	 * 
	 */

	/**
	 * Register all the shortcodes concerning content handling.
	 *
	 * @since	1.0.0
	 * @author	Alexandros Raikos <araikos@unipi.gr>
	 */
	public function add_content_shortcodes()
	{

		// Read multiple objects sequence.
		add_shortcode('policycloud-marketplace-read-multiple', 'PolicyCloud_Marketplace_Public::assets_archive_shortcode');

		// Read single object sequence.
		add_shortcode('policycloud-marketplace-read-single', 'PolicyCloud_Marketplace_Public::asset_shortcode');

		// Create object sequence.
		add_shortcode('policycloud-marketplace-create-object', 'PolicyCloud_Marketplace_Public::asset_creation_shortcode');

		// Account page shortcode.
		add_shortcode('policycloud-marketplace-account', 'PolicyCloud_Marketplace_Public::account_shortcode');
	}

	/**
	 * Display multiple Description Objects for visitors and authenticated users.
	 *
	 * @since 	1.0.0
	 * @author 	Alexandros Raikos <araikos@unipi.gr>
	 */
	public static function assets_archive_shortcode()
	{
		require_once plugin_dir_path(dirname(__FILE__)) . 'public/class-policycloud-marketplace-user.php';
		require_once plugin_dir_path(dirname(__FILE__)) . 'public/partials/policycloud-marketplace-content.php';

		try {
			// Retrieve all public descriptions based on GET parameter filtering.
			$assets = get_assets($_GET);
			$filters = get_filtering_values();
		} catch (ErrorException $e) {
			$notice = $e->getMessage();
		} catch (Exception $e) {
			$error = $e->getMessage();
		}

		// Retrieve description page URL.
		$options = get_option('policycloud_marketplace_plugin_settings');
		if (empty($options['description_page'])) $error = "You have not set a Description page in your PolicyCloud Marketplace settings.";

		// Print response data to front end.
		wp_enqueue_script("policycloud-marketplace-asset-archive");
		require_once plugin_dir_path(dirname(__FILE__)) . 'public/partials/policycloud-marketplace-public-display.php';

		assets_archive_html($assets ?? [], $filters ?? [], [
			"authenticated" => $authenticated ?? false,
			"asset_url" => $options['description_page'],
			"error" => $error ?? null,
			"notice" => $notice ?? null
		]);
	}

	/**
	 * Display a single description object for authenticated users.
	 *
	 * @since	1.0.0
	 * @author	Alexandros Raikos <araikos@unipi.gr>
	 */
	public static function asset_shortcode()
	{
		require_once plugin_dir_path(dirname(__FILE__)) . 'public/class-policycloud-marketplace-user.php';
		require_once plugin_dir_path(dirname(__FILE__)) . 'public/partials/policycloud-marketplace-content.php';

		try {
			// Get specific Description data for authorized users.
			$token = retrieve_token(true);
			if (!empty($token)) {
				$asset = get_asset($_GET['did'], $token['encoded']);

				// Specify Description ownership.
				$owner = ($asset['results'][0][0]['metadata']['provider'] == $token['decoded']['username']);
				$admin = (($token['decoded']['account']['role'] ?? '') == 'admin');
			} else $asset = get_asset($_GET['did']);
		} catch (Exception $e) {
			$error = $e->getMessage();
			try {
				$asset = get_asset($_GET['did']);
			} catch (Exception $e) {
				$error = $e->getMessage();
			}
		}

		// Get asset images.
		if (!empty($asset)) {
			try {
				if (!empty($token)) {
					$images = [];
					foreach ($asset['results'][0][0]['assets']['images'] as $image) {
						array_push($images, get_asset_image($image['id'], $token['encoded']));
					}
				}
			} catch (Exception $e) {
				$error = $e->getMessage();
			}
		}

		// Retrieve login page URL.
		$options = get_option('policycloud_marketplace_plugin_settings');
		if (empty($options['login_page'])) $error = "You have not set a log in page in your PolicyCloud Marketplace settings.";
		if (empty($options['account_page'])) $error = "You have not set an account page in your PolicyCloud Marketplace settings.";
		if (empty($options['archive_page'])) $error = "You have not set an archive page in your PolicyCloud Marketplace settings.";

		require_once plugin_dir_path(dirname(__FILE__)) . 'public/partials/policycloud-marketplace-public-display.php';
		wp_enqueue_script('policycloud-marketplace-asset');
		wp_localize_script('policycloud-marketplace-asset', 'ajax_properties_description_editing', array(
			'ajax_url' => admin_url('admin-ajax.php'),
			'nonce' => wp_create_nonce('ajax_policycloud_description_editing_verification'),
			'asset_id' => $_GET['did']
		));

		asset_html($asset['results'][0][0] ?? [], $images ?? [], [
			"is_authenticated" => !empty($token ?? null),
			"is_owner" => $owner ?? false,
			"is_admin" => $admin ?? false,
			"login_page" => $options['login_page'] ?? "",
			"account_page" => $options['account_page'] ?? "",
			"archive_page" => $options['archive_page'] ?? "",
			"error" => $error ?? '',
		]);
	}

	/**
	 * Handle description editing AJAX requests.
	 *
	 * @uses	PolicyCloud_Marketplace_Public::description_editing()
	 *
	 * @since	1.0.0
	 * @author	Alexandros Raikos <araikos@unipi.gr>
	 */
	public function asset_editing_handler()
	{

		// Verify WordPress generated nonce.
		if (!wp_verify_nonce($_POST['nonce'], 'ajax_policycloud_description_editing_verification')) {
			http_response_code(403);
			die("Unverified request to edit this asset.");
		}

		try {
			require_once plugin_dir_path(dirname(__FILE__)) . 'public/class-policycloud-marketplace-user.php';
			$token = retrieve_token();
			if (!empty($token)) {
				require_once plugin_dir_path(dirname(__FILE__)) . 'public/partials/policycloud-marketplace-content.php';

				if (!empty($_POST['subsequent_action'])) {
					if ($_POST['subsequent_action'] == "asset-editing") {
						// Forward editing request and respond.
						$response = edit_asset($_POST['asset_id'], $_POST, $token);
						http_response_code(200);
						die(json_encode($response));
					}
					if ($_POST['subsequent_action'] == "file-deletion") {
						if (!empty($_POST['file-type'])) {
							if (delete_asset_file($_POST['file-type'], $_POST['file-identifier'], $token)) {
								http_response_code(200);
								die();
							}
						} else throw new RuntimeException('No file type was defined.');
					}
					if ($_POST['subsequent_action'] == "file-download") {
						if (!empty($_POST['file-type'])) {
							$options = get_option('policycloud_marketplace_plugin_settings');
							if (empty($options['marketplace_host'])) {
								throw new RuntimeException("No Marketplace Host was defined in the WordPress Settings.");
							} else {
								$download_otc = get_asset_file_url($_POST['file-type'], $_POST['file-identifier'], $token);
								http_response_code(200);
								$url = 'https://' . $options['marketplace_host'] . '/assets/download/' . $download_otc;
								die(json_encode([
									"url" => $url
								]));
							}
						}
					}
				}
			} else {
				http_response_code(404);
				die("User token not found.");
			}
		} catch (RuntimeException $e) {
			http_response_code(400);
			die($e->getMessage());
		} catch (InvalidArgumentException $e) {
			http_response_code(404);
			die($e->getMessage());
		} catch (JsonException $e) {
			http_response_code(440);
			die();
		} catch (ErrorException $e) {
			http_response_code(500);
			die($e->getMessage());
		}
	}

	/**
	 * Handle description approval AJAX requests.
	 *
	 * @uses	PolicyCloud_Marketplace_Public::description_approval()
	 *
	 * @since	1.0.0
	 * @author	Alexandros Raikos <araikos@unipi.gr>
	 */
	function asset_approval_handler()
	{
		// Verify WordPress generated nonce.
		if (!wp_verify_nonce($_POST['nonce'], 'ajax_policycloud_description_editing_verification')) {
			http_response_code(403);
			die("Unverified request to approve this asset.");
		}

		try {
			require_once plugin_dir_path(dirname(__FILE__)) . 'public/class-policycloud-marketplace-user.php';
			$token = retrieve_token();
			if (!empty($token)) {
				require_once plugin_dir_path(dirname(__FILE__)) . 'public/partials/policycloud-marketplace-content.php';
				$approval = $_POST['approval'] ?? '';
				$did = $_POST['did'] ?? '';
				if (approve_asset($did, $approval, $token)) {
					http_response_code(200);
					die();
				}
			} else {
				http_response_code(404);
				die("User token not found.");
			}
		} catch (RuntimeException $e) {
			http_response_code(400);
			die($e->getMessage());
		} catch (InvalidArgumentException $e) {
			http_response_code(404);
			die($e->getMessage());
		} catch (JsonException $e) {
			http_response_code(440);
			die();
		} catch (ErrorException $e) {
			http_response_code(500);
			die($e->getMessage());
		}
	}

	/**
	 * Display the asset creation form for authenticated users.
	 *
	 * @since	1.0.0
	 * @author	Alexandros Raikos <araikos@unipi.gr>
	 */
	public static function asset_creation_shortcode()
	{
		require_once plugin_dir_path(dirname(__FILE__)) . 'public/class-policycloud-marketplace-user.php';

		try {
			// Get specific Description data for authorized users.
			$token = retrieve_token();
			if (empty($token)) $error_message = "You need to be logged in to create an Asset.";
		} catch (Exception $e) {
			$error_message = $e->getMessage();
		}

		// Retrieve description page URL.
		$options = get_option('policycloud_marketplace_plugin_settings');
		if (empty($options['description_page'])) $error_message = "You have not set an asset page in your PolicyCloud Marketplace settings.";

		wp_enqueue_script("policycloud-marketplace-asset-creation");
		wp_localize_script('policycloud-marketplace-asset-creation', 'ajax_properties_asset_creation', array(
			'ajax_url' => admin_url('admin-ajax.php'),
			'nonce' => wp_create_nonce('ajax_policycloud_asset_creation_verification'),
			'description_page' => $options['description_page']
		));

		require_once plugin_dir_path(dirname(__FILE__)) . 'public/partials/policycloud-marketplace-public-display.php';
		asset_creation_html($error_message ?? '');
	}

	/**
	 * Handle description creation AJAX requests.
	 *
	 * @uses	PolicyCloud_Marketplace_Public::description_creation()
	 *
	 * @since	1.0.0
	 * @author	Alexandros Raikos <araikos@unipi.gr>
	 */
	public function asset_creation_handler()
	{
		// Verify WordPress generated nonce.
		if (!wp_verify_nonce($_POST['nonce'], 'ajax_policycloud_asset_creation_verification')) {
			http_response_code(403);
			die("Unverified request to create an asset.");
		}

		try {
			require_once plugin_dir_path(dirname(__FILE__)) . 'public/class-policycloud-marketplace-user.php';
			$token = retrieve_token();
			if (!empty($token)) {
				require_once plugin_dir_path(dirname(__FILE__)) . 'public/partials/policycloud-marketplace-content.php';

				$data = [
					"title" => sanitize_text_field($_POST['title']),
					"type" => sanitize_text_field($_POST['type']),
					"subtype" => sanitize_text_field($_POST['subtype'] ?? ''),
					"owner" => sanitize_text_field($_POST['owner'] ?? ''),
					"description" => sanitize_text_field($_POST['description']),
					"fieldOfUse" => explode(", ", $_POST['fields-of-use'] ?? []),
					"comments" => sanitize_text_field($_POST['comments'] ?? '')
				];

				// Prepare data
				$id = create_asset($data, $token);
				http_response_code(200);
				die(json_encode($id));
			} else {
				http_response_code(404);
				die("User token not found.");
			}
		} catch (RuntimeException $e) {
			http_response_code(400);
			die($e->getMessage());
		} catch (InvalidArgumentException $e) {
			http_response_code(404);
			die($e->getMessage());
		} catch (JsonException $e) {
			http_response_code(440);
			die();
		} catch (ErrorException $e) {
			http_response_code(500);
			die($e->getMessage());
		}
	}
}
