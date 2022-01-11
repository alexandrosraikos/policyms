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
 * @author     Alexandros Raikos <araikos@unipi.gr>
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

        require_once plugin_dir_path(dirname(__FILE__)) . 'includes/class-policycloud-marketplace-account.php';

        require_once plugin_dir_path(dirname(__FILE__)) . 'includes/class-policycloud-marketplace-exceptions.php';

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

        // Support for user accounts.
        $this->loader->add_action('init', $plugin_public, 'add_accounts_shortcodes');
        $this->loader->add_filter('wp_nav_menu_items', $plugin_public, 'add_menu_items', 10, 2);
        $this->loader->add_action('wp_ajax_policycloud_marketplace_account_user_registration', $plugin_public, 'account_user_registration_handler');
        $this->loader->add_action('wp_ajax_nopriv_policycloud_marketplace_account_user_registration', $plugin_public, 'account_user_registration_handler');
        $this->loader->add_action('wp_ajax_policycloud_marketplace_account_user_authentication', $plugin_public, 'account_user_authentication_handler');
        $this->loader->add_action('wp_ajax_nopriv_policycloud_marketplace_account_user_authentication', $plugin_public, 'account_user_authentication_handler');
        $this->loader->add_action('wp_ajax_policycloud_marketplace_account_user_password_reset', $plugin_public, 'account_user_password_reset_handler');
        $this->loader->add_action('wp_ajax_nopriv_policycloud_marketplace_account_user_password_reset', $plugin_public, 'account_user_password_reset_handler');
        $this->loader->add_action('wp_ajax_policycloud_marketplace_account_user_retry_verification', $plugin_public, 'account_user_verification_retry_handler');
        $this->loader->add_action('wp_ajax_nopriv_policycloud_marketplace_account_user_retry_verification', $plugin_public, 'account_user_verification_retry_handler');
        $this->loader->add_action('wp_ajax_policycloud_marketplace_account_user_edit', $plugin_public, 'account_user_editing_handler');
        $this->loader->add_action('wp_ajax_nopriv_policycloud_marketplace_account_user_edit', $plugin_public, 'account_user_editing_handler');
        $this->loader->add_action('wp_ajax_policycloud_marketplace_account_user_data_request', $plugin_public, 'account_user_data_request_handler');
        $this->loader->add_action('wp_ajax_nopriv_policycloud_marketplace_account_user_data_request', $plugin_public, 'account_user_data_request_handler');
        $this->loader->add_action('wp_ajax_policycloud_marketplace_account_user_deletion', $plugin_public, 'account_user_deletion_handler');
        $this->loader->add_action('wp_ajax_nopriv_policycloud_marketplace_account_user_deletion', $plugin_public, 'account_user_deletion_handler');

        // Support for descriptions.
        $this->loader->add_action('init', $plugin_public, 'add_description_shortcodes');
        $this->loader->add_action('wp_ajax_policycloud_marketplace_description_creation', $plugin_public, 'description_creation_handler');
        $this->loader->add_action('wp_ajax_nopriv_policycloud_marketplace_description_creation', $plugin_public, 'description_creation_handler');
        $this->loader->add_action('wp_ajax_policycloud_marketplace_description_editing', $plugin_public, 'description_editing_handler');
        $this->loader->add_action('wp_ajax_nopriv_policycloud_marketplace_description_editing', $plugin_public, 'description_editing_handler');
        $this->loader->add_action('wp_ajax_policycloud_marketplace_description_approval', $plugin_public, 'description_approval_handler');
        $this->loader->add_action('wp_ajax_nopriv_policycloud_marketplace_description_approval', $plugin_public, 'description_approval_handler');
        $this->loader->add_action('wp_ajax_policycloud_marketplace_description_deletion', $plugin_public, 'description_deletion_handler');
        $this->loader->add_action('wp_ajax_nopriv_policycloud_marketplace_description_deletion', $plugin_public, 'description_deletion_handler');

        // Support for descriptions' assets.
        $this->loader->add_action('wp_ajax_policycloud_marketplace_asset_download', $plugin_public, 'asset_download_handler');
        $this->loader->add_action('wp_ajax_nopriv_policycloud_marketplace_asset_download', $plugin_public, 'asset_download_handler');
        $this->loader->add_action('wp_ajax_policycloud_marketplace_asset_delete', $plugin_public, 'asset_deletion_handler');
        $this->loader->add_action('wp_ajax_nopriv_policycloud_marketplace_asset_delete', $plugin_public, 'asset_deletion_handler');
        $this->loader->add_action('wp_ajax_policycloud_marketplace_set_description_image', $plugin_public, 'set_description_image_handler');
        $this->loader->add_action('wp_ajax_nopriv_policycloud_marketplace_set_description_image', $plugin_public, 'set_description_image_handler');
        $this->loader->add_action('wp_ajax_policycloud_marketplace_remove_description_image', $plugin_public, 'remove_description_image_handler');
        $this->loader->add_action('wp_ajax_nopriv_policycloud_marketplace_remove_description_image', $plugin_public, 'remove_description_image_handler');

        // Support for descriptions' reviews.
        $this->loader->add_action('wp_ajax_policycloud_marketplace_get_description_reviews', $plugin_public, 'get_description_reviews_handler');
        $this->loader->add_action('wp_ajax_nopriv_policycloud_marketplace_get_description_reviews', $plugin_public, 'get_description_reviews_handler');
        $this->loader->add_action('wp_ajax_policycloud_marketplace_create_review', $plugin_public, 'create_review_handler');
        $this->loader->add_action('wp_ajax_nopriv_policycloud_marketplace_create_review', $plugin_public, 'create_review_handler');
        $this->loader->add_action('wp_ajax_policycloud_marketplace_delete_review', $plugin_public, 'delete_review_handler');
        $this->loader->add_action('wp_ajax_nopriv_policycloud_marketplace_delete_review', $plugin_public, 'delete_review_handler');
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

    /**
     * Return the error message derived from a file upload error code.
     *
     * @uses    PolicyCloud_Marketplace_Public::account_registration()
     *
     * @since   1.0.0
     * @author  Alexandros Raikos <araikos@unipi.gr>
     */
    public static function fileUploadErrorInterpreter($code)
    {
        $errors = array(
            0 => 'There is no error, the file uploaded with success',
            1 => 'The uploaded file exceeds the upload_max_filesize directive.',
            2 => 'The uploaded file exceeds the MAX_FILE_SIZE directive that was specified in the HTML form',
            3 => 'The uploaded file was only partially uploaded',
            4 => 'No file was uploaded',
            6 => 'Missing a temporary folder',
            7 => 'Failed to write file to disk.',
            8 => 'A PHP extension stopped the file upload.',
        );
        return $errors[$code] ?? $code;
    }


    /**
     * Send a request to the PolicyCloud Marketplace API.
     * Documentation: https://documenter.getpostman.com/view/16776360/TzsZs8kn#intro
     *
     * @param string $http_method The standardized HTTP method used for the request.
     * @param array $data The data to be sent according to the documented schema.
     * @param string $token The encoded user access token.
     * @param array $additional_headers Any additional HTTP headers for the request.
     *
     * @throws InvalidArgumentException For missing WordPress settings.
     * @throws ErrorException For connectivity and other API issues.
     *
     * @since   1.0.0
     * @author  Alexandros Raikos <araikos@unipi.gr>
     */
    public static function api_request($http_method, $uri, $data = [], $token = null, $headers = null, $skip_encoding = false)
    {

        // Retrieve hostname URL.
        $options = get_option('policycloud_marketplace_plugin_settings');
        if (empty($options['marketplace_host'])) {
            throw new InvalidArgumentException("No PolicyCloud Marketplace API hostname was defined in WordPress settings.");
        }
        if (empty($options['api_access_token'])) {
            throw new InvalidArgumentException("No PolicyCloud Marketplace API access key was defined in WordPress settings.");
        }

        if (!empty($data)) {
            $data = ($skip_encoding) ?  $data : json_encode($data);
        }
        // Contact Marketplace login API endpoint.
        $curl = curl_init();
        curl_setopt_array($curl, [
            CURLOPT_URL => $options['marketplace_host'] . $uri,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => $http_method,
            CURLOPT_POSTFIELDS => (!empty($data)) ?  $data : null,
            CURLOPT_HTTPHEADER => $headers ?? ['Content-Type: application/json', (!empty($token) ? ('x-access-token: ' . $token) : null), 'x-more-time: ' . $options['api_access_token']]
        ]);

        // Get the data.
        $response = curl_exec($curl);
        $curl_http = curl_getinfo($curl, CURLINFO_HTTP_CODE);

        // Handle errors.
        if (curl_errno($curl)) {
            throw new Exception("Unable to reach the Marketplace server. More details: " . curl_error($curl));
        }

        curl_close($curl);
        if ($curl_http != 200 && $curl_http != 201) {
            throw new PolicyCloudMarketplaceAPIError(
                "The PolicyCloud Marketplace API encountered an HTTP " . $curl_http . " status code. More information: " . $response ?? '',
                $curl_http
            );
        } else {
            if (isset($response)) {
                if (is_string($response)) {
                    $decoded = json_decode($response, true);
                    if (json_last_error() === JSON_ERROR_NONE) {
                        if ($decoded['_status'] == 'successful') {
                            return $decoded;
                        } else {
                            throw new PolicyCloudMarketplaceAPIError(
                                'PolicyCloud Marketplace error when contacting ' . $uri . ': ' . $decoded['message'],
                                $curl_http
                            );
                        }
                    } else {
                        return $response;
                    }
                }
            } else {
                curl_close($curl);
                throw new ErrorException("There was no response.");
            };
        }
    }
}
