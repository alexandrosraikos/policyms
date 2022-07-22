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
		wp_localize_script(
			'policyms',
			'GlobalProperties',
			array(
				'rootURLPath' => ( empty( wp_parse_url( get_site_url() )['path'] )
					? '/'
					: wp_parse_url( get_site_url() )['path'] ),
				'loginPage'   => self::get_setting( true, 'login_page' ),
				'ajaxURL'     => admin_url( 'admin-ajax.php' ),
			)
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
			die( 'The required fields were not specified.' );
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
			die( ! empty( $data ) ? wp_json_encode( $data ) : null );
		} catch ( PolicyMSUnauthorizedRequestException $e ) {
			http_response_code( 401 );
			die( esc_html( $e->getMessage() ) );
		} catch ( PolicyMSInvalidDataException $e ) {
			http_response_code( 400 );
			die( esc_html( $e->getMessage() ) );
		} catch ( PolicyMSMissingOptionsException $e ) {
			http_response_code( 404 );
			die( esc_html( $e->getMessage() ) );
		} catch ( PolicyMSAPIError $e ) {
			http_response_code( 500 );
			die( $e->getMessage() );
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

		return 1 === count( $settings ) ? $settings[ $id ] : $settings;
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
				'archive_page'
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
				$options['account_page'],
				$options['archive_page']
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
				wp_enqueue_script('policyms-account-authentication');
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

	/**
	 * Get the user profile tab content HTML.
	 *
	 * @param PolicyMS_User $user The user.
	 * @param string        $selected_tab The selected tab identifier ID.
	 * @param bool          $visitor Whether the requester is a visitor.
	 * @param string        $description_page The description page URL.
	 * @param string        $description_archive_page The description page archive URL.
	 * @param string        $description_creation_url The description creation URL.
	 * @return string The tab content HTML.
	 *
	 * @since 2.0.0
	 */
	public static function get_user_tab_content(
		PolicyMS_User $user,
		string $selected_tab,
		bool $visitor,
		string $description_page,
		string $description_archive_page,
		string $description_creation_url
	): string {
		if ( ! array_key_exists( $selected_tab, PolicyMS_User::$default_tabs ) ) {
			return notice_html(
				'The requested content is not available.'
			);
		}
		switch ( $selected_tab ) {
			case 'overview':
				return user_overview_html(
					$user->information,
					$user->__get('statistics')
				);
			case 'descriptions':
				// TODO @alexandrosraikos: Parse description list parameters.
				return user_descriptions_list_html(
					$user->__get('descriptions'),
					$visitor,
					$user->is_admin(),
					$description_page,
					$description_archive_page,
					$description_creation_url
				);
			case 'reviews':
				// TODO @alexandrosraikos: Parse review list parameters.
				return user_reviews_list_html(
					$user->__get('reviews'),
					$visitor,
					$description_page
				);
			case 'approvals':
				// TODO @alexandrosraikos: Parse review list parameters.
				return user_approvals_list_html(
					PolicyMS_Description_Collection::get_pending(),
					$description_page,
					$description_archive_page
				);
			case 'profile':
				return user_profile_details_html(
					$user,
					$visitor,
					$user->is_admin(),
					$user->__get('picture'),
					new PolicyMS_OAuth_Controller( $user ),
					wp_create_nonce( 'policyms_account_user_edit' ),
					wp_create_nonce( 'policyms_account_user_deletion' ),
					wp_create_nonce( 'policyms_account_user_retry_verification' ),
					wp_create_nonce( 'policyms_account_user_data_request' )
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
						$user === $self && $visitor,
						$urls['account_page'],
						self::get_user_tab_content(
							$user,
							$selected_tab,
							$visitor,
							$urls['description_page'],
							$urls['archive_page'],
							$selected_tab === 'descriptions' ? $urls['upload_page'] : ''
						),
						$selected_tab,
						wp_create_nonce( 'policyms_account_user_switch_tab' ),
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

	/**
	 * Print the EGI redirection handling shortcode.
	 *
	 * @since 1.4.0
	 */
	public static function account_user_egi_redirection() {
		self::exception_handler(
			function () {
				// NOTE: No need to escape this self-created HTML output.
				print PolicyMS_OAuth_Controller::get_egi_redirection_shortcode();
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
						'password'         => wp_unslash( $data['password'] ),
						'password-confirm' => wp_unslash( $data['password-confirm'] ),
						'name'             => sanitize_text_field( wp_unslash( $data['name'] ) ),
						'surname'          => sanitize_text_field( wp_unslash( $data['surname'] ) ),
						'title'            => sanitize_text_field( wp_unslash( $data['title'] ?? '' ) ),
						'gender'           => sanitize_text_field( wp_unslash( $data['gender'] ?? '' ) ),
						'organization'     => sanitize_text_field( wp_unslash( $data['organization'] ?? '' ) ),
						'email'            => sanitize_email( wp_unslash( $data['email'] ) ),
						'phone'            => sanitize_text_field( $data['phone'] ?? '' ),
						'links-title'      => array_map(
							function ( $title ) {
								return sanitize_text_field( wp_unslash( $title ) );
							},
							$data['links-title'] ?? array()
						),
						'links-url'        => array_map(
							function ( $url ) {
								return esc_url_raw( $url );
							},
							$data['links-url'] ?? array()
						),
						'about'            => sanitize_textarea_field( wp_unslash( $data['about'] ?? '' ) ),
					)
				);
			}
		);
	}

	/**
	 * Handle user login AJAX requests.
	 *
	 * @since   1.0.0
	 */
	public function account_user_authentication_handler() {
		$this->ajax_handler(
			function ( $data ) {
				return PolicyMS_User::authenticate(
					sanitize_email( $data['email'] ),
					wp_unslash( $data['password'] )
				);
			}
		);
	}

	/**
	 * Handles authentication with Google SSO.
	 *
	 * @since   1.2.0
	 */
	public function account_user_authentication_google_handler() {
		$this->ajax_handler(
			function ( $data ) {
				return PolicyMS_User::authenticate_google(
					sanitize_text_field( $data['google_token'] )
				);
			}
		);
	}

	/**
	 * Handles registration with Google SSO.
	 *
	 * @since   1.2.0
	 */
	public function account_user_registration_google_handler() {
		$this->ajax_handler(
			function ( $data ) {
				return PolicyMS_User::register_google(
					sanitize_text_field( $data['google_token'] )
				);
			}
		);
	}

	/**
	 * Handles registration with KeyCloak SSO.
	 *
	 * @since   1.2.0
	 */
	public function account_user_registration_keycloak_handler() {
		$this->ajax_handler(
			function ( $data ) {
				return PolicyMS_User::register_keycloak(
					sanitize_user( $data['keycloak-username'] ),
					wp_unslash( $data['keycloak-password'] )
				);
			}
		);
	}


	/**
	 * Handles authentication with KeyCloak SSO.
	 *
	 * @since   1.2.0
	 */
	public function account_user_authentication_keycloak_handler() {
		$this->ajax_handler(
			function ( $data ) {
				return PolicyMS_User::authenticate_keycloak(
					sanitize_user( $data['keycloak-username'] ),
					wp_unslash( $data['keycloak-password'] )
				);
			}
		);
	}

	/**
	 * Handles Google OAuth credential disconnect.
	 *
	 * @since   1.2.0
	 */
	public function account_disconnect_google_handler() {
		$this->ajax_handler(
			function () {
				$user = new PolicyMS_User();
				return $user->disconnect_google();
			}
		);
	}

	/**
	 * Handles KeyCloak OAuth credential disconnect.
	 *
	 * @since   1.2.0
	 */
	public function account_disconnect_keycloak_handler() {
		$this->ajax_handler(
			function () {
				$user = new PolicyMS_User();
				return $user->disconnect_keycloak();
			}
		);
	}

	/**
	 * Handles EGI Check-In OAuth credential disconnect.
	 *
	 * @since   1.4.0
	 */
	public function account_disconnect_egi_handler() {
		$this->ajax_handler(
			function () {
				$user = new PolicyMS_User();
				return $user->disconnect_egi();
			}
		);
	}

	/**
	 * Handle new tab content request calls.
	 *
	 * @since 2.0.0
	 */
	public function account_user_switch_tab_handler() {
		$this->ajax_handler(
			function ( $data ) {
				return self::get_user_tab_content(
					new PolicyMS_User(
						('true' === $data['is_visitor']) ? sanitize_user( $data['user_id'] ) : null
					),
					sanitize_key( $data['tab_identifier'] ),
					('true' === $data['is_visitor']),
					self::get_setting( true, 'description_page' ),
					self::get_setting( true, 'archive_page' ),
					self::get_setting( true, 'upload_page' )
				);
			}
		);
	}

	/**
	 * Handle a password reset request.
	 *
	 * @since 1.2.0
	 */
	public function account_user_password_reset_handler() {
		$this->ajax_handler(
			function ( $data ) {
				PolicyMS_User::reset_password(
					sanitize_email( $data['email'] )
				);
			}
		);
	}

	/**
	 * Handle user account editing AJAX requests.
	 *
	 * @since   1.0.0
	 */
	public function account_user_editing_handler() {
		$this->ajax_handler(
			function ( $data ) {
				$user = new PolicyMS_User( $data['uid'] ?? null );
				switch ( $data['subsequent_action'] ) {
					case 'edit_account_user':
						$picture = null;
						if ( ! empty( $_FILES['profile_picture'] ) ) {
							$picture = array(
								'error'    => (int) $_FILES['profile_picture']['error'] ?? 4,
								'size'     => (int) $_FILES['profile_picture']['size'] ?? 0,
								'tmp_name' => sanitize_text_field( wp_unslash( $_FILES['profile_picture']['tmp_name'] ?? '' ) ),
								'type'     => sanitize_text_field( wp_unslash( $_FILES['profile_picture']['type'] ?? '' ) ),
							);
						}
						$user->update(
							array(
								'password'         => wp_unslash( $data['password'] ?? '' ),
								'password-confirm' => wp_unslash( $data['password-confirm'] ?? '' ),
								'current-password' => wp_unslash( $data['current-password'] ?? '' ),
								'name'             => sanitize_text_field( wp_unslash( $data['name'] ) ),
								'surname'          => sanitize_text_field( wp_unslash( $data['surname'] ) ),
								'title'            => sanitize_key( $data['title'] ?? '' ),
								'gender'           => sanitize_key( $data['gender'] ?? '' ),
								'organization'     => sanitize_text_field( wp_unslash( $data['organization'] ?? '' ) ),
								'email'            => sanitize_email( $data['email'] ),
								'phone'            => sanitize_text_field( $data['phone'] ?? '' ),
								'links-title'      => array_map(
									fn( $title) => sanitize_text_field( $title ),
									$data['links-title'] ?? ''
								),
								'links-url'        => array_map(
									fn( $url) => sanitize_text_field( $url ),
									$data['links-url'] ?? ''
								),
								'about'            => sanitize_textarea_field( wp_unslash( $data['about'] ?? '' ) ),
								'public-email'     => sanitize_key( $data['public-email'] ),
								'public-phone'     => sanitize_key( $data['public-phone'] ),
							),
							$picture
						);
						break;
					case 'delete_profile_picture':
						$user->delete_picture();
						break;
					default:
						throw new PolicyMSInvalidDataException(
							'No subsequent action was defined.'
						);
				}
			}
		);
	}

	/**
	 * Handle user verification email resending.
	 *
	 * @since   1.2.0
	 */
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
	 */
	public function account_user_data_request_handler() {
		$this->ajax_handler(
			function () {
				$user = new PolicyMS_User();
				return $user->get_data_copy();
			}
		);
	}

	/**
	 * Handle user account deletion AJAX requests.
	 *
	 * @since   1.0.0
	 */
	public function account_user_deletion_handler() {
		$this->ajax_handler(
			function ( $data ) {
				$data['current_password'] = wp_unslash( $data['current_password'] );
				if ( ! empty( $data['user'] ) ) {
					PolicyMS_User::delete_other(
						$data['current_password'],
						sanitize_user( $data['user'] )
					);
				} else {
					$user = new PolicyMS_User();
					$user->delete( $data['current_password'] );
				}
			}
		);
	}

	/**
	 * Register all the shortcodes concerning content handling.
	 *
	 * @since   1.0.0
	 */
	public function add_description_shortcodes() {
		add_shortcode( 'policyms-descriptions-featured', 'PolicyMS_Public::descriptions_featured_shortcode' );
		add_shortcode( 'policyms-description-archive', 'PolicyMS_Public::descriptions_archive_shortcode' );
		add_shortcode( 'policyms-description', 'PolicyMS_Public::description_shortcode' );
		add_shortcode( 'policyms-description-creation', 'PolicyMS_Public::description_creation_shortcode' );
	}

	/**
	 * Display multiple Description Objects for visitors and authenticated users.
	 *
	 * @since   1.0.0
	 */
	public static function descriptions_archive_shortcode() {
		self::exception_handler(
			function () {
				wp_enqueue_script( 'policyms-descriptions-archive' );
				$query     = sanitize_text_field( wp_unslash( $_GET['search'] ?? '' ) );
				$category  = sanitize_key( $_GET['type'] ?? '' );
				$views_gte = !empty($_GET['views-gte']) ? (int)$_GET['views-gte'] : null;
				$views_lte = !empty($_GET['views-lte']) ? (int)$_GET['views-lte'] : null;
				$date_gte  = ! empty( $_GET['update-date-gte'] )
					? sanitize_text_field( wp_unslash( $_GET['update-date-gte'] ) )
					: null;
				$date_lte  = ! empty( $_GET['update-date-lte'] )
					? sanitize_text_field( wp_unslash( $_GET['update-date-lte'] ) )
					: null;

				// NOTE: No need to escape this self-created HTML output.
				print descriptions_archive_html(
					PolicyMS_Description_Filters::get_defaults(),
					new PolicyMS_Description_Filters(
						$query,
						$category,
						$views_gte,
						$views_lte,
						$date_gte,
						$date_lte
					),
					wp_create_nonce( 'policyms_description_filtering_nonce' ),
					PolicyMS_Description_Collection::get_all()
				);
			}
		);
	}

	/**
	 * Display featured descriptions for visitors and authenticated users.
	 *
	 * @since   1.0.0
	 */
	public static function descriptions_featured_shortcode() {
		self::exception_handler(
			function () {
				wp_enqueue_script( 'policyms-descriptions-archive' );

				// NOTE: No need to escape this self-created HTML output.
				print featured_descriptions_html(
					PolicyMS_Description_Collection::get_featured()
				);
			}
		);
	}

	/**
	 * Display the description creation form for authenticated users.
	 *
	 * @since   1.0.0
	 */
	public static function description_creation_shortcode() {
		self::exception_handler(
			function () {
				if ( PolicyMS_User::is_authenticated() ) {
					wp_enqueue_script( 'policyms-description-creation' );

					// NOTE: No need to escape this self-created HTML output.
					print description_editor_html(
						null,
						self::get_setting( true, 'description_page' ),
						'',
						wp_create_nonce( 'policyms_description_creation' ),
					);
				} else {
					print notice_html( 'You need to be logged in to create a description.' );
				}
			}
		);
	}

	/**
	 * Display a single description object for authenticated users.
	 *
	 * @since   1.0.0
	 */
	public static function description_shortcode() {
		self::exception_handler(
			function () {
				if ( empty( $_GET['did'] ) ) {
					throw new PolicyMSInvalidDataException(
						'Please specify the ID of the description.'
					);
				}

				// Get the description.
				$description = new PolicyMS_Description(
					sanitize_text_field( wp_unslash( $_GET['did'] ) )
				);

				// Initialize the user permissions object.
				$authenticated = PolicyMS_User::is_authenticated();

				if ( $authenticated ) {
					$user = new PolicyMS_User();

					if ( $user->is_verified() || $description->is_provider( $user ) ) {
						$image_blobs = array_map(
							function ( $image ) {
								return $image->pull();
							},
							array_filter(
								$description->assets ?? array(),
								function ( $category ) {
									return 'images' === $category;
								},
								ARRAY_FILTER_USE_KEY
							)['images'] ?? array()
						);

						$reviews = $description->get_reviews(
							intval( $_GET['reviews-page'] ?? 1 )
						);
					} else {
						$authenticated = false;
						notice_html( 'You need to verify your email address to be able to view description details.', 'notice' );
					}
				}

				wp_enqueue_script( 'policyms-description' );

				$urls = self::get_setting(
					true,
					'login_page',
					'account_page',
					'archive_page',
					'marketplace_host'
				);

				$administrator = (isset($user)) ? $user->is_admin(): false;
				$provider = (isset($user)) ? $description->is_provider( $user ) :false;

				// NOTE: No need to escape this self-created HTML output.
				print description_html(
					$description,
					$urls['account_page'],
					$urls['archive_page'],
					$urls['login_page'],
					$authenticated,
					$administrator,
					$provider,
					$reviews ?? array(),
					$image_blobs ?? array(),
					$authenticated
						? wp_create_nonce( 'policyms_asset_download' ) : '',
					$authenticated
						? wp_create_nonce( 'policyms_get_description_reviews' ) : '',
					$authenticated
						? wp_create_nonce( 'policyms_create_review' ) : '',
					$authenticated
						? wp_create_nonce( 'policyms_delete_review' ) : '',
						$administrator
						? wp_create_nonce( 'policyms_description_approval' ) : '',
					$provider || $administrator
						? wp_create_nonce( 'policyms_description_editing' )
						: '',
					$provider || $administrator
						? wp_create_nonce( 'policyms_set_description_image' )
						: '',
					$provider || $administrator
						? wp_create_nonce( 'policyms_remove_description_image' )
						: '',
					$provider || $administrator
						? wp_create_nonce( 'policyms_asset_delete' )
						: '',
					$provider || $administrator
						? wp_create_nonce( 'policyms_description_deletion' )
						: '',
					$provider
						? $urls['account_page'] . '#descriptions'
						: $urls['account_page'] . '#approvals',
					$urls['marketplace_host']
				);
			}
		);
	}

	/**
	 * Handle description editing AJAX requests.
	 *
	 * @since   1.0.0
	 */
	public function description_editing_handler() {
		$this->ajax_handler(
			function ( $data ) {
				$description = new PolicyMS_Description( $data['description-id'] );
				try {
					$description->update(
						array(
							'title'       => sanitize_text_field(
								wp_unslash( $data['title'] )
							),
							'type'        => sanitize_text_field(
								wp_unslash( $data['type'] )
							),
							'owner'       => sanitize_text_field(
								wp_unslash( $data['owner'] ?? '' )
							),
							'description' => sanitize_text_field(
								wp_unslash( $data['description'] )
							),
							'links-title' => array_map(
								function ( $title ) {
									return sanitize_text_field( wp_unslash( $title ) );
								},
								$data['links-title'] ?? array()
							),
							'links-url'   => array_map(
								function ( $url ) {
									return esc_url_raw( $url );
								},
								$data['links-url'] ?? array()
							),
							'keywords'    => explode(
								', ',
								sanitize_text_field(
									wp_unslash( $data['keywords'] ?? '' )
								)
							),
							'comments'    => sanitize_text_field(
								wp_unslash( $data['comments'] ?? '' )
							),
						),
						array_filter(
							array_keys( $_FILES ),
							function ( $key ) {
								return (
									substr( $key, 0, 5 ) === 'image' ||
									substr( $key, 0, 5 ) === 'video' ||
									substr( $key, 0, 4 ) === 'file'
								);
							}
						)
					);
				} catch ( PolicyMSAPIError $e ) {
					if ( 406 === $e->http_status ) {
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
	 * @since   1.0.0
	 */
	public function description_approval_handler() {
		$this->ajax_handler(
			function ( $data ) {
				$user = new PolicyMS_User();
				if ( $user->is_admin() ) {
					$description = new PolicyMS_Description( $data['description_id'] );
					if ( 'approve' === $data['decision'] ) {
						$description->approve( $data['approval'] );
					} else {
						$description->reject( $data['reason'] );
					}
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
				// TODO @alexandrosraikos: Handle file upload section after successful creation (#133).
				return ( PolicyMS_Description::create(
					array(
						'title'       => sanitize_text_field( wp_unslash( $data['title'] ) ),
						'type'        => sanitize_key( wp_unslash( $data['type'] ) ),
						'owner'       => sanitize_text_field( wp_unslash( $data['owner'] ?? '' ) ),
						'description' => sanitize_text_field( wp_unslash( $data['description'] ) ),
						'links'       => PolicyMS_User::implode_urls(
							array_map(
								function ( $title ) {
									return sanitize_text_field( wp_unslash( $title ) );
								},
								$data['links-title'] ?? array()
							),
							array_map(
								function ( $url ) {
									return esc_url_raw( $url );
								},
								$data['links-url'] ?? array()
							)
						),
						'keywords'    => explode(
							', ',
							$data['keywords'] ?? array()
						),
						'comments'    => sanitize_textarea_field(
							wp_unslash( $data['comments'] ?? '' )
						),
					)
				) );
			}
		);
	}


	/**
	 * Handle description deletion AJAX requests.
	 *
	 * @since   1.0.0
	 */
	public function description_deletion_handler() {
		$this->ajax_handler(
			function ( $data ) {
				$description = new PolicyMS_Description( $data['description_id'] );
				$description->delete();
			}
		);
	}

	/**
	 * Forward a temporary download URL to the client.
	 *
	 * @since 1.3.0
	 */
	public function asset_download_handler() {
		$this->ajax_handler(
			function ( $data ) {
				$otc = PolicyMS_Asset::get_download_url(
					sanitize_key( wp_unslash( $data['category'] ) ),
					sanitize_text_field( wp_unslash( $data['file_id'] ) )
				);
				return array(
					'url' => self::get_setting( true, 'marketplace_host' ) . '/assets/download/' . $otc . ( ( 'true' === $data['download'] ) ? '' : '?na=not' ),
				);
			}
		);
	}

	/**
	 * Handle asset deletion AJAX requests.
	 *
	 * @since 1.3.0
	 */
	public function asset_deletion_handler() {
		$this->ajax_handler(
			function ( $data ) {
				PolicyMS_Asset::delete(
					sanitize_key( wp_unslash( $data['asset_category'] ) ),
					sanitize_text_field( wp_unslash( $data['asset_id'] ) )
				);
			}
		);
	}

	/**
	 * Handle description reviews retrieval AJAX requests.
	 *
	 * @since 1.3.0
	 */
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

	/**
	 * Handle review creation AJAX requests.
	 *
	 * @since 1.3.0
	 */
	public function create_review_handler() {
		$this->ajax_handler(
			function ( $data ) {
				if ( 'true' === $data['update'] ) {
					PolicyMS_Review::update(
						sanitize_text_field( wp_unslash( $data['description_id'] ) ),
						(int) $data['rating'],
						sanitize_textarea_field( wp_unslash( $data['comment'] ) ),
						sanitize_key( wp_unslash( $data['update'] ) )
					);
				} else {
					PolicyMS_Review::create(
						sanitize_text_field( wp_unslash( $data['description_id'] ) ),
						(int) $data['rating'],
						sanitize_textarea_field( wp_unslash( $data['comment'] ) ),
						sanitize_key( wp_unslash( $data['update'] ) )
					);
				}
			}
		);
	}

	/**
	 * Handle review deletion AJAX requests.
	 *
	 * @since 1.3.0
	 */
	public function delete_review_handler() {
		$this->ajax_handler(
			function ( $data ) {
				PolicyMS_Review::delete(
					sanitize_text_field( wp_unslash( $data['description_id'] ) ),
					sanitize_text_field( wp_unslash( $data['author_id'] ) )
				);
			}
		);
	}

	/**
	 * Handle image cover configuration AJAX requests
	 *
	 * @since 1.3.0
	 */
	public function set_description_image_handler() {
		$this->ajax_handler(
			function ( $data ) {
				PolicyMS_Description::set_default_image(
					sanitize_text_field( wp_unslash( $data['description_id'] ) ),
					sanitize_text_field( wp_unslash( $data['image_id'] ) )
				);
			}
		);
	}


	/**
	 * Handle image cover removal AJAX requests
	 *
	 * @since 1.3.0
	 */
	public function remove_description_image_handler() {
		$this->ajax_handler(
			function ( $data ) {
				PolicyMS_Description::remove_default_image(
					sanitize_text_field( wp_unslash( $data['description_id'] ) )
				);
			}
		);
	}
}
