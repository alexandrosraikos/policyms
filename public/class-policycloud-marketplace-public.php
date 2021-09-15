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
	 * @since    1.0.0
	 * @param      string    $plugin_name       The name of the plugin.
	 * @param      string    $version    The version of this plugin.
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

		wp_enqueue_style($this->plugin_name, plugin_dir_url(__FILE__) . 'css/policycloud-marketplace-public.css', array(), $this->version, 'all');
	}

	/**
	 * Register the JavaScript for the public-facing side of the site.
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

		wp_enqueue_script($this->plugin_name, plugin_dir_url(__FILE__) . 'js/policycloud-marketplace-public.js', array('jquery'), $this->version, false);

		wp_register_script("policycloud-marketplace-registration", plugin_dir_url(__FILE__) . 'js/policycloud-marketplace-public-registration.js', array('jquery'), $this->version, false);

		wp_register_script("policycloud-marketplace-login", plugin_dir_url(__FILE__) . 'js/policycloud-marketplace-public-login.js', array('jquery'), $this->version, false);

		wp_enqueue_script("policycloud-marketplace-logout", plugin_dir_url(__FILE__) . 'js/policycloud-marketplace-public-logout.js', array('jquery'), $this->version, false);

		wp_register_script("upload_ste", plugin_dir_url(__FILE__) . 'js/policycloud-marketplace-public-up.js', array('jquery'), $this->version, false);
	}

	/**
	 * 
	 * -----------
	 * 
	 * REGISTRATION
	 * 
	 * -----------
	 */


	/**
	 * Register the shortcode for user registration.
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

		return registration_form_html();
	}


	/**
	 * Enact user registration using the Marketplace API.
	 *
	 * @since    1.0.0
	 */
	private static function user_registration($data)
	{
		function marketplace_username_exists($username)
		{

			$options = get_option('policycloud_marketplace_plugin_settings');
			$curl = curl_init();

			curl_setopt_array($curl, array(
				CURLOPT_URL => 'https://' . $options['marketplace_host'] . '/username/availability',
				CURLOPT_RETURNTRANSFER => true,
				CURLOPT_ENCODING => '',
				CURLOPT_MAXREDIRS => 10,
				CURLOPT_TIMEOUT => 0,
				CURLOPT_FOLLOWLOCATION => true,
				CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
				CURLOPT_CUSTOMREQUEST => 'GET',
				CURLOPT_HTTPHEADER => array(
					'username: "' . $username . '"'
				),
			));
			$isexist = false;
			$response = curl_exec($curl);
			$response = json_decode($response, true);
			if ($response['_status'] == 'successful') {
				$isexist = true;
			}
			curl_close($curl);
			//echo $response;
			return $isexist;
		}

		// Information validation.
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
		) {

			throw new Exception('Please fill all required fields!');
		}

		if (!filter_var($data["email"], FILTER_VALIDATE_EMAIL)) {
			throw new Exception("Please enter a valid email");
		}

		if ($data['password'] !== $data['password_confirm']) {
			throw new Exception('Password and Confirm password should match!');
		}
		$uppercase = preg_match('@[A-Z]@', $data['password']);
		$lowercase = preg_match('@[a-z]@', $data['password']);
		$number    = preg_match('@[0-9]@', $data['password']);
		$specialChars = preg_match('@[^\w]@', $data['password']);

		if (!$uppercase || !$lowercase || !$number || !$specialChars || strlen($data['password']) < 8) {
			throw new Exception('Password should be at least 8 characters in length and should include at least one upper case letter, one number, and one special character.');
		}
		if (!marketplace_username_exists($data['username'])) {
			throw new Exception("Username already exists.");
			// }
		}
		if ($data['username'] <= 2) {
			throw new Exception("Username must be at least 2 chars");
		}
		// Contact Marketplace registration API.
		$curl = curl_init();

		$options = get_option('policycloud_marketplace_plugin_settings');
		error_log(json_encode($data));
		$registration_data = array(
			'username' => $data['username'],
			'account' => array(
				'password' => $data['password']
			),
			'info' => array(
				'name' => $data['name'],
				'surname' => $data['surname'],
				'title' => $data['title'],
				'gender' => $data['gender'],
				'organization' => $data['organization'],
				'phone' => $data['phone'] ?? '',
				'email' => $data['email']
			)
		);
		curl_setopt_array($curl, array(
			CURLOPT_URL => 'https://' . $options['marketplace_host'] . '/registration/users',
			CURLOPT_RETURNTRANSFER => true,
			CURLOPT_ENCODING => '',
			CURLOPT_MAXREDIRS => 10,
			CURLOPT_TIMEOUT => 0,
			CURLOPT_FOLLOWLOCATION => true,
			CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
			CURLOPT_CUSTOMREQUEST => 'POST',
			CURLOPT_POSTFIELDS => json_encode($registration_data),
			CURLOPT_HTTPHEADER => array('Content-Type: application/json')
		));

		$response = json_decode(curl_exec($curl), true);
		curl_close($curl);
		error_log(json_encode($response));
		// Return encypted token.
		if (!isset($response)) {
			throw new Exception("Unable to reach the Marketplace server.");
		} elseif ($response['_status'] == 'successful') {

			try {

				// Αποκωδικοποίηση και επιστροφή κρυπτογραφημένου token.
				$options = get_option('policycloud_marketplace_plugin_settings');

				error_log($options['jwt_key']);
				$data = JWT::decode($response['token'], $options['jwt_key'], array('HS256'));

				return openssl_encrypt(json_encode($data), "AES-128-ECB", $options['jwt_key']);
			} catch (Exception $e) {
				throw new Exception($e->getMessage());
			}
		} elseif ($response['_status'] == 'unsuccessful') {

			// Επιστροφή σφάλματος.
			throw new Exception($response['message']);
		}
	}


	/**
	 * Handle user registration AJAX requests.
	 *
	 * @since    1.0.0
	 */
	public function user_registration_handler()
	{

		// Verify WordPress generated nonce.
		if (!wp_verify_nonce($_POST['nonce'], 'ajax_registration')) {
			die("Unverified request to register user.");
		}

		$response = array();

		// Attempt to send shipment using POST data.
		try {
			$response = array(
				'status' => 'success',
				'data' => PolicyCloud_Marketplace_Public::user_registration($_POST)
			);
		} catch (Exception $e) {
			$response = array(
				'status' => 'failure',
				'data' => $e->getMessage()
			);
		}

		echo json_encode($response);

		// Return success.
		die();
	}


	/**
	 * 
	 * -----------
	 * 
	 * LOGIN
	 * 
	 * -----------
	 */

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

		return login_form_html();
	}

	/**
	 * Enact user registration using the Marketplace API.
	 *
	 * @since    1.0.0
	 */
	private static function user_login($data)
	{
		// Information validation.
		if (
			empty($data['username']) ||
			empty($data['password'])
		) {
			throw new Exception('Please fill all required fields!');
		}

		// Contact Marketplace login API endpoint.
		$curl = curl_init();

		$options = get_option('policycloud_marketplace_plugin_settings');

		curl_setopt_array($curl, array(
			CURLOPT_URL => 'https://' . $options['marketplace_host'] . '/authorization/users',
			CURLOPT_RETURNTRANSFER => true,
			CURLOPT_ENCODING => '',
			CURLOPT_MAXREDIRS => 10,
			CURLOPT_TIMEOUT => 0,
			CURLOPT_FOLLOWLOCATION => true,
			CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
			CURLOPT_CUSTOMREQUEST => 'POST',
			CURLOPT_POSTFIELDS => '{"password": "' . $data['password'] . '", "username": "' . $data['username'] . '"}',
			CURLOPT_HTTPHEADER => array('Content-Type: application/json')
		));

		$response = json_decode(curl_exec($curl), true);
		curl_close($curl);
		// Return encypted token.
		if (!isset($response)) {
			throw new Exception("Unable to reach the Marketplace server.");
		} elseif ($response['_status'] == 'successful') {
			try {

				// Αποκωδικοποίηση και επιστροφή κρυπτογραφημένου token.
				$options = get_option('policycloud_marketplace_plugin_settings');
				error_log($options['jwt_key']);

				return openssl_encrypt($response['token'], "AES-128-ECB", $options['jwt_key']);
			} catch (Exception $e) {
				throw new Exception($e->getMessage());
			}
		} elseif ($response['_status'] == 'unsuccessful') {

			// Επιστροφή σφάλματος.
			throw new Exception($response['message']);
		}
	}

	/**
	 * Handle user login AJAX requests.
	 *
	 * @since    1.0.0
	 */
	public function user_login_handler()
	{

		// Verify WordPress generated nonce.
		if (!wp_verify_nonce($_POST['nonce'], 'ajax_login')) {
			die("Unverified request to register user.");
		}

		$response = array();

		// Attempt to send shipment using POST data.
		try {
			$response = array(
				'status' => 'success',
				'data' => PolicyCloud_Marketplace_Public::user_login($_POST)
			);
		} catch (Exception $e) {
			$response = array(
				'status' => 'failure',
				'data' => $e->getMessage()
			);
		}

		echo json_encode($response);

		// Return success.
		die();
	}

	public static function prepare_token($token, $encrypted = true)
	{
		// Retrieve credentials.
		$options = get_option('policycloud_marketplace_plugin_settings');
		if (!$options) {
			throw new Exception("No PolicyCloud Marketplace credentials defined in WordPress settings.");
		}
		if (!isset($options['jwt_key'])) throw new Exception("No Marketplace Key defined in WordPress settings.");


		if ($encrypted) {
			// Decrypt token.
			$token = openssl_decrypt($token, "AES-128-ECB", $options['jwt_key']);
			if (!$token) throw new Exception("Decryption was unsuccessful.");
		}

		// Validate using JWT.
		try {
			JWT::decode($token, $options['jwt_key'], array('HS256'));
		} catch (InvalidArgumentException $e) {
			error_log($e->getMessage());
			return false;
		} catch (UnexpectedValueException $e) {
			error_log($e->getMessage());
			return false;
		} catch (SignatureInvalidException $e) {
			error_log($e->getMessage());
			return false;
		} catch (BeforeValidException $e) {
			error_log($e->getMessage());
			return false;
		} catch (ExpiredException $e) {
			error_log($e->getMessage());
			return false;
		}

		if ($encrypted) {
			return $token;
		} else {
			return true;
		}
	}

	/**
	 * 
	 * ----------
	 * 
	 * MENU ITEM
	 * 
	 * ----------
	 * 
	 */

	public static function add_conditional_access_menu_item($items, $args)
	{
		// if ($args->theme_location == 'primary') {
		if (isset($_COOKIE["ppmapi-token"])) {
			$link = '<a class="menu-link elementor-item policycloud-logout">Log out</a>';
		} else {
			$link = '<a class="menu-link elementor-item" href="/login">Log In</a>';
		}
		return $items . '<li class="menu-item menu-item-type-post_type menu-item-object-page policycloud-access-button">' . $link . '</li>';
		// }
		// else {
		// 	return $items;
		// }
	}


	/**
	 * 
	 * -----------
	 * 
	 * READ DATA
	 * 
	 * -----------
	 */

	public static function get_specific_description(string $api_host, string $token, $id)
	{
		// Contact Marketplace login API endpoint.
		$curl = curl_init();

		curl_setopt_array($curl, array(
			CURLOPT_URL => 'https://' . $api_host . '/descriptions/all/' . $id,
			CURLOPT_RETURNTRANSFER => true,
			CURLOPT_ENCODING => '',
			CURLOPT_MAXREDIRS => 10,
			CURLOPT_TIMEOUT => 0,
			CURLOPT_FOLLOWLOCATION => true,
			CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
			CURLOPT_CUSTOMREQUEST => 'POST',
			CURLOPT_HTTPHEADER => array('Content-Type: application/json', 'x-access-token: ' . $token)
		));

		// Get data,
		$description = json_decode(curl_exec($curl), true);

		// Handle errors.
		if (!empty(curl_error($curl))) {
			throw new Exception("There was a connection error while attempting to retrieve all descriptions.");
		}

		// Close session.
		curl_close($curl);

		return $description;
	}


	/**
	 * Display multiple objects for visitors and privileged users.
	 *
	 * @since    1.0.0
	 */
	public static function read_multiple_objects()
	{
		function get_public_descriptions(string $api_host, array $collections = null)
		{
			// Get all descriptions.
			if (!isset($collections)) {

				// Contact PolicyCloud Marketplace API.
				$curl = curl_init();

				curl_setopt_array($curl, array(
					CURLOPT_URL => 'https://' . $api_host . '/descriptions/all',
					CURLOPT_RETURNTRANSFER => true,
					CURLOPT_ENCODING => "",
					CURLOPT_MAXREDIRS => 10,
					CURLOPT_TIMEOUT => 30,
					CURLOPT_FOLLOWLOCATION => true,
					CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
					CURLOPT_CUSTOMREQUEST => "GET",
				));

				// Get data.
				$descriptions = json_decode(curl_exec($curl), true);

				// Handle errors.
				if (!empty(curl_error($curl))) {
					throw new Exception("There was a connection error while attempting to retrieve all descriptions.");
				}

				// Close session.
				curl_close($curl);
			}

			// Get collection-filtered descriptions.
			else {
				$descriptions = array();
				foreach ($collections as $collection) {

					// Contact PolicyCloud Marketplace API.
					$curl = curl_init();

					curl_setopt_array($curl, array(
						CURLOPT_URL => 'https://' . $api_host . '/descriptions/' . $collection,
						CURLOPT_RETURNTRANSFER => true,
						CURLOPT_ENCODING => "",
						CURLOPT_MAXREDIRS => 10,
						CURLOPT_TIMEOUT => 30,
						CURLOPT_FOLLOWLOCATION => true,
						CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
						CURLOPT_CUSTOMREQUEST => "GET",
					));

					// Append descriptions
					$descriptions += (array) json_decode(curl_exec($curl), true);

					// Handle errors.
					if (!empty(curl_error($curl))) throw new Exception("There was a connection error while attempting to retrieve all descriptions.");

					// Close session.
					curl_close($curl);
				}
			}

			return $descriptions;
		}


		require_once plugin_dir_path(dirname(__FILE__)) . 'public/partials/policycloud-marketplace-public-display.php';

		// Retrieve credentials.
		$options = get_option('policycloud_marketplace_plugin_settings');
		if (!$options) error_log("No PolicyCloud Marketplace credentials defined in WordPress settings.");
		if (empty($options['marketplace_host'])) error_log("No Marketplace Host was defined in WordPress settings.");

		try {
			
			// Get all publicly available descriptions.
			$descriptions = get_public_descriptions($options['marketplace_host']);

			/**
			 *	TODO @alexandrosraikos: Ανάγνωση $_GET για την κατασκευή της κατάλληλης φιλτραρισμένης κλήσης API.
			 *	Σημείωση: Δημιουργία μεταβλητής $request = '/φτιάξε/το/αντίστοιχο/endpoint' και χρήση της στο curl μετά.
			 *	Σχήμα δεδομένων φίλτρων:
			 */

			if (isset($_GET['search'])) {
			}
			if (isset($_GET['collections'])) {
				$descriptions = get_public_descriptions($options['marketplace_host'], $_GET['collections']);
			}
		} catch (Exception $e) {
			error_log($e->getMessage());
			$descriptions = array();
		}

		// Access control checking.
		if (isset($_COOKIE['ppmpapi-token'])) {
			try {
				// Retrieve token.
				$token = PolicyCloud_Marketplace_Public::prepare_token($_COOKIE['ppmpapi-token']);

				// Get specific description data from the list for authorized users.
				$descriptions = array_map(function ($guest_description) use ($options, $token) {
					return PolicyCloud_Marketplace_Public::get_specific_description($options['marketplace_host'], $token, $guest_description['id']);
				}, $descriptions);
			} catch (Exception $e) {
				error_log($e->getMessage());
			}
		}

		// Print response data to front end.
		wp_enqueue_script("policycloud-marketplace-read-multiple", plugin_dir_url(__FILE__) . 'js/policycloud-marketplace-public-read-multiple.js', array('jquery'));
		read_multiple_html($descriptions);
	}

	public static function read_single_object()
	{
		// TODO @alexandrosraikos: Fetch Description data from the API.
		
		// Print response data to front end.
		wp_enqueue_script("policycloud-marketplace-read-single", plugin_dir_url(__FILE__) . 'js/policycloud-marketplace-public-read-single.js', array('jquery'));
		
		$description = "Hello";
		read_multiple_html($description);
	}


	/**
	 * 
	 * -----------
	 * 
	 * CREATE
	 * 
	 * -----------
	 */


	public static function create_object()
	{
		require_once plugin_dir_path(dirname(__FILE__)) . 'public/partials/policycloud-marketplace-public-display.php';

		wp_enqueue_script("upload_ste");
		wp_localize_script('upload_ste', 'ajax_prop', array(
			'ajax_url' => admin_url('admin-ajax.php'),
			'nonce' => wp_create_nonce('ajax_upload'),
		));

		return upload_step();
	}




	/**
	 * 
	 * -----------
	 * 
	 * UPDATE
	 * 
	 * -----------
	 */


	/**
	 * 
	 * -----------
	 * 
	 * DELETE
	 * 
	 * -----------
	 */
}
