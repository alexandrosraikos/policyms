<?php
/**
 * The public-facing functionality of the plugin.
 *
 * @link       https://github.com/alexandrosraikos/policyms/
 * @since      1.0.0
 *
 * @package    PolicyMS
 * @subpackage PolicyMS/public
 */

/**
 * The public-facing functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the public-facing stylesheet and JavaScript.
 *
 * @package    PolicyMS
 * @subpackage PolicyMS/public
 * @author     Alexandros Raikos <alexandros@araikos.gr>
 */
class PolicyMS_Public {


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
	 * @param   string $plugin_name       The name of the plugin.
	 * @param   string $version    The version of this plugin.
	 */
	public function __construct( $plugin_name, $version ) {
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'public/partials/policyms-public-display.php';
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'public/partials/policyms-public-user-display.php';
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'public/partials/policyms-public-description-display.php';

		$this->plugin_name = $plugin_name;
		$this->version     = $version;
	}

	/**
	 *
	 * Generic
	 *
	 * This section refers to global functionality.
	 */

	/**
	 * Register the stylesheets for the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_styles() {
		wp_enqueue_style(
			$this->plugin_name,
			plugin_dir_url( __FILE__ ) . 'css/policyms-public.css',
			array(),
			$this->version,
			'all'
		);
		wp_enqueue_style(
			'policyms-public-descriptions',
			plugin_dir_url( __FILE__ ) . 'css/policyms-public-descriptions.css',
			array(),
			$this->version,
			'all'
		);
		wp_enqueue_style(
			'policyms-public-accounts',
			plugin_dir_url( __FILE__ ) . 'css/policyms-public-accounts.css',
			array(),
			$this->version,
			'all'
		);
	}

	/**
	 * Register the JavaScript for the public-facing side of the site.
	 *
	 * @since   1.0.0
	 */
	public function enqueue_scripts() {

		/**
		 * Globally enqueued scripts.
		 */

		wp_enqueue_script(
			'policyms',
			plugin_dir_url( __FILE__ ) . 'js/policyms-public.js',
			array( 'jquery' ),
			$this->version,
			false
		);

		wp_enqueue_script(
			'fontawesome',
			'https://use.fontawesome.com/releases/v5.15.4/js/all.js',
			array( 'policyms' ),
			$this->version,
			false
		);

		/**
		 * Registered scripts for enqueuing.
		 */

		wp_register_script(
			'policyms-account-registration',
			plugin_dir_url( __FILE__ ) . 'js/policyms-public-account-registration.js',
			array( 'jquery', 'policyms' ),
			$this->version,
			false
		);

		wp_register_script(
			'policyms-account-authentication',
			plugin_dir_url( __FILE__ ) . 'js/policyms-public-account-authentication.js',
			array( 'jquery', 'policyms' ),
			$this->version,
			false
		);

		wp_register_script(
			'policyms-account',
			plugin_dir_url( __FILE__ ) . 'js/policyms-public-account.js',
			array( 'jquery', 'policyms' ),
			$this->version,
			false
		);

		wp_register_script(
			'policyms-description',
			plugin_dir_url( __FILE__ ) . 'js/policyms-public-description.js',
			array( 'jquery', 'policyms' ),
			$this->version,
			false
		);

		wp_register_script(
			'policyms-description-archive',
			plugin_dir_url( __FILE__ ) . 'js/policyms-public-description-archive.js',
			array( 'jquery', 'policyms' ),
			$this->version,
			false
		);

		wp_register_script(
			'policyms-description-creation',
			plugin_dir_url( __FILE__ ) . 'js/policyms-public-description-creation.js',
			array( 'jquery', 'policyms' ),
			$this->version,
			false
		);
	}


	/**
	 * The generalized handler for AJAX calls.
	 *
	 * @param callable $completion The callback for completed data.
	 * @throws RuntimeException When the json can't be decoded.
	 *
	 * @since 1.4.0
	 */
	private function ajax_handler( $completion ) {
		if ( ! isset( $_POST['action'] ) || ! isset( $_POST['nonce'] ) ) {
			http_response_code( 400 );
			die( 'The required WordPress fields were not specified.' );
		}

		// Verify the action related nonce.
		$action = sanitize_key( $_POST['action'] );
		if ( ! wp_verify_nonce( sanitize_key( $_POST['nonce'] ), $action ) ) {
			http_response_code( 403 );
			die( 'Unverified request for action: ' . esc_attr( $action ) );
		}

		try {
			// Execute the callback.
			$data = $completion(
				array_filter(
					$_POST,
					function ( $key ) {
						return ( 'action' !== $key && 'nonce' !== $key );
					},
					ARRAY_FILTER_USE_KEY
				)
			);

			// Prepare the data and send.
			http_response_code( 200 );
			die( ! empty( $data ) ? esc_js( wp_json_encode( $data ) ) : null );
		} catch ( PolicyMSUnauthorizedRequestException $e ) {
			http_response_code( 401 );
			die( esc_html( $e->getMessage() ) );
		} catch ( PolicyMSInvalidDataException $e ) {
			http_response_code( 400 );
			die( esc_html( $e->getMessage() ) );
		} catch ( PolicyMSMissingOptionsException $e ) {
			http_response_code( 404 );
			die( esc_html( $e->getMessage() ) );
		} catch ( \Exception $e ) {
			http_response_code( 500 );
			die( 'There was an internal error.' );
		}
	}

	/**
	 * An error registrar for asynchronous throwing functions.
	 *
	 * @param callable $completion The action that needs to be done.
	 *
	 * @since 1.4.0
	 */
	public static function exception_handler( $completion ): void {
		try {
			// Run completion function.
			$completion();
		} catch ( PolicyMSAPIError $e ) {
			// NOTE: No need to escape this self-created HTML output.
			print notice_html( $e->getMessage(), 'error', $e->http_status );
		} catch ( \Exception $e ) {
			// NOTE: No need to escape this self-created HTML output.
			print notice_html( $e->getMessage() );
		}
	}

	/**
	 * Retrieve one or more settings from WordPress Options.
	 *
	 * @param bool   $throw Whether to throw an error.
	 * @param string ...$option_ids A series of one or more option IDs.
	 * @return string|array One or multiple option values.
	 * @throws PolicyMSMissingOptionsException When a requested option isn't registered.
	 *
	 * @since 1.2.0
	 */
	public static function get_setting( bool $throw, string ...$option_ids ) {
		$options  = get_option( 'policyms_plugin_settings' );
		$settings = array();
		foreach ( $option_ids as $id ) {
			if ( $options[ $id ] ) {
				$settings[ $id ] = str_contains( $id, '_page' )
					? get_page_link( intval( $options[ $id ] ) )
					: $options[ $id ];
			} else {
				$message = 'Please finish setting up the PolicyMS in the WordPress settings.';
				if ( $throw ) {
					throw new PolicyMSMissingOptionsException( $message );
				} else {
					// NOTE: No need to escape this self-created HTML output.
					print notice_html( $message, 'notice' );
				}
			}
		}

		return 1 === count( $settings ) ? $settings[ $id[0] ] : $settings;
	}

	/**
	 *
	 * Accounts
	 *
	 * This section refers to functionality and shortcodes relevant to user accounts.
	 */

	/**
	 * Register all the shortcodes concerning user authentication
	 *
	 * @since   1.0.0
	 */
	public function add_accounts_shortcodes() {
		add_shortcode(
			'policyms-user-registration',
			'PolicyMS_Public::account_user_registration_shortcode'
		);

		add_shortcode(
			'policyms-user-authentication',
			'PolicyMS_Public::account_user_authentication_shortcode'
		);

		add_shortcode(
			'policyms-user-reset-password',
			'PolicyMS_Public::account_user_reset_password_shortcode'
		);

		add_shortcode(
			'policyms-user',
			'PolicyMS_Public::account_user_shortcode'
		);

		add_shortcode(
			'policyms-user-egi-redirection',
			'PolicyMS_OAuth_Controller::get_egi_redirection_shortcode'
		);
	}

	/**
	 * Add a menu item to a selected menu, which conditionally switches
	 * from log in to log out actions.
	 *
	 * @param string   $items The HTML list content for the menu items.
	 * @param stdClass $args An object containing wp_nav_menu() arguments.
	 *
	 * @since   1.0.0
	 */
	public static function add_menu_items( $items, $args ) {
		// Retrieve credentials.
		try {
			$options = self::get_setting(
				true,
				'selected_menu',
				'login_page',
				'account_page',
				'registration_page',
			);
		} catch ( \Exception $e ) {
			return $items;
		}

		// Add conditional menu item.
		if ( $args->theme_location === $options['selected_menu'] ) {
			return $items . menu_items_html(
				PolicyMS_User::is_authenticated(),
				$options['login_page'],
				$options['registration_page'],
				$options['account_page']
			);
		} else {
			return $items;
		}
	}

	/**
	 * Register the shortcodes for user registration.
	 *
	 * @since   1.0.0
	 */
	public static function account_user_registration_shortcode() {
		self::exception_handler(
			function () {
				$options = self::get_setting( true, 'login_page', 'account_page', 'tos_url' );

				wp_enqueue_script( 'policyms-account-registration' );

				// NOTE: No need to escape this self-created HTML output.
				print user_registration_html(
					PolicyMS_Account::is_authenticated(),
					wp_create_nonce( 'policyms_account_user_registration' ),
					$options['login_page'],
					$options['account_page'],
					$options['tos_url'],
					new PolicyMS_OAuth_Controller()
				);
			}
		);
	}


	/**
	 * Register the shortcode for account authentication
	 *
	 * @since   1.0.0
	 * @author  Alexandros Raikos <alexandros@araikos.gr>
	 */
	public static function account_user_authentication_shortcode() {
		self::exception_handler(
			function () {
				$home_url = ( empty( wp_parse_url( get_site_url() )['path'] )
					? '/'
					: wp_parse_url( get_site_url() )['path'] );

				// NOTE: No need to escape this self-created HTML output.
				print user_authentication_html(
					wp_create_nonce( 'policyms_account_user_authentication' ),
					$home_url,
					self::get_setting( true, 'registration_page' ),
					self::get_setting( true, 'password_reset_page' ),
					new PolicyMS_OAuth_Controller(),
					PolicyMS_Account::is_authenticated()
				);
			}
		);
	}

	/**
	 * Register the shortcode for account password reset.
	 *
	 * @since   1.4.0
	 */
	public static function account_user_reset_password_shortcode() {
		wp_enqueue_script( 'policyms-account-authentication' );

		// NOTE: No need to escape this self-created HTML output.
		print user_password_reset_html(
			PolicyMS_Account::is_authenticated(),
			wp_create_nonce( 'policyms_account_user_password_reset' )
		);
	}

	public static function get_user_tab_content(
		PolicyMS_User $user,
		string $selected_tab,
		bool $visitor,
		string $description_page
	) {
		if ( ! array_key_exists( $selected_tab, PolicyMS_User::$default_tabs ) ) {
			return notice_html(
				'The requested content is not available.'
			);
		}
		switch ( $selected_tab ) {
			case 'overview':
				return user_overview_html(
					$user->information,
					$user->statistics
				);
			case 'decriptions':
				// TODO @alexandrosraikos: Parse description list parameters.
				return user_descriptions_list_html(
					$user->descriptions,
					$visitor,
					$user->is_admin(),
					$description_page
				);
			case 'reviews':
				// TODO @alexandrosraikos: Parse review list parameters.
				return user_reviews_list_html(
					$user->reviews,
					$visitor,
					$description_page
				);
			case 'approvals':
				// TODO @alexandrosraikos: Parse review list parameters.
				return user_approvals_list_html(
					PolicyMS_Description_Collection::get_pending(),
					$description_page
				);
			case 'profile':
				return user_profile_details_html(
					$user,
					$visitor,
					$user->is_admin(),
					$user->picture,
					new PolicyMS_OAuth_Controller( $user )
				);
		}
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
	 * @author  Alexandros Raikos <alexandros@araikos.gr>
	 */
	public static function account_user_shortcode() {

		self::exception_handler(
			function () {
				if ( PolicyMS_User::is_authenticated() ) {
					$uid     = ! empty( $_GET['user'] )
						? sanitize_user( wp_unslash( $_GET['user'] ) )
						: null;
					$visitor = ! empty( $uid );
					$self    = new PolicyMS_User();

					if ( $visitor ) {
						if ( $self->is_verified() ) {
							$user = new PolicyMS_User( $uid );
						} else {
							throw new PolicyMSUnauthorizedRequestException(
								'You need to be verify your email address in order to view other user accounts.'
							);
						}
					} else {
						$user = $self;
					}

					// Localize script.
					wp_enqueue_script( 'policyms-account' );

					$urls = self::get_setting(
						true,
						'account_page',
						'description_page',
						'archive_page',
						'upload_page'
					);

					// Get selected user tab.
					$selected_tab = 'overview';
					if ( ! empty( $_GET['tab'] ) ) {
						$selected_tab = sanitize_key( wp_unslash( $_GET['tab'] ) );
					}

					// NOTE: No need to escape this self-created HTML output.
					print user_html(
						$user,
						$visitor,
						$user === $self,
						$urls['account_page'],
						self::get_user_tab_content(
							$user,
							$selected_tab,
							$visitor,
							$urls['description_page']
						),
						$selected_tab,
						wp_create_nonce( 'policyms_account_user_edit' ),
						wp_create_nonce( 'policyms_account_user_deletion' ),
						wp_create_nonce( 'policyms_account_user_retry_verification' ),
						wp_create_nonce( 'policyms_account_user_data_request' )
					);

				} else {
					print notice_html(
						'You need to be logged in to view accounts.',
						'notice'
					);
				}
			}
		);
	}

	public static function account_user_egi_redirection() {
		self::exception_handler(
			function () {
				if ( isset( $_GET['code'] ) ) {
					$token = PolicyMS_User::authenticate_egi( $_GET['code'] );
					notice_html( "Please wait while you're being redirected...", 'notice' );
					wp_enqueue_script( 'policyms-account-authentication' );
					wp_localize_script(
						'policyms-account-authentication',
						'AccountAuthenticationProperties',
						array(
							'EGISuccessRedirect' => self::get_setting( true, 'account_page' ),
							'EGISuccessToken'    => $token,
						)
					);
				} else {
					notice_html( 'An EGI code was not found.' );
				}
			}
		);
	}

	/**
	 * Handle user registration AJAX requests.
	 *
	 * @uses    PolicyMS_Public::account_registration()
	 *
	 * @since   1.0.0
	 * @author  Alexandros Raikos <alexandros@araikos.gr>
	 */
	public function account_user_registration_handler() {
		$this->ajax_handler(
			function ( $data ) {

				return PolicyMS_User::register(
					array(
						'password'         => stripslashes( $data['password'] ),
						'password-confirm' => stripslashes( $data['password-confirm'] ),
						'name'             => filter_var( stripslashes( $data['name'] ), FILTER_SANITIZE_STRING ),
						'surname'          => filter_var( stripslashes( $data['surname'] ), FILTER_SANITIZE_STRING ),
						'title'            => filter_var( $data['title'] ?? '', FILTER_SANITIZE_STRING ),
						'gender'           => filter_var( $data['gender'] ?? '', FILTER_SANITIZE_STRING ),
						'organization'     => filter_var( stripslashes( $data['organization'] ?? '' ), FILTER_SANITIZE_STRING ),
						'email'            => filter_var( $data['email'], FILTER_SANITIZE_EMAIL ),
						'phone'            => filter_var( $data['phone'] ?? '', FILTER_SANITIZE_NUMBER_INT ),
						'socials-title'    => array_map(
							function ( $title ) {
								return filter_var( stripslashes( $title ), FILTER_SANITIZE_STRING );
							},
							$data['socials-title'] ?? array()
						),
						'socials-url'      => array_map(
							function ( $url ) {
								return filter_var( $url, FILTER_SANITIZE_URL );
							},
							$data['socials-url'] ?? array()
						),
						'about'            => $data['about'] ?? '',
					)
				);
			}
		);
	}

	/**
	 * Handle user login AJAX requests.
	 *
	 * @uses    PolicyMS_Public::account_authentication)
	 *
	 * @since   1.0.0
	 * @author  Alexandros Raikos <alexandros@araikos.gr>
	 */
	public function account_user_authentication_handler() {
		$this->ajax_handler(
			function ( $data ) {

				return PolicyMS_User::authenticate(
					$data['email'],
					$data['password']
				);
			}
		);
	}

	public function account_user_authentication_google_handler() {
		$this->ajax_handler(
			function ( $data ) {
				return PolicyMS_User::authenticate_google(
					$data['google_token']
				);
			}
		);
	}

	public function account_user_registration_google_handler() {
		$this->ajax_handler(
			function ( $data ) {
				return PolicyMS_User::register_google(
					$data['google_token']
				);
			}
		);
	}

	public function account_disconnect_google_handler() {
		$this->ajax_handler(
			function () {
				$user = new PolicyMS_User();
				return $user->disconnect_google();
			}
		);
	}

	public function account_disconnect_keycloak_handler() {
		$this->ajax_handler(
			function () {
				$user = new PolicyMS_User();
				return $user->disconnect_keycloak();
			}
		);
	}

	public function account_disconnect_egi_handler() {
		$this->ajax_handler(
			function () {
				$user = new PolicyMS_User();
				return $user->disconnect_egi();
			}
		);
	}

	public function account_user_registration_keycloak_handler() {
		$this->ajax_handler(
			function ( $data ) {
				return PolicyMS_User::register_keycloak(
					$data['keycloak-username'],
					$data['keycloak-password']
				);
			}
		);
	}

	public function account_user_authentication_keycloak_handler() {
		$this->ajax_handler(
			function ( $data ) {
				return PolicyMS_User::authenticate_keycloak(
					$data['keycloak-username'],
					$data['keycloak-password']
				);
			}
		);
	}

	public function account_user_password_reset_handler() {
		$this->ajax_handler(
			function ( $data ) {

				PolicyMS_User::reset_password(
					$data['email']
				);
			}
		);
	}

	/**
	 * Handle user account editing AJAX requests.
	 *
	 * @uses    PolicyMS_Public::account_registration()
	 *
	 * @since   1.0.0
	 * @author  Alexandros Raikos <alexandros@araikos.gr>
	 */
	public function account_user_editing_handler() {
		$this->ajax_handler(
			function ( $data ) {
				$user = new PolicyMS_User( $data['uid'] ?? null );
				switch ( $data['subsequent_action'] ) {
					case 'edit_account_user':
						$user->update(
							array(
								'password'         => stripslashes( $data['password'] ?? '' ),
								'password-confirm' => stripslashes( $data['password-confirm'] ?? '' ),
								'current-password' => stripslashes( $data['current-password'] ?? '' ),
								'name'             => stripslashes( $data['name'] ),
								'surname'          => stripslashes( $data['surname'] ),
								'title'            => $data['title'] ?? '',
								'gender'           => $data['gender'] ?? '',
								'organization'     => stripslashes( $data['organization'] ?? '' ),
								'email'            => $data['email'],
								'phone'            => $data['phone'] ?? '',
								'socials-title'    => $data['socials-title'] ?? '',
								'socials-url'      => $data['socials-url'] ?? '',
								'about'            => stripslashes( $data['about'] ?? '' ),
								'public-email'     => $data['public-email'],
								'public-phone'     => $data['public-phone'],
							),
							$_FILES['profile_picture']
						);
						break;
					case 'delete_profile_picture':
						$user->delete_picture();
						break;
					default:
						throw new PolicyMSInvalidDataException(
							'No subsequent action was defined.'
						);
						break;
				}
			}
		);
	}

	public function account_user_verification_retry_handler() {
		$this->ajax_handler(
			function () {

				$user = new PolicyMS_User();
				$user->resend_verification_email();
			}
		);
	}

	/**
	 * Handle user account editing AJAX requests.
	 *
	 * @since   1.0.0
	 * @author  Alexandros Raikos <alexandros@araikos.gr>
	 */
	public function account_user_data_request_handler() {
		$this->ajax_handler(
			function ( $data ) {

				$user = new PolicyMS_User();
				$data = $user->get_data_copy();
				return $data;
			}
		);
	}

	/**
	 * Handle user account deletion AJAX requests.
	 *
	 * @since   1.0.0
	 * @author  Alexandros Raikos <alexandros@araikos.gr>
	 */
	public function account_user_deletion_handler() {
		$this->ajax_handler(
			function ( $data ) {
				if ( ! empty( $data['user'] ) ) {
					PolicyMS_User::delete_other( $data['current_password'], $data['user'] );
				} else {
					$user = new PolicyMS_User();
					$user->delete( $data['current_password'] );
				}
			}
		);
	}

	/**
	 *
	 * Content
	 *
	 * This section refers to functionality and shortcodes relevant to content.
	 */

	/**
	 * Register all the shortcodes concerning content handling.
	 *
	 * @since   1.0.0
	 * @author  Alexandros Raikos <alexandros@araikos.gr>
	 */
	public function add_description_shortcodes() {
		add_shortcode( 'policyms-descriptions-featured', 'PolicyMS_Public::descriptions_featured_shortcode' );

		// Read multiple objects sequence.
		add_shortcode( 'policyms-description-archive', 'PolicyMS_Public::descriptions_archive_shortcode' );

		// Read single object sequence.
		add_shortcode( 'policyms-description', 'PolicyMS_Public::description_shortcode' );

		// Create object sequence.
		add_shortcode( 'policyms-description-creation', 'PolicyMS_Public::description_creation_shortcode' );
	}

	/**
	 * Display multiple Description Objects for visitors and authenticated users.
	 *
	 * @since   1.0.0
	 * @author  Alexandros Raikos <alexandros@araikos.gr>
	 */
	public static function descriptions_archive_shortcode() {
		self::exception_handler(
			function () {
				wp_enqueue_script( '.policyms.descriptions.archive' );

				descriptions_archive_html(
					PolicyMS_Description::get_all(),
					PolicyMS_Description::get_filters_range(),
					self::get_setting( true, 'description_page' )
				);
			}
		);
	}

	/**
	 * Display featured descriptions for visitors and authenticated users.
	 *
	 * @since   1.0.0
	 * @author  Alexandros Raikos <alexandros@araikos.gr>
	 */
	public static function descriptions_featured_shortcode() {
		self::exception_handler(
			function () {
				$featured = PolicyMS_Description::get_featured();
				wp_enqueue_script( '.policyms.descriptions.archive' );
				featured_descriptions_html(
					$featured
				);
			}
		);
	}

	/**
	 * Display the description creation form for authenticated users.
	 *
	 * @since   1.0.0
	 * @author  Alexandros Raikos <alexandros@araikos.gr>
	 */
	public static function description_creation_shortcode() {
		self::exception_handler(
			function () {
				if ( PolicyMS_User::is_authenticated() ) {
					wp_enqueue_script( 'policyms-description-creation' );
					wp_localize_script(
						'policyms-description-creation',
						'DescriptionCreationProperties',
						array(
							'nonce'           => wp_create_nonce( 'policyms_description_creation' ),
							'descriptionPage' => self::get_setting( true, 'description_page' ),
						)
					);

					description_editor_html();
				} else {
					notice_html( 'You need to be logged in to create a description.' );
				}
			}
		);
	}

	/**
	 * Display a single description object for authenticated users.
	 *
	 * @since   1.0.0
	 * @author  Alexandros Raikos <alexandros@araikos.gr>
	 */
	public static function description_shortcode() {
		self::exception_handler(
			function () {
				if ( empty( $_GET['did'] ) ) {
					throw new PolicyMSInvalidDataException(
						'Please specify the ID of the description.'
					);
				}
				$description = new PolicyMS_Description( $_GET['did'] );
				$permissions = array(
					'authenticated' => PolicyMS_User::is_authenticated(),
					'provider'      => false,
					'administrator' => false,
				);

				if ( $permissions['authenticated'] ) {
					$user = new PolicyMS_User();

					if ( $user->is_verified() || $description->is_provider( $user ) ) {
						$permissions['provider']      = $description->is_provider( $user );
						$permissions['administrator'] = $user->is_admin();

						$image_blobs = array_map(
							function ( $image ) {
								return $image->pull();
							},
							array_filter(
								$description->assets ?? array(),
								function ( $category ) {
									return $category == 'images';
								},
								ARRAY_FILTER_USE_KEY
							)['images'] ?? array()
						);

						$reviews = $description->get_reviews( filter_var( $_GET['reviews-page'] ?? 1, FILTER_VALIDATE_INT ) );
					} else {
						$permissions['authenticated'] = false;
						notice_html( 'You need to verify your email address to be able to view description details.', 'notice' );
					}
				}

				wp_enqueue_script( 'policyms-description-creation' );
				wp_enqueue_script( 'policyms-description' );
				wp_localize_script(
					'policyms-description',
					'DescriptionEditingProperties',
					array(
						'nonce'                   => wp_create_nonce( 'policyms_description_editing' ),
						'descriptionID'           => $description->id,
						'assetDownloadNonce'      => $permissions['authenticated'] ? wp_create_nonce( 'policyms_asset_download' ) : null,
						'assetDeleteNonce'        => $permissions['authenticated'] ? wp_create_nonce( 'policyms_asset_delete' ) : null,
						'setDefaultImageNonce'    => $permissions['authenticated'] ? wp_create_nonce( 'policyms_set_description_image' ) : null,
						'removeDefaultImageNonce' => $permissions['authenticated'] ? wp_create_nonce( 'policyms_remove_description_image' ) : null,
						'reviewsNonce'            => $permissions['authenticated'] ? wp_create_nonce( 'policyms_get_description_reviews' ) : null,
						'createReviewNonce'       => $permissions['authenticated'] ? wp_create_nonce( 'policyms_create_review' ) : null,
						'deleteReviewNonce'       => $permissions['authenticated'] ? wp_create_nonce( 'policyms_delete_review' ) : null,
						'approvalNonce'           => $permissions['administrator'] ? wp_create_nonce( 'policyms_description_approval' ) : null,
						'deletionNonce'           => ( $permissions['administrator'] || $permissions['provider'] ) ? wp_create_nonce( 'policyms_description_deletion' ) : null,
						'deleteRedirect'          => $permissions['provider'] ? ( self::get_setting( true, 'account_page' ) . '#descriptions' ) : ( self::get_setting( true, 'account_page' ) . '#approvals' ),
						'videoURL'                => $permissions['authenticated'] ? self::get_setting( true, 'marketplace_host' ) : '',
					)
				);

				add_filter(
					'wpseo_title',
					function ( $title ) use ( $description ) {
						if ( is_singular( 'product' ) ) {
							$title = $description->information['title'];
						}
						return $title;
					}
				);

				description_html(
					$description,
					$image_blobs ?? null,
					self::get_setting(
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
	 * @uses    PolicyMS_Public::description_editing()
	 *
	 * @since   1.0.0
	 * @author  Alexandros Raikos <alexandros@araikos.gr>
	 */
	public function description_editing_handler() {
		$this->ajax_handler(
			function ( $data ) {
				$description = new PolicyMS_Description( $data['description_id'] );
				try {

					// TODO @alexandrosraikos: Remove 'subtype' entirely. (#128)
					// TODO @alexandrosraikos: Rename 'Fields of Use' to 'Keywords'. (#128)
					$description->update(
						array(
							'title'       => stripslashes( sanitize_text_field( $data['title'] ) ),
							'type'        => sanitize_text_field( $data['type'] ),
							'subtype'     => sanitize_text_field( $data['subtype'] ?? '' ),
							'owner'       => stripslashes( sanitize_text_field( $data['owner'] ?? '' ) ),
							'description' => stripslashes( sanitize_text_field( $data['description'] ) ),
							'links-title' => array_map(
								function ( $title ) {
									return filter_var( stripslashes( $title ), FILTER_SANITIZE_STRING );
								},
								$data['links-title'] ?? array()
							),
							'links-url'   => array_map(
								function ( $url ) {
									return filter_var( $url, FILTER_SANITIZE_URL );
								},
								$data['links-url'] ?? array()
							),
							'fieldOfUse'  => explode( ', ', $data['fields-of-use'] ?? '' ),
							'comments'    => stripslashes( sanitize_text_field( $data['comments'] ?? '' ) ),
						),
						array_filter(
							array_keys( $_FILES ),
							function ( $key ) {
								return ( substr( $key, 0, 5 ) === 'image' ||
									substr( $key, 0, 5 ) === 'video' ||
									substr( $key, 0, 4 ) === 'file' );
							}
						)
					);
				} catch ( PolicyMSAPIError $e ) {
					if ( $e->http_status === 406 ) {
						http_response_code( 200 );
						die();
					} else {
						throw $e;
					}
				}
			}
		);
	}

	/**
	 * Handle description approval AJAX requests.
	 *
	 * @uses    PolicyMS_Public::description_approval()
	 *
	 * @since   1.0.0
	 * @author  Alexandros Raikos <alexandros@araikos.gr>
	 */
	function description_approval_handler() {
		$this->ajax_handler(
			function ( $data ) {

				$user = new PolicyMS_User();
				if ( $user->is_admin() ) {
					$description = new PolicyMS_Description( $data['description_id'] );
					$description->approve( $data['approval'] );
				}
			}
		);
	}

	/**
	 * Handle description creation AJAX requests.
	 *
	 * @uses    PolicyMS_Public::description_creation()
	 *
	 * @since   1.0.0
	 * @author  Alexandros Raikos <alexandros@araikos.gr>
	 */
	public function description_creation_handler() {
		$this->ajax_handler(
			function ( $data ) {
				// TODO @alexandrosraikos: Handle file upload section after successful creation. (#133)
				if ( ! empty( $data['subtype'] ) ) {
					if ( strlen( $data['subtype'] ) > 25 ) {
						throw new PolicyMSInvalidDataException(
							'The secondary collection type label exceeds the 25 character limit.'
						);
					}
				}

				return ( PolicyMS_Description::create(
					array(
						'title'       => stripslashes( sanitize_text_field( $data['title'] ) ),
						'type'        => sanitize_text_field( $data['type'] ),
						'subtype'     => strtolower( sanitize_text_field( $data['subtype'] ?? '' ) ),
						'owner'       => stripslashes( sanitize_text_field( $data['owner'] ?? '' ) ),
						'description' => stripslashes( sanitize_text_field( $data['description'] ) ),
						'links'       => PolicyMS_User::implode_urls(
							array_map(
								function ( $title ) {
									return filter_var( stripslashes( $title ), FILTER_SANITIZE_STRING );
								},
								$data['links-title'] ?? array()
							),
							array_map(
								function ( $url ) {
									return filter_var( $url, FILTER_SANITIZE_URL );
								},
								$data['links-url'] ?? array()
							)
						),
						'fieldOfUse'  => explode( ', ', $data['fields-of-use'] ?? array() ),
						'comments'    => stripslashes( sanitize_text_field( $data['comments'] ?? '' ) ),
					)
				) );
			}
		);
	}


	/**
	 * Handle description deletion AJAX requests.
	 *
	 * @uses    PolicyMS_Public::description_creation()
	 *
	 * @since   1.0.0
	 * @author  Alexandros Raikos <alexandros@araikos.gr>
	 */
	public function description_deletion_handler() {
		$this->ajax_handler(
			function ( $data ) {
				$description = new PolicyMS_Description( $data['description_id'] );
				$description->delete();
			}
		);
	}

	public function asset_download_handler() {
		$this->ajax_handler(
			function ( $data ) {
				$otc = PolicyMS_Asset::get_download_url(
					$data['category'],
					$data['file_id']
				);
				return array(
					'url' => self::get_setting( true, 'marketplace_host' ) . '/assets/download/' . $otc . ( ( $data['download'] == 'true' ) ? '' : '?na=not' ),
				);
			}
		);
	}

	public function asset_deletion_handler() {
		$this->ajax_handler(
			function ( $data ) {
				PolicyMS_Asset::delete( $data['asset_category'], $data['asset_id'] );
			}
		);
	}

	public function get_description_reviews_handler() {
		$this->ajax_handler(
			function ( $data ) {
				$description = new PolicyMS_Description( $data['description_id'] );
				$reviews     = $description->get_reviews( $data['page'] );
				ob_start();
				description_reviews_list_html( $reviews['content'], $description->user_review->user_id ?? null );
				$html_response = ob_get_contents();
				ob_end_clean();
				return $html_response;
			}
		);
	}

	public function create_review_handler() {
		$this->ajax_handler(
			function ( $data ) {
				if ( $data['update'] == 'on' ) {
					PolicyMS_Review::update(
						$data['description_id'],
						$data['rating'],
						stripslashes( $data['comment'] ),
						$data['update']
					);
				} else {
					PolicyMS_Review::create(
						$data['description_id'],
						$data['rating'],
						stripslashes( $data['comment'] ),
						$data['update']
					);
				}
			}
		);
	}

	public function delete_review_handler() {
		$this->ajax_handler(
			function ( $data ) {
				PolicyMS_Review::delete( $data['description_id'], $data['author_id'] );
			}
		);
	}

	public function set_description_image_handler() {
		$this->ajax_handler(
			function ( $data ) {
				PolicyMS_Description::set_default_image(
					$data['description_id'],
					$data['image_id']
				);
			}
		);
	}

	public function remove_description_image_handler() {
		$this->ajax_handler(
			function ( $data ) {
				PolicyMS_Description::remove_default_image(
					$data['description_id']
				);
			}
		);
	}
}
