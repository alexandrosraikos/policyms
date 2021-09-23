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

require_once plugin_dir_path(dirname(__FILE__)) . 'public/partials/vendor/autoload.php';

use Firebase\JWT\BeforeValidException;
use Firebase\JWT\ExpiredException;
use Firebase\JWT\JWT;
use Firebase\JWT\SignatureInvalidException;

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
		add_shortcode('policycloud-marketplace-create', 'PolicyCloud_Marketplace_Public::create_object');
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
		wp_enqueue_script($this->plugin_name, plugin_dir_url(__FILE__) . 'js/policycloud-marketplace-public.js', array('jquery'), $this->version, false);

		wp_register_script("policycloud-marketplace-registration", plugin_dir_url(__FILE__) . 'js/policycloud-marketplace-public-registration.js', array('jquery'), $this->version, false);
		wp_register_script("policycloud-marketplace-login", plugin_dir_url(__FILE__) . 'js/policycloud-marketplace-public-login.js', array('jquery'), $this->version, false);
		wp_enqueue_script("policycloud-marketplace-logout", plugin_dir_url(__FILE__) . 'js/policycloud-marketplace-public-logout.js', array('jquery'), $this->version, false);

		wp_register_script("upload_ste", plugin_dir_url(__FILE__) . 'js/policycloud-marketplace-public-up.js', array('jquery'), $this->version, false);
		wp_register_script("policycloud-marketplace-read-single", plugin_dir_url(__FILE__) . 'js/policycloud-marketplace-public-read-single.js', array('jquery'), $this->version, false);
	}

	/**
	 * Register the shortcodes for user registration.
	 *
	 * @since    1.0.0
	 */
	public static function registration_shortcode()
	{
		require_once plugin_dir_path(dirname(__FILE__)) . 'public/partials/policycloud-marketplace-public-display.php';

		wp_enqueue_script("policycloud-marketplace-registration");
		wp_localize_script('policycloud-marketplace-registration', 'ajax_prop', array(
			'ajax_url' => admin_url('admin-ajax.php'),
			'nonce' => wp_create_nonce('ajax_registration'),
		));

		registration_form_html();
	}


	/**
	 * Enact user registration using the Marketplace API.
	 * For more information concerning the schema of the registration data, please visit:
	 * https://documenter.getpostman.com/view/16776360/TzsZs8kn#17a87988-323b-4209-b93c-ea3854616ab3 
	 * 
	 * @param	array $data The user information to be registered with the Marketplace API.
	 * @usedby PolicyCloud_Marketplace_Public::user_registration_handler()
	 * @since	1.0.0
	 */
	private static function user_registration($data)
	{
		/**
		 * Check username availability using the Marketplace API.
		 * For more information concerning the schema of the registration data, please visit:
		 * https://documenter.getpostman.com/view/16776360/TzsZs8kn#135d37d6-0eef-47e5-a31f-df4153962503
		 * 
		 * @param	string $username The username to be registered.
		 * @return	bool The availability of the requested username.
		 * @since	1.0.0
		 */
		function marketplace_username_exists($hostname, $username)
		{
			// Request username availability status.
			$curl = curl_init();
			curl_setopt_array($curl, [
				CURLOPT_URL => 'https://' . $hostname . '/username/availability',
				CURLOPT_RETURNTRANSFER => true,
				CURLOPT_ENCODING => '',
				CURLOPT_MAXREDIRS => 10,
				CURLOPT_TIMEOUT => 0,
				CURLOPT_FOLLOWLOCATION => true,
				CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
				CURLOPT_CUSTOMREQUEST => 'GET',
				CURLOPT_HTTPHEADER => [
					'username: "' . $username . '"'
				],
			]);
			$response = json_decode(curl_exec($curl), true);
			curl_close($curl);

			// Return status.
			return ($response['_status'] ?? '') == 'successful';
		}

		// Information validation checks and errors.
		if (
			empty($data['username']) ||
			empty($data['password']) ||
			empty($data['email']) ||
			empty($data['name']) ||
			empty($data['surname']) ||
			empty($data['title']) ||
			empty($data['gender']) ||
			empty($data['organization']) ||
			empty($data['phone'])
		) throw new Exception('Please fill all required fields!');
		if (!filter_var($data["email"], FILTER_VALIDATE_EMAIL)) throw new Exception("Please enter a valid email");
		if ($data['password'] !== $data['password_confirm']) throw new Exception('Password and password confirmation should match!');
		if (
			!empty(preg_match('@[A-Z]@', $data['password'])) ||
			!empty(preg_match('@[a-z]@', $data['password'])) ||
			!empty(preg_match('@[0-9]@', $data['password'])) ||
			!empty(preg_match('@[^\w]@', $data['password'])) ||
			strlen($data['password']) < 8
		) throw new Exception('Password should be at least 8 characters in length and should include at least one upper case letter, one number, and one special character.');
		if ($data['username'] <= 2) throw new Exception("Username must be at least 2 chars");

		// Retrieve API credentials.
		$options = get_option('policycloud_marketplace_plugin_settings');
		if (empty($options['marketplace_host'])) throw new Exception("No PolicyCloud Marketplace API hostname was defined in WordPress settings.");

		// Username availability check.
		if (!marketplace_username_exists($options['marketplace_host'], $data['username'])) throw new Exception("Username already exists.");

		// Contact Marketplace registration API.
		$curl = curl_init();
		curl_setopt_array($curl, array(
			CURLOPT_URL => 'https://' . $options['marketplace_host'] . '/registration/users',
			CURLOPT_RETURNTRANSFER => true,
			CURLOPT_ENCODING => '',
			CURLOPT_MAXREDIRS => 10,
			CURLOPT_TIMEOUT => 0,
			CURLOPT_FOLLOWLOCATION => true,
			CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
			CURLOPT_CUSTOMREQUEST => 'POST',
			CURLOPT_POSTFIELDS => json_encode([
				'username' => $data['username'],
				'account' => [
					'password' => $data['password']
				],
				'info' => [
					'name' => $data['name'],
					'surname' => $data['surname'],
					'title' => $data['title'],
					'gender' => $data['gender'],
					'organization' => $data['organization'],
					'phone' => $data['phone'] ?? '',
					'email' => $data['email']
				]
			]),
			CURLOPT_HTTPHEADER => array('Content-Type: application/json')
		));
		$response = json_decode(curl_exec($curl), true);
		curl_close($curl);

		// Check response and return encypted token.
		if (!isset($response)) throw new Exception("Unable to reach the Marketplace server.");
		elseif ($response['_status'] == 'successful') {
			try {
				// Encrypt token using the same key and return.
				return openssl_encrypt(json_encode($data), "AES-128-ECB", $options['jwt_key']);
			} catch (Exception $e) {
				throw new Exception($e->getMessage());
			}
		} elseif ($response['_status'] == 'unsuccessful') throw new Exception($response['message']);
	}


	/**
	 * Handle user registration AJAX requests.
	 *
	 * @uses 	PolicyCloud_Marketplace_Public::user_registration()
	 * @since	1.0.0
	 */
	public function user_registration_handler()
	{
		// Verify WordPress generated nonce.
		if (!wp_verify_nonce($_POST['nonce'], 'ajax_registration')) {
			die("Unverified request to register user.");
		}

		// Attempt to send shipment using POST data.
		try {
			die(json_encode([
				'status' => 'success',
				'data' => PolicyCloud_Marketplace_Public::user_registration($_POST)
			]));
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
		require_once plugin_dir_path(dirname(__FILE__)) . 'public/partials/policycloud-marketplace-public-display.php';

		wp_enqueue_script('policycloud-marketplace-login');
		wp_localize_script('policycloud-marketplace-login', 'ajax_prop', array(
			'ajax_url' => admin_url('admin-ajax.php'),
			'nonce' => wp_create_nonce('ajax_login'),
		));

		login_form_html();
	}

	/**
	 * Enact user registration using the Marketplace API.
	 *
	 * @param array $data The data user for existing user authentication (username, password).
	 * @return string The encoded Marketplace API token for the successfully authenticated user.
	 * @usedby PolicyCloud_Marketplace_Public::user_login_handler()
	 * @since    1.0.0
	 */
	private static function user_login($data)
	{
		// Check submitted log in information.
		if (
			empty($data['username']) ||
			empty($data['password'])
		) throw new Exception('Please fill in all required fields.');

		$options = get_option('policycloud_marketplace_plugin_settings');
		if (empty($options['marketplace_host'])) throw new Exception("No PolicyCloud Marketplace API hostname was defined in WordPress settings.");

		// Contact Marketplace login API endpoint.
		$curl = curl_init();
		curl_setopt_array($curl, [
			CURLOPT_URL => 'https://' . $options['marketplace_host'] . '/authorization/users',
			CURLOPT_RETURNTRANSFER => true,
			CURLOPT_ENCODING => '',
			CURLOPT_MAXREDIRS => 10,
			CURLOPT_TIMEOUT => 0,
			CURLOPT_FOLLOWLOCATION => true,
			CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
			CURLOPT_CUSTOMREQUEST => 'POST',
			CURLOPT_POSTFIELDS => json_encode($data),
			CURLOPT_HTTPHEADER => ['Content-Type: application/json']
		]);
		$response = json_decode(curl_exec($curl), true);
		curl_close($curl);

		// Return encypted token.
		if (!isset($response)) {
			throw new Exception("Unable to reach the Marketplace server.");
		} elseif ($response['_status'] == 'successful') {
			try {
				// Encrypt token using the same key and return.
				return openssl_encrypt($response['token'], "AES-128-ECB", $options['jwt_key']);
			} catch (Exception $e) {
				throw new Exception($e->getMessage());
			}
		} elseif ($response['_status'] == 'unsuccessful') throw new Exception($response['message']);
	}

	/**
	 * Handle user login AJAX requests.
	 *
	 * @uses	PolicyCloud_Marketplace_Public::user_login()
	 * @since	1.0.0
	 */
	public function user_login_handler()
	{
		// Verify WordPress generated nonce.
		if (!wp_verify_nonce($_POST['nonce'], 'ajax_login')) {
			die("Unverified request to register user.");
		}

		// Attempt to send shipment using POST data.
		try {
			die(json_encode([
				'status' => 'success',
				'data' => PolicyCloud_Marketplace_Public::user_login($_POST)
			]));
		} catch (Exception $e) {
			die(json_encode([
				'status' => 'failure',
				'data' => $e->getMessage()
			]));
		}
	}


	/**
	 * Retrieve and decrypt the token from the user.
	 * @param	bool $decode Pass *true* if the token must be returned decoded as well.
	 * @return	string|bool Returns the token, decrypted and/or decoded. Returns *false* if there is no token.
	 * @since	1.0.0
	 */
	private static function retrieve_token(bool $decode = false)
	{
		// Retrieve credentials.
		$options = get_option('policycloud_marketplace_plugin_settings');
		if (empty($options['jwt_key'])) throw new Exception("No PolicyCloud Marketplace API Key defined in WordPress settings.");

		// Retrieve saved token.
		if (!empty($_COOKIE['ppmpapi-token'])) {

			// Decrypt token.
			$token = openssl_decrypt($_COOKIE['ppmpapi-token'], "AES-128-ECB", $options['jwt_key']);
			if (empty($token)) throw new Exception("Decryption was unsuccessful.");

			// Validate token age, signature and content.
			try {
				$decoded_token = JWT::decode($token, $options['jwt_key'], array('HS256'));
			} catch (InvalidArgumentException $e) {
				throw new Exception($e->getMessage());
			} catch (UnexpectedValueException $e) {
				throw new Exception($e->getMessage());
			} catch (SignatureInvalidException $e) {
				throw new Exception($e->getMessage());
			} catch (BeforeValidException $e) {
				throw new Exception($e->getMessage());
			} catch (ExpiredException $e) {
				throw new Exception($e->getMessage());
			}

			return ($decode) ? $decoded_token : $token;
		} else return false;
	}


	/**
	 * Add a menu item to a selected menu, which conditionally switches
	 * from log in to log out actions.
	 * 
	 * @since    1.0.0
	 */
	public static function add_conditional_access_menu_item($items, $args)
	{
		// Retrieve credentials.
		$options = get_option('policycloud_marketplace_plugin_settings');
		if (empty($options['selected_menu']) || empty($options['login_page'])) return $items;

		// Add conditional menu item.
		if ($args->theme_location == $options['selected_menu']) {
			try {
				if (!empty(PolicyCloud_Marketplace_Public::retrieve_token())) {
					$link = '<a class="menu-link elementor-item policycloud-logout">Log out</a>';
				} else {
					$link = '<a class="menu-link elementor-item" href="' . $options['login_page'] . '">Log In</a>';
				}
			} catch (\Exception $e) {
				$link = '<a class="menu-link elementor-item" href="' . $options['login_page'] . '">Log In</a>';
			}
			return $items . '<li class="menu-item menu-item-type-post_type menu-item-object-page policycloud-access-button">' . $link . '</li>';
		} else return $items;
	}

	/**
	 * Retrieve a specific Description Object from the Marketplace API.
	 * For more information on the retrieved schema, visit: 
	 * https://documenter.getpostman.com/view/16776360/TzsZs8kn#8191ba2b-f1aa-4b05-8e66-12025c85d999
	 * 
	 * @param	string $api_host The hostname of the Marketplace API server.
	 * @param	string $token The user's access token used for authorization (encoded).
	 * @return  array An array containing the description information.
	 * @since    1.0.0
	 */
	private static function get_specific_description(string $token, $id)
	{
		// Retrieve credentials.
		$options = get_option('policycloud_marketplace_plugin_settings');
		if (empty($options['marketplace_host'])) throw new Exception("No PolicyCloud Marketplace API hostname was defined in WordPress settings.");

		// Contact Marketplace login API endpoint.
		$curl = curl_init();

		curl_setopt_array($curl, array(
			CURLOPT_URL => 'https://' . $options['marketplace_host'] . '/descriptions/all/' . $id,
			CURLOPT_RETURNTRANSFER => true,
			CURLOPT_ENCODING => '',
			CURLOPT_MAXREDIRS => 10,
			CURLOPT_TIMEOUT => 0,
			CURLOPT_FOLLOWLOCATION => true,
			CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
			CURLOPT_CUSTOMREQUEST => 'POST',
			CURLOPT_HTTPHEADER => ['Content-Type: application/json', 'x-access-token: ' . $token]
		));

		// Get data,
		$description = json_decode(curl_exec($curl), true);

		// Handle errors.
		if (!empty(curl_error($curl))) throw new Exception("There was a connection error while attempting to retrieve all descriptions.");

		// Close session.
		curl_close($curl);
		return $description;
	}

	/**
	 * Retrieve publicly available Description Objects from the Marketplace API, also filtered by collection.
	 *
	 * To learn more about the Marketplace API data schema for retrieving objects and filtering them, visit:
	 * https://documenter.getpostman.com/view/16776360/TzsZs8kn#6c8e45e3-5be6-4c10-82a6-7d698b092e9e
	 *
	 * @param	array $args An array of arguments to filter the search.
	 * @since    1.0.0
	 */
	private static function get_descriptions(array $args) {

			// Retrieve credentials.
			$options = get_option('policycloud_marketplace_plugin_settings');
			if (empty($options['marketplace_host'])) throw new Exception("No PolicyCloud Marketplace API hostname was defined in WordPress settings.");

			/** 
			 * 
			 * 	TODO @alexandrosraikos: Coordinate & create search filtering criteria. More information here:
			 *  https://documenter.getpostman.com/view/16776360/TzsZs8kn#595b5504-1d07-49f8-8166-8efdb400c5f4
			 * 
			 * */

			// Filtering by information.
			if (!empty($args['owner'])) {
			}
			if (!empty($args['title'])) {
			}
			if (!empty($args['type'])) {
			}
			if (!empty($args['subtype'])) {
			}
			if (!empty($args['comments'])) {
			}
			if (!empty($args['contact'])) {
			}
			if (!empty($args['description'])) {
			}
			if (!empty($args['field_of_use'])) {
			}

			// Filtering by metadata.
			if (!empty($args['provider'])) {
			}
			if (!empty($args['upload_date'])) {
			}
			if (!empty($args['update_date'])) {
			}
			if (!empty($args['last_updated_by'])) {
			}
			if (!empty($args['version'])) {
			}
			if (!empty($args['views'])) {
			}

			$curl = curl_init();

			// Get all descriptions.
			if (!isset($args['collections'])) {

				// Contact PolicyCloud Marketplace API.
				curl_setopt_array($curl, [
					CURLOPT_URL => 'https://' . $options['marketplace_host'] . '/descriptions/all',
					CURLOPT_RETURNTRANSFER => true,
					CURLOPT_ENCODING => "",
					CURLOPT_MAXREDIRS => 10,
					CURLOPT_TIMEOUT => 30,
					CURLOPT_FOLLOWLOCATION => true,
					CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
					CURLOPT_CUSTOMREQUEST => "GET",
				]);

				// Handle errors.
				if (!empty(curl_error($curl))) throw new Exception("There was a connection error while attempting to retrieve all descriptions.");

				// Get data.
				$descriptions = json_decode(curl_exec($curl), true);
			}

			// Filtering by collection.
			else {
				$descriptions = array();
				foreach ($args['collection'] as $collection) {

					// Contact PolicyCloud Marketplace API.
					$curl = curl_init();
					curl_setopt_array($curl, [
						CURLOPT_URL => 'https://' . $options['marketplace_host'] . '/descriptions/' . $collection,
						CURLOPT_RETURNTRANSFER => true,
						CURLOPT_ENCODING => "",
						CURLOPT_MAXREDIRS => 10,
						CURLOPT_TIMEOUT => 30,
						CURLOPT_FOLLOWLOCATION => true,
						CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
						CURLOPT_CUSTOMREQUEST => "GET",
					]);

					// Handle errors.
					if (!empty(curl_error($curl))) throw new Exception("There was a connection error while attempting to retrieve all descriptions.");

					// Append descriptions
					$descriptions += (array) json_decode(curl_exec($curl), true);
				}
			}

			// Close session.
			curl_close($curl);
			return $descriptions;
	}


	/**
	 * Display multiple Description Objects for visitors and authenticated users.
	 *
	 * @since    1.0.0
	 */
	public static function read_multiple_objects()
	{
		try {
			// Retrieve all public descriptions based on GET parameter filtering.
			$descriptions = PolicyCloud_Marketplace_Public::get_descriptions($_GET);

			// Get specific description data from the list for authorized users.
			$token = PolicyCloud_Marketplace_Public::retrieve_token();
			if (!empty($token)) {
				$descriptions = array_map(function ($public_description) use ($token) {
					return PolicyCloud_Marketplace_Public::get_specific_description($token, $public_description['id']);
				}, $descriptions);
				$authenticated = true;
			}
			else $authenticated = false;
		} catch (Exception $e) {
			$error = $e->getMessage();
		}

		// Retrieve description page URL.
		$options = get_option('policycloud_marketplace_plugin_settings');
		if (empty($options['description_page'])) $error = "You have not set a Description page in your PolicyCloud Marketplace settings.";

		// Print response data to front end.
		wp_enqueue_script("policycloud-marketplace-read-multiple", plugin_dir_url(__FILE__) . 'js/policycloud-marketplace-public-read-multiple.js', array('jquery'));
		require_once plugin_dir_path(dirname(__FILE__)) . 'public/partials/policycloud-marketplace-public-display.php';
		read_multiple_html($descriptions, [
			"authenticated" => $authenticated ?? false,
			"description_url" => $options['description_page'],
			"error" => $error ?? null
		]);
	}

	public static function read_single_object()
	{
		// TODO @alexandrosraikos: Conditionally fetch Description Object data (public / authenticated) for display. (?)
		// TODO @alexandrosraikos: Coordinate the addition of the editing form in the generated HTML.
		// TODO @alexandrosraikos: Create AJAX handler and script for description object editing.

		try {
			// Get specific description data for authorized users.
			$token = PolicyCloud_Marketplace_Public::retrieve_token();
			if (!empty($token)) {
				$description = PolicyCloud_Marketplace_Public::get_specific_description($token, $_GET['did']);
			}
			else $error = "Please log in to view this content.";
		} catch (Exception $e) {
			$error = $e->getMessage();
		}

		require_once plugin_dir_path(dirname(__FILE__)) . 'public/partials/policycloud-marketplace-public-display.php';
		wp_enqueue_script("policycloud-marketplace-read-single");

		read_single_html($description, $error ?? null);
	}


	public static function create_object()
	{
		require_once plugin_dir_path(dirname(__FILE__)) . 'public/partials/policycloud-marketplace-public-display.php';

		// TODO @alexandrosraikos: Coordinate the addition of the editing form in the generated HTML.
		// TODO @alexandrosraikos: Create AJAX handler and script for description object creation.

		wp_enqueue_script("upload_ste");
		wp_localize_script('upload_ste', 'ajax_prop', array(
			'ajax_url' => admin_url('admin-ajax.php'),
			'nonce' => wp_create_nonce('ajax_upload'),
		));

		upload_step();
	}
}
