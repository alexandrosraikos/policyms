<?php

/**
 * The public-facing functionality of the plugin.
 *
 * @link       http://example.com
 * @since      1.0.0
 *
 * @package    Plugin_Name
 * @subpackage Plugin_Name/public
 */

/**
 * The public-facing functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the public-facing stylesheet and JavaScript.
 *
 * @package    Plugin_Name
 * @subpackage Plugin_Name/public
 * @author     Your Name <email@example.com>
 */
class Plugin_Name_Public
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
		 * defined in Plugin_Name_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Plugin_Name_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

		wp_enqueue_style($this->plugin_name, plugin_dir_url(__FILE__) . 'css/plugin-name-public.css', array(), $this->version, 'all');
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
		 * defined in Plugin_Name_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Plugin_Name_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

		wp_enqueue_script($this->plugin_name, plugin_dir_url(__FILE__) . 'js/plugin-name-public.js', array('jquery'), $this->version, false);

		wp_register_script("wpbiskoto-registration", plugin_dir_url(__FILE__) . 'js/plugin-name-public-registration.js', array('jquery'), $this->version, false);
	}

	public static function registration_shortcode()
	{
		require_once plugin_dir_path(dirname(__FILE__)) . 'public/partials/plugin-name-public-display.php';

		wp_enqueue_script("wpbiskoto-registration");
		wp_localize_script('wpbiskoto-registration', 'ajax_prop', array(
			'ajax_url' => admin_url('admin-ajax.php'),
			'nonce' => wp_create_nonce('ajax_registration'),
		));

		return registration_form_html();
	}

	private static function user_registration($information)
	{

		/**
		 * TODO @elefkour : Επαλήθευση πληροφοριών.
		 * - Να μην υπάρχουν κενές τιμές.
		 * - Έλεγχος έγκυρης διεύθυνσης email.
		 * ----
		 * Αλλιώς βγάζει σφάλμα η συνάρτηση:
		 * throw new Exception("μήνυμα");
		 */
		//to evala se afth th metavlhth epeidh eixa kanei etsi sthn arxh tous elenxous.
	
		$data = $information;

		if (empty($data['username']) ||
			empty($data['password']) ||
			empty($data['email']) ||
			empty($data['name']) ||
			empty($data['surname']) ||
			empty($data['title']) ||
			empty($data['gender'])||
			empty($data['organization']) ||
			empty($data['phone'])) {
			
			throw new Exception('Please fill all required fields!');
		}
		if(!filter_var($data["email"], FILTER_VALIDATE_EMAIL)){
			throw new Exception("Please enter a valid email");
		}
		//afto to evala mh to xreiastoume kapoia stigmh gia antistoixia kwdikwn
		if ($data['password'] !== $data['password_confirm']) {
		   throw new Exception('Password and Confirm password should match!');   
		   
		}
		$uppercase = preg_match('@[A-Z]@', $data['password']);
$lowercase = preg_match('@[a-z]@', $data['password']);
$number    = preg_match('@[0-9]@', $data['password']);
$specialChars = preg_match('@[^\w]@', $data['password']);
//se periptwsh pou theloume na kanoume to xrhsth na valei isxuro kwdiko
if(!$uppercase || !$lowercase || !$number || !$specialChars || strlen($data['password']) < 8) {
    throw new Exception('Password should be at least 8 characters in length and should include at least one upper case letter, one number, and one special character.');
}
		
		/**
		 * TODO @elefkour : Αποστολή με HTTP POST στο Marketplace API.
		 * Προβολή στοιχείων χρήστη εδώ:
		 * https://documenter.getpostman.com/view/16776360/TzsZs8kn#17a87988-323b-4209-b93c-ea3854616ab3
		 * - Σε περίπτωση API error, τότε:
		 * throw new Exception(<<βάλε το μήνυμα API error εδώ>>);
		 * - Σε περίπτωση επιτυχίας, τότε:
		 * Βάλτο στο $response.
		 */
		$curl = curl_init();
		
		curl_setopt_array($curl, array(
		  CURLOPT_URL => 'https://iristest.ddns.net:4444/registration/users',
		  CURLOPT_RETURNTRANSFER => true,
		  CURLOPT_ENCODING => '',
		  CURLOPT_MAXREDIRS => 10,
		  CURLOPT_TIMEOUT => 0,
		  CURLOPT_FOLLOWLOCATION => true,
		  CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
		  CURLOPT_CUSTOMREQUEST => 'POST',
		  CURLOPT_POSTFIELDS =>'{"password": "'.$data['password'].'", "username": "'.$data['username'].'", "name": "'.$data['name'].'", "surname": "'.$data['surname'].'", "title": "'.$data['title'].'", "gender": "'.$data['gender'].'", "organization": "'.$data['organization'].'", "email": "'.$data['email'].'", "phone": "'.$data['phone'].'"}',
		  CURLOPT_HTTPHEADER => array('Content-Type: application/json')
		));
		
		$response = json_decode(curl_exec($curl),true);
		curl_close($curl);
		if ($response['_status'] == "successful")
			return array($response["token"], 1);
		else return array($response["message"], 0, $response);

		/**
		 * TODO @alexandrosraikos : Αποθήκευση κρυπτογραφημένου JWT.
		 */
	}

	public function user_registration_handler()
	{

		// Verify WordPress generated nonce.
		if (!wp_verify_nonce($_POST['nonce'], 'ajax_registration')) {
			die("Unverified request to register user.");
		}

		// Attempt to send shipment using POST data.
		try {
			Plugin_Name_Public::user_registration($_POST);
		} catch (Exception $e) {
			// Return error.
			echo $e->getMessage();
		}

		// Return success.
		die();
	}
}
