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
     * @since   1.0.0
     * @param   string    $plugin_name       The name of the plugin.
     * @param   string    $version    The version of this plugin.
     */
    public function __construct($plugin_name, $version)
    {
        require_once plugin_dir_path(dirname(__FILE__)) . 'public/partials/policycloud-marketplace-public-display.php';
        require_once plugin_dir_path(dirname(__FILE__)) . 'public/class-policycloud-marketplace-user.php';
        require_once plugin_dir_path(dirname(__FILE__)) . 'public/partials/policycloud-marketplace-public-user-display.php';
        require_once plugin_dir_path(dirname(__FILE__)) . 'public/class-policycloud-marketplace-description.php';
        require_once plugin_dir_path(dirname(__FILE__)) . 'public/partials/policycloud-marketplace-public-description-display.php';

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
        wp_enqueue_style('policycloud-marketplace-public-descriptions', plugin_dir_url(__FILE__) . 'css/policycloud-marketplace-public-descriptions.css', array(), $this->version, 'all');
        wp_enqueue_style('policycloud-marketplace-public-accounts', plugin_dir_url(__FILE__) . 'css/policycloud-marketplace-public-accounts.css', array(), $this->version, 'all');
    }

    /**
     * Register the JavaScript for the public-facing side of the site.
     *
     * @since   1.0.0
     * @author  Alexandros Raikos <araikos@unipi.gr>
     */
    public function enqueue_scripts()
    {
        // Generic script.
        wp_enqueue_script("policycloud-marketplace", plugin_dir_url(__FILE__) . 'js/policycloud-marketplace-public.js', array('jquery'), $this->version, false);
        wp_localize_script("policycloud-marketplace", 'GlobalProperties', array(
            "rootURLPath" => (empty(parse_url(get_site_url())['path']) ? "/" : parse_url(get_site_url())['path']),
            "archivePage" => self::get_plugin_setting('archive_page'),
            "ajaxURL" => admin_url('admin-ajax.php')
        ));

        // Accounts related scripts.
        wp_register_script("policycloud-marketplace-account-registration", plugin_dir_url(__FILE__) . 'js/policycloud-marketplace-public-account-registration.js', array('jquery', 'policycloud-marketplace'), $this->version, false);
        wp_register_script("policycloud-marketplace-account-authentication", plugin_dir_url(__FILE__) . 'js/policycloud-marketplace-public-account-authentication.js', array('jquery', 'policycloud-marketplace'), $this->version, false);
        wp_register_script("policycloud-marketplace-account", plugin_dir_url(__FILE__) . 'js/policycloud-marketplace-public-account.js', array('jquery', 'policycloud-marketplace'), $this->version, false);

        // Content related scripts.
        wp_register_script("policycloud-marketplace-description", plugin_dir_url(__FILE__) . 'js/policycloud-marketplace-public-description.js', array('jquery', 'policycloud-marketplace'), $this->version, false);
        wp_register_script(".policycloud-marketplace.descriptions.archive", plugin_dir_url(__FILE__) . 'js/policycloud-marketplace-public-description-archive.js', array('jquery', 'policycloud-marketplace'), $this->version, false);
        wp_register_script("policycloud-marketplace-description-creation", plugin_dir_url(__FILE__) . 'js/policycloud-marketplace-public-description-creation.js', array('jquery', 'policycloud-marketplace'), $this->version, false);
    }


    /**
     * The generalized handler for AJAX calls.
     *
     * @param string $action The action slug used in WordPress.
     * @param callable $completion The callback for completed data.
     * @return void The function simply echoes the response to the
     *
     * @usedby All functions triggered by the WordPress AJAX handler.
     *
     * @author Alexandros Raikos <alexandros@araikos.gr>
     * @since 1.4.0
     */
    private function ajax_handler($completion): void
    {
        $action = sanitize_key($_POST['action']);

        // Verify the action related nonce.
        if (!wp_verify_nonce($_POST['nonce'], $action)) {
            http_response_code(403);
            die("Unverified request for action: " . $action);
        }

        // Send shipment using POST data and handle errors.
        try {
            /** @var array $data The filtered $_POST data excluding WP specific keys. */
            $data = $completion(array_filter($_POST, function ($key) {
                return ($key != 'action' && $key != 'nonce');
            }, ARRAY_FILTER_USE_KEY));

            // Prepare the data and send.
            if (empty($data)) {
                http_response_code(200);
                die();
            } else {
                $data = json_encode($data);
                if ($data == false) {
                    throw new RuntimeException("There was an error while encoding the data to JSON.");
                } else {
                    http_response_code(200);
                    die(json_encode($data));
                }
            }
        } catch (PolicyCloudMarketplaceUnauthorizedRequestException $e) {
            http_response_code(401);
            die($e->getMessage());
        } catch (PolicyCloudMarketplaceInvalidDataException $e) {
            http_response_code(400);
            die($e->getMessage());
        } catch (PolicyCloudMarketplaceMissingOptionsException $e) {
            http_response_code(404);
            die($e->getMessage());
        } catch (\Exception $e) {
            http_response_code(500);
            die($e->getMessage());
        }
    }

    /**
     * An error registrar for asynchronous throwing functions.
     *
     * @param callable $completion The action that needs to be done.
     *
     * @uses show_alert()
     *
     * @author Alexandros Raikos <alexandros@araikos.gr>
     * @since 1.4.0
     */
    private static function exception_handler($completion): void
    {
        try {
            // Run completion function.
            $completion();
        } catch (\Exception $e) {
            // Display the error.

            show_alert($e->getMessage());
        }
    }

    /**
     *
     * Accounts
     *
     * This section refers to functionality and shortcodes relevant to user accounts.
     *
     */

    /**
     * Register all the shortcodes concerning user authentication
     *
     * @since   1.0.0
     * @author  Alexandros Raikos <araikos@unipi.gr>
     */
    public function add_accounts_shortcodes()
    {
        // Registration sequence.
        add_shortcode('policycloud-marketplace-user-registration', 'PolicyCloud_Marketplace_Public::account_user_registration_shortcode');

        // Log in sequence.
        add_shortcode('policycloud-marketplace-user-authentication', 'PolicyCloud_Marketplace_Public::account_user_authentication_shortcode');

        // Reset password shortcode.
        add_shortcode('policycloud-marketplace-user-reset-password', 'PolicyCloud_Marketplace_Public::account_user_reset_password_shortcode');

        // Account page shortcode.
        add_shortcode('policycloud-marketplace-user', 'PolicyCloud_Marketplace_Public::account_user_shortcode');
    }

    /**
     * Add a menu item to a selected menu, which conditionally switches
     * from log in to log out actions.
     *
     * @since   1.0.0
     * @author  Alexandros Raikos <araikos@unipi.gr>
     */
    public static function add_menu_items($items, $args)
    {
        // Retrieve credentials.
        try {
            $options = self::get_plugin_setting(
                true,
                'selected_menu',
                'login_page',
                'account_page',
                'registration_page',
                'upload_page',
                'archive_page'
            );
        } catch (PolicyCloudMarketplaceMissingOptionsException $e) {
            return $items;
        }

        if (!function_exists('list_url_wrap')) {
            function list_url_wrap($url)
            {
                $random_id = rand(1000, 10000);
                return '<li id="menu-item-' . $random_id . '" class="menu-item menu-item-type-post_type menu-item-object-page menu-item-' . $random_id . '">' . $url . '</li>';
            }
        }

        // Add conditional menu item.
        if ($args->theme_location == $options['selected_menu']) {
            if (PolicyCloud_Marketplace_User::is_authenticated()) {
                $links = list_url_wrap('<a href="' . $options['upload_page'] . '">Create</a>');
                $links .= list_url_wrap('<a href="' . $options['account_page'] . '">My Account</a>');
                $links .= list_url_wrap('<a class="policycloud-logout">Log out</a>');
            } else {
                $links = list_url_wrap('<a href="' . $options['login_page'] . '">Log In</a>');
                $links .= list_url_wrap('<a href="' . $options['registration_page'] . '">Register</a>');
            }
            $links .= list_url_wrap(
                "
                <div class=\"policycloud-marketplace menu-search\">
                    <button class=\"tactile\" data-action=\"description-search\">
                        <span class=\"fas fa-search\"></span>
                    </button>
                </div>
                "
            );
            return $items . $links;
        } else {
            return $items;
        }
    }

    public static function get_plugin_setting(bool $throw, string ...$id)
    {
        $options = get_option('policycloud_marketplace_plugin_settings');

        $settings = [];
        foreach ($id as $key) {
            if (empty($options[$key])) {
                if ($throw) {
                    throw new PolicyCloudMarketplaceMissingOptionsException(
                        "Please finish setting up the Policy Cloud Marketplace in the WordPress settings."
                    );
                } else {
                    show_alert(
                        "Please finish setting up the Policy Cloud Marketplace in the WordPress settings.",
                        'notice'
                    );
                }
            } else {
                if (!function_exists('str_contains')) {
                    function str_contains($haystack, $needle)
                    {
                        return $needle !== '' && mb_strpos($haystack, $needle) !== false;
                    }
                }
                if (str_contains($key, '_page')) {
                    $settings[$key] = get_page_link($options[$key]);
                } else {
                    $settings[$key] = $options[$key];
                }
            }
        }

        if (count($settings) == 1) {
            return $settings[$id[0]];
        } else {
            return $settings;
        }
    }

    /**
     * Register the shortcodes for user registration.
     *
     * @since   1.0.0
     * @author  Alexandros Raikos <araikos@unipi.gr>
     */
    public static function account_user_registration_shortcode()
    {
        self::exception_handler(
            function () {
                $options = self::get_plugin_setting(true, 'login_page', 'account_page', 'tos_url');

                wp_enqueue_script("policycloud-marketplace-account-registration");
                wp_localize_script('policycloud-marketplace-account-registration', 'AccountRegistrationProperties', array(
                    'nonce' => wp_create_nonce('policycloud_marketplace_account_user_registration'),
                    'accountPage' => $options['account_page']
                ));

                account_user_registration_html(
                    $options['login_page'],
                    $options['tos_url'] ?? '',
                    PolicyCloud_Marketplace_Account::is_authenticated()
                );
            }
        );
    }


    /**
     * Register the shortcode for account authentication
     *
     * @since   1.0.0
     * @author  Alexandros Raikos <araikos@unipi.gr>
     */
    public static function account_user_authentication_shortcode()
    {
        wp_enqueue_script("policycloud-marketplace-account-authentication");
        wp_localize_script('policycloud-marketplace-account-authentication', 'AccountAuthenticationProperties', array(
            'nonce' => wp_create_nonce('policycloud_marketplace_account_user_authentication')
        ));

        account_user_authentication_html(
            self::get_plugin_setting(true, 'registration_page'),
            self::get_plugin_setting(true, 'password_reset_page'),
            PolicyCloud_Marketplace_Account::is_authenticated()
        );
    }

    /**
     * Register the shortcode for account password reset.
     *
     * @since   1.0.0
     * @author  Alexandros Raikos <araikos@unipi.gr>
     */
    public static function account_user_reset_password_shortcode()
    {
        wp_enqueue_script("policycloud-marketplace-account-authentication");
        wp_localize_script('policycloud-marketplace-account-authentication', 'AccountAuthenticationProperties', array(
            'nonce' => wp_create_nonce('policycloud_marketplace_account_user_password_reset')
        ));

        account_user_reset_password_html(PolicyCloud_Marketplace_Account::is_authenticated());
    }

    /**
     * Requests account related content to display for authenticated users.
     *
     * @uses    retrieve_token()
     * @uses    verify_user()
     * @uses    get_user_information()
     * @uses    get_user_descriptions()
     * @uses    get_user_statistics()
     * @uses    account_html()
     *
     * @since   1.0.0
     * @author  Alexandros Raikos <araikos@unipi.gr>
     */
    public static function account_user_shortcode()
    {

        self::exception_handler(
            function () {
                if (PolicyCloud_Marketplace_User::is_authenticated()) {
                    $user_id = !empty($_GET['user']) ? sanitize_user($_GET['user']) : null;
                    $visitor = !empty($user_id);
                    $self = new PolicyCloud_Marketplace_User();
                    if ($visitor) {
                        if ($self->is_verified()) {
                            $user = new PolicyCloud_Marketplace_User($user_id);
                        } else {
                            throw new PolicyCloudMarketplaceUnauthorizedRequestException(
                                "You need to be verified in order to view other user accounts."
                            );
                        }
                        if ($self->username === $user_id) {
                            show_alert(
                                "You're currently viewing your profile as it is viewed by other registered users.
                                <a href=\"" . self::get_plugin_setting(true, 'account_page') . "\">Return to your account page.</a>",
                                'notice'
                            );
                        }
                    } else {
                        $user = $self;
                    }

                    $data = [
                        'username' => $user->username,
                        'picture' => $user->picture,
                        'information' => $user->information,
                        'statistics' => $user->statistics,
                        'descriptions' => $user->descriptions,
                        'reviews' => $user->reviews,
                        'approvals' => ($self->is_admin() && $self->is_verified()) ? $user->approvals : null,
                        'metadata' => $user->metadata,
                        'preferences' => $user->preferences
                    ];

                    // Localize script.
                    wp_enqueue_script('policycloud-marketplace-account');
                    wp_localize_script('policycloud-marketplace-account', 'AccountEditingProperties', array(
                        'nonce' => wp_create_nonce('policycloud_marketplace_account_user_edit'),
                        'deletionNonce' => wp_create_nonce('policycloud_marketplace_account_user_deletion'),
                        'verificationRetryNonce' => wp_create_nonce('policycloud_marketplace_account_user_retry_verification'),
                        'requestDataCopyNonce' => wp_create_nonce('policycloud_marketplace_account_user_data_request'),
                        'userID' => $user->id
                    ));

                    // TODO @alexandrosraikos: Add account name as head meta title. #54
                    remove_action('wp_head', '_wp_render_title_tag', 1);
                    add_filter(
                        'pre_get_document_title',
                        function ($title) use ($user) {
                            $title = $user->informaton['name'] . ' ' . $user->information['surname'];
                            return $title;
                        },
                        9999,
                        1
                    );

                    if ($self->is_admin()) {
                        account_user_html(
                            $data,
                            ($self->is_admin() && $self->is_verified()),
                            $visitor,
                            self::get_plugin_setting(
                                true,
                                'description_page',
                                'archive_page',
                                'upload_page'
                            )
                        );
                    } else {
                        account_user_html(
                            [
                                'username' => $user->username,
                                'picture' => $user->picture,
                                'information' => $user->information,
                                'statistics' => $user->statistics,
                                'descriptions' => $user->descriptions,
                                'reviews' => $user->reviews,
                                'metadata' => $user->metadata,
                                'preferences' => $user->preferences
                            ],
                            $self->is_admin(),
                            $visitor,
                            self::get_plugin_setting(
                                true,
                                'description_page',
                                'archive_page',
                                'upload_page'
                            )
                        );
                    }
                } else {
                    show_alert("You need to be logged in to view accounts.");
                }
            }
        );
    }

    /**
     * Handle user registration AJAX requests.
     *
     * @uses    PolicyCloud_Marketplace_Public::account_registration()
     *
     * @since   1.0.0
     * @author  Alexandros Raikos <araikos@unipi.gr>
     */
    public function account_user_registration_handler()
    {
        $this->ajax_handler(
            function ($data) {

                return PolicyCloud_Marketplace_User::register([
                    'username' => sanitize_user($data['username']),
                    'password' => stripslashes($data['password']),
                    'password-confirm' => stripslashes($data['password-confirm']),
                    'name' => filter_var(stripslashes($data['name']), FILTER_SANITIZE_STRING),
                    'surname' => filter_var(stripslashes($data['surname']), FILTER_SANITIZE_STRING),
                    'title' => filter_var($data['title'] ?? '', FILTER_SANITIZE_STRING),
                    'gender' => filter_var($data['gender'] ?? '', FILTER_SANITIZE_STRING),
                    'organization' => filter_var(stripslashes($data['organization'] ?? ''), FILTER_SANITIZE_STRING),
                    'email' => filter_var($data['email'], FILTER_SANITIZE_EMAIL),
                    'phone' => filter_var($data['phone'] ?? '', FILTER_SANITIZE_NUMBER_INT),
                    'social-title' => array_map(
                        function ($title) {
                            return filter_var(stripslashes($title), FILTER_SANITIZE_STRING);
                        },
                        $data['social-title'] ?? []
                    ),
                    'social-url' =>  array_map(
                        function ($url) {
                            return filter_var($url, FILTER_SANITIZE_URL);
                        },
                        $data['social-url'] ?? []
                    ),
                    'about' => $data['about'] ?? '',
                ]);
            }
        );
    }

    /**
     * Handle user login AJAX requests.
     *
     * @uses    PolicyCloud_Marketplace_Public::account_authentication)
     *
     * @since   1.0.0
     * @author  Alexandros Raikos <araikos@unipi.gr>
     */
    public function account_user_authentication_handler()
    {
        $this->ajax_handler(
            function ($data) {

                return PolicyCloud_Marketplace_User::authenticate(
                    $data['username-email'],
                    $data['password']
                );
            }
        );
    }

    public function account_user_password_reset_handler()
    {
        $this->ajax_handler(
            function ($data) {

                PolicyCloud_Marketplace_User::reset_password(
                    $data['email']
                );
            }
        );
    }

    /**
     * Handle user account editing AJAX requests.
     *
     * @uses    PolicyCloud_Marketplace_Public::account_registration()
     *
     * @since   1.0.0
     * @author  Alexandros Raikos <araikos@unipi.gr>
     */
    public function account_user_editing_handler()
    {
        $this->ajax_handler(
            function ($data) {

                $user = new PolicyCloud_Marketplace_User($data['username'] ?? null);
                switch ($data['subsequent_action']) {
                    case 'edit_account_user':
                        $user->update(
                            [
                                'password' => stripslashes($data['password'] ?? ''),
                                'password-confirm' => stripslashes($data['password-confirm'] ?? ''),
                                'current-password' => stripslashes($data['current-password'] ?? ''),
                                'name' => stripslashes($data['name']),
                                'surname' => stripslashes($data['surname']),
                                'title' => $data['title'] ?? '',
                                'gender' => $data['gender'] ?? '',
                                'organization' => stripslashes($data['organization'] ?? ''),
                                'email' => $data['email'],
                                'phone' => $data['phone'] ?? '',
                                'socials-title' => $data['socials-title'] ?? '',
                                'socials-url' => $data['socials-url'] ?? '',
                                'about' => stripslashes($data['about'] ?? ''),
                                'public-email' => $data['public-email'],
                                'public-phone' => $data['public-phone'],
                            ],
                            $_FILES['profile_picture']
                        );
                        break;
                    case 'delete_profile_picture':
                        break;
                    default:
                        throw new PolicyCloudMarketplaceInvalidDataException(
                            "No subsequent action was defined."
                        );
                        break;
                }
            }
        );
    }

    public function account_user_verification_retry_handler()
    {
        $this->ajax_handler(
            function () {

                $user = new PolicyCloud_Marketplace_User();
                $user->resend_verification_email();
            }
        );
    }

    /**
     * Handle user account editing AJAX requests.
     *
     * @since   1.0.0
     * @author  Alexandros Raikos <araikos@unipi.gr>
     */
    public function account_user_data_request_handler()
    {
        $this->ajax_handler(
            function ($data) {

                $user = new PolicyCloud_Marketplace_User();
                $data = $user->get_data_copy();
                return $data;
            }
        );
    }

    /**
     * Handle user account deletion AJAX requests.
     *
     * @since   1.0.0
     * @author  Alexandros Raikos <araikos@unipi.gr>
     */
    public function account_user_deletion_handler()
    {
        $this->ajax_handler(
            function ($data) {

                $user = new PolicyCloud_Marketplace_User();
                $user->delete($data['current_password']);
            }
        );
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
     * @since   1.0.0
     * @author  Alexandros Raikos <araikos@unipi.gr>
     */
    public function add_description_shortcodes()
    {
        add_shortcode('policycloud-marketplace-descriptions-featured', 'PolicyCloud_Marketplace_Public::descriptions_featured_shortcode');

        // Read multiple objects sequence.
        add_shortcode('policycloud-marketplace-description-archive', 'PolicyCloud_Marketplace_Public::descriptions_archive_shortcode');

        // Read single object sequence.
        add_shortcode('policycloud-marketplace-description', 'PolicyCloud_Marketplace_Public::description_shortcode');

        // Create object sequence.
        add_shortcode('policycloud-marketplace-description-creation', 'PolicyCloud_Marketplace_Public::description_creation_shortcode');
    }

    /**
     * Display multiple Description Objects for visitors and authenticated users.
     *
     * @since   1.0.0
     * @author  Alexandros Raikos <araikos@unipi.gr>
     */
    public static function descriptions_archive_shortcode()
    {
        self::exception_handler(
            function () {
                wp_enqueue_script(".policycloud-marketplace.descriptions.archive");

                descriptions_archive_html(
                    PolicyCloud_Marketplace_Description::get_all(),
                    PolicyCloud_Marketplace_Description::get_filters_range(),
                    self::get_plugin_setting(true, 'description_page')
                );
            }
        );
    }

    /**
     * Display featured descriptions for visitors and authenticated users.
     *
     * @since   1.0.0
     * @author  Alexandros Raikos <araikos@unipi.gr>
     */
    public static function descriptions_featured_shortcode()
    {
        self::exception_handler(
            function () {
                $featured = PolicyCloud_Marketplace_Description::get_featured();
                wp_enqueue_script(".policycloud-marketplace.descriptions.archive");
                featured_descriptions_html(
                    $featured,
                    self::get_plugin_setting(true, 'description_page')
                );
            }
        );
    }

    /**
     * Display the description creation form for authenticated users.
     *
     * @since   1.0.0
     * @author  Alexandros Raikos <araikos@unipi.gr>
     */
    public static function description_creation_shortcode()
    {
        self::exception_handler(
            function () {
                if (PolicyCloud_Marketplace_User::is_authenticated()) {
                    wp_enqueue_script("policycloud-marketplace-description-creation");
                    wp_localize_script('policycloud-marketplace-description-creation', 'DescriptionCreationProperties', array(
                        'nonce' => wp_create_nonce('policycloud_marketplace_description_creation'),
                        'descriptionPage' => self::get_plugin_setting(true, 'description_page')
                    ));

                    description_editor_html();
                } else {
                    show_alert("You need to be logged in to create a description.");
                }
            }
        );
    }

    /**
     * Display a single description object for authenticated users.
     *
     * @since   1.0.0
     * @author  Alexandros Raikos <araikos@unipi.gr>
     */
    public static function description_shortcode()
    {

        self::exception_handler(
            function () {
                if (empty($_GET['did'])) {
                    throw new PolicyCloudMarketplaceInvalidDataException(
                        "Please specify the ID of the description."
                    );
                }
                $description = new PolicyCloud_Marketplace_Description($_GET['did']);
                $permissions = [
                    'authenticated' => PolicyCloud_Marketplace_User::is_authenticated(),
                    'provider' => false,
                    'administrator' =>  false
                ];

                if ($permissions['authenticated']) {
                    $user = new PolicyCloud_Marketplace_User();

                    if ($user->is_verified() || $description->is_provider($user)) {

                        $permissions['provider'] = $description->is_provider($user);
                        $permissions['administrator'] = $user->is_admin();

                        $image_blobs =  array_map(
                            function ($image) {
                                return $image->pull();
                            },
                            array_filter(
                                $description->assets ?? [],
                                function ($category) {
                                    return  $category == 'images';
                                },
                                ARRAY_FILTER_USE_KEY
                            )['images'] ?? []
                        );

                        // $reviews = $description->get_reviews();
                    } else {
                        $permissions['authenticated'] = false;
                        show_alert("You need to be verified in order to view description details.", 'notice');
                    }
                }

                wp_enqueue_script('policycloud-marketplace-description');
                wp_localize_script('policycloud-marketplace-description', 'DescriptionEditingProperties', array(
                    'nonce' => wp_create_nonce('policycloud_marketplace_description_editing'),
                    'descriptionID' => $description->id,
                    'approvalNonce' => $permissions['administrator'] ? wp_create_nonce('policycloud_marketplace_description_approval') : null,
                    'deletionNonce' => ($permissions['administrator'] || $permissions['provider']) ? wp_create_nonce('policycloud_marketplace_description_deletion') : null,
                    'deleteRedirect' => $permissions['provider'] ? (self::get_plugin_setting(true, 'account_page') . "#descriptions") : (self::get_plugin_setting(true, 'account_page') . "#approvals")
                ));
                // TODO @alexandrosraikos: Add main image selector on the editing form. #69
                // TODO @alexandrosraikos: Restructure gallery to dynamic thumbnail usage. #68
                // TODO @alexandrosraikos: Add description name as head meta title. #54
                // TODO @alexandrosraikos: Add user reviews interface CRUD. #38
                // TODO @alexandrosraikos: Add video viewer in gallery. #39

                description_html(
                    $description,
                    $image_blobs ?? null,
                    self::get_plugin_setting(
                        true,
                        'login_page',
                        'account_page',
                        'archive_page'
                    ),
                    $reviews ?? null,
                    $permissions
                );
            }
        );
    }

    /**
     * Handle description editing AJAX requests.
     *
     * @uses    PolicyCloud_Marketplace_Public::description_editing()
     *
     * @since   1.0.0
     * @author  Alexandros Raikos <araikos@unipi.gr>
     */
    public function description_editing_handler()
    {
        $this->ajax_handler(
            function ($data) {

                $description = new PolicyCloud_Marketplace_Description($data['description_id']);

                switch ($data['subsequent_action']) {
                    case 'description-editing':
                        $description->update(
                            [
                                "title" => sanitize_text_field($data['title']),
                                "type" => sanitize_text_field($data['type']),
                                "subtype" => sanitize_text_field($data['subtype'] ?? ''),
                                "owner" => sanitize_text_field($data['owner'] ?? ''),
                                "description" => sanitize_text_field($data['description']),
                                "fieldOfUse" => explode(", ", $data['fields-of-use'] ?? ''),
                                "comments" => sanitize_text_field($data['comments'] ?? '')
                            ],
                            array_filter(
                                array_keys($_FILES),
                                function ($key) {
                                    return (substr($key, 0, 5) === "image"  ||
                                        substr($key, 0, 5) === "video"  ||
                                        substr($key, 0, 4) === "file");
                                }
                            )
                        );
                        break;
                    case 'asset-deletion':
                        foreach ($description->assets[$data['file-type']] as $asset) {
                            if ($asset->id == $data['file-identifier']) {
                                $asset->delete();
                                return;
                            }
                        }
                        throw new PolicyCloudMarketplaceInvalidDataException("The file could not be found.");
                        break;
                    case 'asset-download':
                        foreach ($description->assets[$data['file-type']] as $asset) {
                            if ($asset->id == $data['file-identifier']) {
                                return $asset->get_download_url();
                            }
                        }
                        throw new PolicyCloudMarketplaceInvalidDataException("The file could not be found.");
                        break;
                    default:
                        throw new PolicyCloudMarketplaceInvalidDataException("No subsequent action was defined.");
                        break;
                }
            }
        );
    }

    /**
     * Handle description approval AJAX requests.
     *
     * @uses    PolicyCloud_Marketplace_Public::description_approval()
     *
     * @since   1.0.0
     * @author  Alexandros Raikos <araikos@unipi.gr>
     */
    function description_approval_handler()
    {
        $this->ajax_handler(
            function ($data) {

                $user = new PolicyCloud_Marketplace_User();
                if ($user->is_admin()) {

                    $description = new PolicyCloud_Marketplace_Description($data['description_id']);
                    $description->approve($data['approval']);
                }
            }
        );
    }

    /**
     * Handle description creation AJAX requests.
     *
     * @uses    PolicyCloud_Marketplace_Public::description_creation()
     *
     * @since   1.0.0
     * @author  Alexandros Raikos <araikos@unipi.gr>
     */
    public function description_creation_handler()
    {
        $this->ajax_handler(
            function ($data) {

                return (PolicyCloud_Marketplace_Description::create(
                    [
                        "title" => sanitize_text_field($data['title']),
                        "type" => sanitize_text_field($data['type']),
                        "subtype" => sanitize_text_field($data['subtype'] ?? ''),
                        "owner" => sanitize_text_field($data['owner'] ?? ''),
                        "description" => sanitize_text_field($data['description']),
                        "fieldOfUse" => explode(", ", $data['fields-of-use'] ?? []),
                        "comments" => sanitize_text_field($data['comments'] ?? '')
                    ]
                ));
            }
        );
    }


    /**
     * Handle description deletion AJAX requests.
     *
     * @uses    PolicyCloud_Marketplace_Public::description_creation()
     *
     * @since   1.0.0
     * @author  Alexandros Raikos <araikos@unipi.gr>
     */
    public function description_deletion_handler()
    {
        $this->ajax_handler(
            function ($data) {
                $description = new PolicyCloud_Marketplace_Description($data['description_id']);
                $description->delete();
            }
        );
    }
}
