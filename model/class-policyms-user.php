<?php
/**
 * The class definition for users.
 *
 * @link       https://github.com/alexandrosraikos/policyms/
 * @since      1.1.0
 *
 * @package    PolicyMS
 * @subpackage PolicyMS/model
 */

/**
 * The class definition for users.
 *
 * Defines basic authentication properties and functionality,
 * as well as some basic properties.
 *
 * @package    PolicyMS
 * @subpackage PolicyMS/model
 * @author     Alexandros Raikos <alexandros@araikos.gr>
 */
class PolicyMS_User extends PolicyMS_Account {

	/**
	 * The user's information.
	 *
	 * @var array $information The information.
	 *
	 * @since      1.1.0
	 */
	public array $information;

	/**
	 * The user's account metadata.
	 *
	 * @var array $metadata The metadata.
	 *
	 * @since      1.1.0
	 */
	public array $metadata;

	/**
	 * The user's preferences.
	 *
	 * @var array $preferences The array of preferences.
	 *
	 * @since      1.1.0
	 */
	public array $preferences;

	/**
	 * The user's account statistics.
	 *
	 * @var array $statistics The statistics.
	 *
	 * @since      1.1.0
	 */
	public ?array $statistics;

	/**
	 * The user's description collection.
	 *
	 * @var ?PolicyMS_Description_Collection $descriptions The description collection.
	 *
	 * @since      1.1.0
	 */
	public ?PolicyMS_Description_Collection $descriptions;

	/**
	 * The user's review collection.
	 *
	 * @var array $reviews The review collection.
	 *
	 * @since      1.1.0
	 */
	public ?array $reviews;

	/**
	 * The user's picture thumbnail.
	 *
	 * @var ?string $picture The thumbnail in encoded base64.
	 *
	 * @since      1.2.0
	 */
	public ?string $picture;


	/**
	 * The default titles for user names.
	 *
	 * @var array $titles The default titles.
	 *
	 * @since 2.0.0
	 */
	public static $titles = array(
		'Mr.'   => 'Mr.',
		'Ms.'   => 'Ms.',
		'Mrs.'  => 'Mrs.',
		'Dr.'   => 'Dr.',
		'Prof.' => 'Prof.',
		'Sir'   => 'Sir',
		'Miss'  => 'Miss',
		'Mx.'   => 'Mx.',
		'-'     => 'None',
	);

	/**
	 * The default gender options.
	 *
	 * @var array $titles The default gender options.
	 *
	 * @since 2.0.0
	 */
	public static $genders = array(
		'male'        => 'Male',
		'female'      => 'Female',
		'transgender' => 'Transgender',
		'genderqueer' => 'Genderqueer',
		'questioning' => 'Questioning',
		'-'           => 'Prefer not to say',
	);

	/**
	 * The default profile tab options.
	 *
	 * @var array $titles The tab options.
	 *
	 * @since 2.0.0
	 */
	public static array $default_tabs = array(
		'overview'     => 'Overview',
		'descriptions' => 'Descriptions',
		'reviews'      => 'Reviews',
		'approvals'    => 'Approvals',
		'profile'      => 'Profile',
	);

	/**
	 * Initialize a user object.
	 *
	 * @param ?string $uid The user's ID retrieves a user
	 *  different than the one making the request.
	 */
	public function __construct( ?string $uid = null ) {

		// Retrieve self or other user data.
		if ( isset( $uid ) ) {
			$data = $this->get_account_data( $uid );
			parent::__construct( $uid );
		} else {
			$data = $this->get_account_data();
			parent::__construct( $data['uid'] );
		}

		// Assign to properties.
		$this->uid         = $data['uid'];
		$this->information = $data['info'];
		$this->metadata    = $data['account'];
		$this->preferences = $data['profile_parameters'];
	}

	/**
	 * Magic getter for various user-related objects.
	 *
	 * Used to either retrieve objects from present instance,
	 * or retrieve them on demand from the API.
	 *
	 * @param string $variable_name The name of the variable.
	 * @throws PolicyMSInvalidDataException When the name of the variable doesn't exist.
	 *
	 * @since 1.2.0
	 */
	public function __get( string $variable_name ) {
		switch ( $variable_name ) {
			case 'information':
			case 'metadata':
			case 'uid':
			case 'preferences':
				return $this->${$variable_name};
			case 'statistics':
				return $this->statistics ?? $this->get_statistics();
			case 'descriptions':
				return $this->descriptions ?? $this->get_descriptions();
			case 'reviews':
				return $this->reviews ?? $this->get_reviews();
			case 'approvals':
				return PolicyMS_Description_Collection::get_pending();
			case 'picture':
				return $this->picture ?? $this->get_picture();
			default:
				return $this->${$variable_name};
		}
	}

	/**
	 * ------------------------------------------------
	 * Class Methods (Public)
	 * ------------------------------------------------
	 */

	/**
	 * Checks whether the user is an administrator.
	 *
	 * @return bool Whether the user is an administrator.
	 * @since 1.1.0
	 */
	public function is_admin(): bool {
		return 'admin' === $this->get_role();
	}

	/**
	 * Checks whether the user is verified.
	 *
	 * @return bool Whether the user is verified
	 * @since 1.1.0
	 */
	public function is_verified(): bool {
		return '1' === $this->metadata['verified'];
	}

	/**
	 * Retrieve the role of the user.
	 *
	 * @return string The user's role.
	 * @since 1.1.0
	 */
	public function get_role(): string {
		return $this->metadata['role'];
	}

	/**
	 * Check whether the user is password protected.
	 *
	 * Useful when checking for OAuth-only accounts.
	 *
	 * @return bool The password protected state.
	 * @since 2.0.0
	 */
	public function is_password_protected(): bool {
		return '1' === $this->metadata['password_protected'];
	}

	/**
	 * Notify the API to resend a verification email.
	 *
	 * @since 1.2.0
	 */
	public function resend_verification_email(): void {
		PolicyMS_Communication_Controller::api_request(
			'POST',
			'/accounts/users/verification/resend',
			array(
				'email' => $this->information['email'],
			),
			$this->token
		);
	}

	/**
	 * Update the user's information and retrieve a new token.
	 *
	 * @param array  $data The updated information.
	 * @param ?array $picture A new profile picture from $_FILES, if any.
	 * @return ?string The new encrypted access token, if the profile was updated.
	 *
	 * @since 1.1.0
	 */
	public function update( array $data, ?array $picture = null ): ?string {
		// Inspect uploaded information.
		self::inspect( $data );

		// Upload new profile picture.
		if ( isset( $picture ) ) {
			$token = $this->update_picture( $picture );
		}

		// Contact the PolicyMS API for password change.
		if ( ! empty( $data['password'] ) ) {
			$response = PolicyMS_Communication_Controller::api_request(
				'POST',
				'/accounts/users/password/change',
				array(
					'old_password'         => $data['current-password'],
					'new_password'         => $data['password'],
					'confirm_new_password' => $data['password-confirm'],
				),
				$this->token
			);
			if ( ! empty( $response['token'] ) ) {
				$token = $response['token'];
			}
		}

		$response = PolicyMS_Communication_Controller::api_request(
			'PUT',
			'/accounts/users/information/' . $this->id,
			array(
				'info'               => array(
					'name'         => $data['name'],
					'surname'      => $data['surname'],
					'title'        => $data['title'],
					'gender'       => $data['gender'],
					'organization' => $data['organization'],
					'email'        => $data['email'],
					'phone'        => $data['phone'],
					'social'       => $this->implode_urls(
						$data['links-title'],
						$data['links-url']
					),
					'about'        => $data['about'],
				),
				'profile_parameters' => array(
					'public_email' => intval( $data['public-email'] ),
					'public_phone' => intval( $data['public-phone'] ),
				),
			),
			$this->token
		);

		if ( ! empty( $response['token'] ) ) {
			$token = $response['token'];
		}

		// Return encrypted token.
		if ( ! empty( $token ) ) {
			return parent::encrypt_token( $token );
		} else {
			return null;
		}
	}


	/**
	 * Delete the user account.
	 *
	 * @param string $current_password The user's current password.
	 *
	 * @since 1.1.0
	 */
	public function delete( string $current_password ): void {
		PolicyMS_Communication_Controller::api_request(
			'DELETE',
			'/accounts/users/delete/' . $this->id,
			array( 'password' => $current_password ),
			$this->token
		);
	}


	/**
	 * Retrieve the thumbnail and update the base64 encoding locally.
	 *
	 * @return string The base64 encoded user thumbnail.
	 *
	 * @since 1.1.0
	 */
	public function get_picture(): string {
		if ( 'default_image_users' === $this->preferences['profile_image'] ) {
			$this->picture = get_site_url(
				null,
				'/wp-content/plugins/policyms/public/assets/svg/user.svg'
			);
		} else {
			$picture_data  = PolicyMS_Communication_Controller::api_request(
				'GET',
				'/images/' . $this->preferences['profile_image'],
				array(),
				$this->token,
				array(
					'Content-Type: application/octet-stream',
					! empty( $this->token ) ? 'x-access-token: ' . $this->token : null,
				)
			);
			$this->picture = 'data:image/*;base64,' . base64_encode( $picture_data );
		}

		return $this->picture;
	}

	/**
	 * Delete the profile picture.
	 *
	 * @return string The new access token.
	 *
	 * @since 1.1.0
	 */
	public function delete_picture(): string {
		$response = PolicyMS_Communication_Controller::api_request(
			'DELETE',
			'/accounts/users/image/' . $this->id,
			array(),
			$this->token,
			array(
				'Content-Type: application/json',
				'x-access-token: ' . $this->token,
				'x-more-time: ' .
					PolicyMS_Public::get_setting(
						true,
						'api_access_token'
					),
			)
		);
		return parent::encrypt_token( $response['token'] );
	}

	/**
	 * Update the profile picture.
	 *
	 * @param array $picture The profile picture upload information from `$_FILES`.
	 * @return string The new access token.
	 * @throws PolicyMSInvalidDataException On unsupported formats and sizes.
	 *
	 * @since 1.1.0
	 */
	public function update_picture( array $picture ): ?string {
		if ( 0 === $picture['error'] ) {
			if ( 'image/jpeg' !== $picture['type'] && 'image/png' !== $picture['type'] ) {
				throw new PolicyMSInvalidDataException(
					'Supported formats for  profile pictures are .png and .jpg/.jpeg.'
				);
			}
			if ( $picture['size'] > 1000000 ) {
				throw new PolicyMSInvalidDataException(
					'The image file is too large. Please upload a file less than 1MB in size.'
				);
			}

			$response = PolicyMS_Communication_Controller::api_request(
				'PUT',
				'/accounts/users/image',
				array(
					'asset' => new CURLFile(
						$picture['tmp_name'],
						$picture['type'],
						$this->id
					),
				),
				$this->token,
				array(
					'x-access-token: ' . $this->token,
					'x-more-time: ' .
						PolicyMS_Public::get_setting(
							true,
							'api_access_token'
						),
				),
				true
			);

			return $response['token'];
		} elseif ( 4 === $picture['error'] ) {
			return null;
		}
	}

	/**
	 * Retrieve a complete copy of all of the user's data.
	 *
	 * @return array The data copy.
	 */
	public function get_data_copy(): array {
		$response = PolicyMS_Communication_Controller::api_request(
			'GET',
			'/accounts/users/data',
			array(),
			$this->token
		);

		return $response['account_data'];
	}

	/**
	 * Disconnect the Google OAuth connection.
	 *
	 * @return string The encrypted access token.
	 */
	public function disconnect_google(): string {
		$response = PolicyMS_Communication_Controller::api_request(
			'POST',
			'/accounts/users/sso/google/disconnect',
			array(),
			null,
			array(
				'Content-Type: application/json',
				'x-more-time: ' .
					PolicyMS_Public::get_setting(
						true,
						'api_access_token'
					),
				'x-access-token: ' . $this->token,
			)
		);
		return parent::encrypt_token( $response['token'] );
	}

	/**
	 * Disconnect the KeyCloak OAuth connection.
	 *
	 * @return string The encrypted access token.
	 */
	public function disconnect_keycloak(): string {
		$response = PolicyMS_Communication_Controller::api_request(
			'POST',
			'/accounts/users/sso/keycloak/disconnect',
			array(),
			null,
			array(
				'Content-Type: application/json',
				'x-more-time: ' .
					PolicyMS_Public::get_setting(
						true,
						'api_access_token'
					),
				'x-access-token: ' . $this->token,
			)
		);
		return parent::encrypt_token( $response['token'] );
	}

	/**
	 * Disconnect the EGI OAuth connection.
	 *
	 * @return string The encrypted access token.
	 */
	public function disconnect_egi(): string {
		$response = PolicyMS_Communication_Controller::api_request(
			'POST',
			'/accounts/users/sso/egi-check-in/disconnect',
			array(),
			null,
			array(
				'Content-Type: application/json',
				'x-more-time: ' .
					PolicyMS_Public::get_setting(
						true,
						'api_access_token'
					),
				'x-access-token: ' . $this->token,
			)
		);
		return parent::encrypt_token( $response['token'] );
	}


	/**
	 * ------------------------------------------------
	 * Class Methods (Protected)
	 * ------------------------------------------------
	 */

	/**
	 * Retrieve the user's statistics.
	 *
	 * @return array The specially formatted statistics array.
	 *
	 * @since 1.3.0
	 */
	protected function get_statistics(): array {
		$this->statistics = PolicyMS_Communication_Controller::api_request(
			'GET',
			'/accounts/users/statistics/' . $this->id,
			array(),
			$this->token
		)['results'];

		return $this->statistics;
	}

	/**
	 * Retrieve the user's descriptions collection.
	 *
	 * @return PolicyMS_Description_Collection The user's descriptions.
	 *
	 * @since 1.1.0
	 */
	protected function get_descriptions(): PolicyMS_Description_Collection {
		$this->descriptions = PolicyMS_Description_Collection::get_owned(
			$this
		);
		return $this->descriptions;
	}

	/**
	 * Retrieve the user's reviews.
	 *
	 * @return array The user's reviews.
	 *
	 * @since 1.1.0
	 */
	protected function get_reviews(): array {
		$this->reviews = PolicyMS_Review::get_owned(
			$this
		);
		return $this->reviews;
	}

	/**
	 * ------------------------------------------------
	 * Static Class Methods (Public)
	 * ------------------------------------------------
	 */

	/**
	 * Retrieve the access token after successful authentication.
	 *
	 * @param string $id The identification credential (UID, e-mail).
	 * @param string $password The active user password.
	 *
	 * @return string The access token.
	 */
	public static function authenticate( string $id, string $password ): string {
		// Get the authorised token.
		$response = PolicyMS_Communication_Controller::api_request(
			'POST',
			'/accounts/users/authorization',
			array(
				is_email( $id ) ? 'email' : 'uid' => $id,
				'password'                        => $password,
			)
		);

		return parent::encrypt_token( $response['token'] );
	}

	/**
	 * Register with Google OAuth token information.
	 *
	 * @param string $google_token The Google OAuth token.
	 * @return string The new access token.
	 *
	 * @since 1.2.0
	 */
	public static function register_google( string $google_token ): string {
		// Get the authorized token.
		$response = PolicyMS_Communication_Controller::api_request(
			'POST',
			'/accounts/users/sso/google/registration',
			array(
				'token' => $google_token,
			),
			null,
			array(
				'Content-Type: application/json',
				'x-more-time: ' .
					PolicyMS_Public::get_setting(
						true,
						'api_access_token'
					),
			)
		);

		return parent::encrypt_token( $response['token'] );
	}

	/**
	 * Authenticate with Google OAuth token information.
	 *
	 * @param string $google_token The Google OAuth token.
	 * @return string The new access token.
	 *
	 * @since 1.2.0
	 */
	public static function authenticate_google( string $google_token ): string {
		try {
			$token = PolicyMS_Account::retrieve_token();
		} catch ( PolicyMSUnauthorizedRequestException $e ) {
			$token = false;
		}

		// Get the authorized token.
		$response = PolicyMS_Communication_Controller::api_request(
			'POST',
			'/accounts/users/sso/google/login',
			array(
				'token' => $google_token,
			),
			null,
			array(
				'Content-Type: application/json',
				'x-more-time: ' .
					PolicyMS_Public::get_setting(
						true,
						'api_access_token'
					),
				! empty( $token ) ? 'x-access-token: ' . $token : '',
			)
		);

		return parent::encrypt_token( $response['token'] );
	}

	/**
	 * Authenticate with EGI OAuth token information.
	 *
	 * @param string $egi_code The EGI OAuth code.
	 * @return string The new access token.
	 * @throws PolicyMSAPIError When there is an issue with the EGI server.
	 *
	 * @since 1.2.0
	 */
	public static function authenticate_egi( string $egi_code ) {
		$options = PolicyMS_Public::get_setting(
			true,
			'egi_redirection_page',
			'egi_client_id',
			'egi_client_secret',
			'egi_code_challenge',
			'egi_code_verifier'
		);

		// Retrieve EGI data.
		$response = wp_remote_post(
			'https://aai-demo.egi.eu/auth/realms/egi/protocol/openid-connect/token',
			array(
				'body'    => 'grant_type=authorization_code&code=' . $egi_code . '&client_id=' . $options['egi_client_id'] . '&redirect_uri=' . substr( $options['egi_redirection_page'], 0, -1 ) . '&code_verifier=' . $options['egi_code_verifier'] . '&client_secret=' . $options['egi_client_secret'],
				'headers' => array(
					'Content-Type' => 'application/x-www-form-urlencoded',
				),
			)
		);

		$http_code = wp_remote_retrieve_response_code( $response );
		if ( 200 !== $http_code || is_wp_error( $response ) ) {
			throw new PolicyMSAPIError( 'There was an unknown error when contacting the EGI authentication server.', $http_code );
		}

		// Get final user token.
		$response = PolicyMS_Communication_Controller::api_request(
			'POST',
			'/accounts/users/sso/egi-check-in/login',
			json_decode( wp_remote_retrieve_body( $response ) ),
			( self::is_authenticated() ) ? self::retrieve_token() : null
		);

		return parent::encrypt_token( $response['token'] );
	}

	/**
	 * Register with KeyCloak OAuth login information.
	 *
	 * @param string $username The KeyCloak OAuth username.
	 * @param string $password The KeyCloak OAuth password.
	 * @return string The new access token.
	 *
	 * @since 1.2.0
	 */
	public static function register_keycloak(
		string $username,
		string $password
	): string {
		// Get the authorized token.
		$response = PolicyMS_Communication_Controller::api_request(
			'POST',
			'/accounts/users/sso/keycloak/registration',
			array(
				'username' => $username,
				'password' => $password,
			),
			null,
			array(
				'Content-Type: application/json',
				'x-more-time: ' .
					PolicyMS_Public::get_setting(
						true,
						'api_access_token'
					),
			)
		);

		return parent::encrypt_token( $response['token'] );
	}

	/**
	 * Authenticate with KeyCloak OAuth login information.
	 *
	 * @param string $username The KeyCloak OAuth username.
	 * @param string $password The KeyCloak OAuth password.
	 * @return string The new access token.
	 *
	 * @since 1.2.0
	 */
	public static function authenticate_keycloak(
		string $username,
		string $password
	): string {
		try {
			$token = PolicyMS_Account::retrieve_token();
		} catch ( PolicyMSUnauthorizedRequestException $e ) {
			$token = false;
		}

		$response = PolicyMS_Communication_Controller::api_request(
			'POST',
			'/accounts/users/sso/keycloak/login',
			array(
				'username' => $username,
				'password' => $password,
			),
			null,
			array(
				'Content-Type: application/json',
				'x-more-time: ' .
					PolicyMS_Public::get_setting(
						true,
						'api_access_token'
					),
				! empty( $token ) ? 'x-access-token: ' . $token : '',
			)
		);

		return parent::encrypt_token( $response['token'] );
	}

	/**
	 * Register a new user with the API.
	 *
	 * @param array $information The user information.
	 * @return string The new access token.
	 *
	 * @since 1.1.0
	 */
	public static function register( array $information ): string {
		self::inspect( $information, array( 'password', 'email', 'name', 'surname' ) );

		$response = PolicyMS_Communication_Controller::api_request(
			'POST',
			'/accounts/users/registration',
			array(
				'account' => array(
					'password' => $information['password'],
				),
				'info'    => array(
					'name'         => stripslashes( $information['name'] ),
					'surname'      => stripslashes( $information['surname'] ),
					'title'        => $information['title'] ?? '',
					'gender'       => $information['gender'] ?? '',
					'organization' => stripslashes( $information['organization'] ?? '' ),
					'phone'        => $information['phone'] ?? '',
					'email'        => $information['email'],
					'about'        => stripslashes( $information['about'] ),
					'social'       => self::implode_urls(
						$information['links-title'],
						$information['links-url']
					),
				),
			)
		);

		return parent::encrypt_token( $response['token'] );
	}

	/**
	 * Request a password reset for a specific email.
	 *
	 * @param string $email The concerned email address.
	 * @return void
	 *
	 * @since 1.2.0
	 */
	public static function reset_password( string $email ) {
		PolicyMS_Communication_Controller::api_request(
			'POST',
			'/accounts/users/password/reset',
			array(
				'email' => $email,
			)
		);
	}

	/**
	 * Delete another user via the API.
	 *
	 * @param string $current_password The current administrator's password.
	 * @param string $uid The user ID for deletion.
	 *
	 * @since 1.1.0
	 */
	public static function delete_other( string $current_password, string $uid ) {

		PolicyMS_Communication_Controller::api_request(
			'DELETE',
			'/accounts/users/delete/' . $uid,
			array(
				'password' => $current_password,
				'uid'      => $uid,
			),
			PolicyMS_Account::retrieve_token()
		);
	}

	/**
	 * ------------------------------------------------
	 * Static Class Methods (Protected)
	 * ------------------------------------------------
	 */

	/**
	 * Get the main account information.
	 *
	 * @param ?string $id Another user's ID, if any.
	 * @return array The account data.
	 *
	 * @since 1.1.0
	 */
	protected static function get_account_data( string $id = null ): array {
		$response = PolicyMS_Communication_Controller::api_request(
			'GET',
			'/accounts/users/information' . ( isset( $id ) ? '/' . $id : '' ) . '?resources=1',
			array(),
			PolicyMS_Account::retrieve_token()
		);

		return $response['result'];
	}

	/**
	 * Check the provided and required information for validity.
	 *
	 * @param array $information The information array.
	 * @param array $required The required information IDs.
	 * @throws PolicyMSInvalidDataException On invalid or incomplete data.
	 *
	 * @since 1.1.0
	 */
	protected static function inspect(
		array $information,
		array $required = null
	): void {
		// Check required fields.
		if ( isset( $required ) ) {
			foreach ( $required as $field ) {
				if ( empty( $information[ $field ] ) ) {
					throw new PolicyMSInvalidDataException(
						'Please fill in all the required fields.'
					);
				}
			}
		}

		// Check email.
		if ( ! filter_var( $information['email'], FILTER_VALIDATE_EMAIL ) ) {
			throw new PolicyMSInvalidDataException(
				'Please enter a valid email address.'
			);
		}

		// Check password and confirmation.
		if ( ! empty( $information['password'] ) ) {
			if ( ! empty( preg_match( '@[A-Z]@', $information['password'] ) ) &&
				! empty( preg_match( '@[a-z]@', $information['password'] ) ) &&
				! empty( preg_match( '@[0-9]@', $information['password'] ) ) &&
				! empty( preg_match( '@[^\w]@', $information['password'] ) ) &&
				strlen( $information['password'] ) < 8
			) {
				throw new PolicyMSInvalidDataException(
					'Password should be at least 8 characters and  include at least one uppercase letter, a number, and a special character.'
				);
			}
			if ( ! empty( $information['password-confirm'] ) ) {
				if ( $information['password'] !== $information['password-confirm'] ) {
					throw new PolicyMSInvalidDataException(
						'Password and password confirmation should match.'
					);
				}
			}
		}

		// Check title.
		if ( ! empty( $information['title'] ) ) {
			if ( ! array_key_exists(
				$information['title'],
				self::$titles
			) ) {
				throw new PolicyMSInvalidDataException( 'Please select a valid title.' );
			}
		}

		// Check gender.
		if ( ! empty( $information['gender'] ) ) {
			if ( ! array_key_exists(
				$information['gender'],
				self::$genders
			) ) {
				throw new PolicyMSInvalidDataException(
					'Please select a gender from the list.'
				);
			}
		}
	}

	/**
	 * Create a combined line formatted array of URLs and titles.
	 *
	 * @param array|string $titles One or more titles.
	 * @param array|string $urls One or more URLs.
	 * @return array The line formatted array of URLs and titles.
	 *
	 * @since 1.1.0`
	 */
	public static function implode_urls( array|string $titles, array|string $urls ): array {
		if ( ! empty( $titles ) && ! empty( $urls ) ) {
			if ( ! is_array( $titles ) || ! is_array( $urls ) ) {
				$titles = array( $titles );
				$urls   = array( $urls );
			}
			return array_filter(
				array_map(
					function ( $k, $v ) use ( $urls ) {
						return $v . ':' . $urls[ $k ];
					},
					array_keys( $titles ),
					$titles
				),
				function ( $combined_string ) {
					return ':' !== $combined_string;
				}
			);
		} else {
			return array( ':' );
		}
	}
}
