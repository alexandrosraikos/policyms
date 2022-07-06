<?php

class PolicyMS_User extends PolicyMS_Account {

	public array $information;
	public array $metadata;
	public array $preferences;

	protected ?array $statistics;
	protected ?array $descriptions;
	protected ?array $reviews;
	protected ?string $picture;

	public function __construct( ?string $uid = null ) {
		if ( isset( $uid ) ) {
			$data = $this->get_account_data( $uid );
			parent::__construct( $uid );
		} else {
			$data = $this->get_account_data();
			parent::__construct( $data['uid'] );
		}

		$this->uid         = $data['uid'];
		$this->information = $data['info'];
		$this->metadata    = $data['account'];
		$this->preferences = $data['profile_parameters'];
	}

	public function __get( string $name ) {
		switch ( $name ) {
			case 'information':
			case 'metadata':
			case 'uid':
			case 'preferences':
				return $this->${$name};
			case 'statistics':
				return $this->statistics ?? $this->get_statistics();
			case 'descriptions':
				return $this->descriptions ?? $this->get_descriptions();
			case 'reviews':
				return $this->reviews ?? $this->get_reviews();
			case 'approvals':
				return PolicyMS_Description::get_pending();
			case 'picture':
				return $this->picture ?? $this->get_picture();
			default:
				throw new Exception(
					'The property "' .
						$name .
						'" does not exist in ' .
						get_class( $this ) .
						'.'
				);
		}
	}

	/**
	 * ------------
	 * Basic Methods
	 * ------------
	 */

	public function is_admin(): bool {
		return $this->get_role() == 'admin';
	}

	public function is_verified(): bool {
		return $this->metadata['verified'] == '1';
	}

	public function get_role(): string {
		return $this->metadata['role'];
	}

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
						$data['socials-title'],
						$data['socials-url']
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
			return parent::persist_token( $token );
		} else {
			return null;
		}
	}

	public function delete( string $current_password ): void {
		PolicyMS_Communication_Controller::api_request(
			'DELETE',
			'/accounts/users/delete/' . $this->id,
			array( 'password' => $current_password ),
			$this->token
		);
	}

	/**
	 * ------------
	 * Internal Methods
	 * ------------
	 */

	/**
	 *
	 * User Data
	 * ------------
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

	protected function get_descriptions(): array {
		$this->descriptions = PolicyMS_Description::get_owned(
			$this,
			$this->token
		);
		return $this->descriptions;
	}

	protected function get_reviews(): array {
		$this->reviews = PolicyMS_Review::get_owned(
			$this,
			$this->token
		);
		return $this->reviews;
	}

	/**
	 *
	 * User Picture
	 * ------------
	 */

	public function get_picture() {
		if ( $this->preferences['profile_image'] == 'default_image_users' ) {
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
					PolicyMS_Public::get_plugin_setting(
						true,
						'api_access_token'
					),
			)
		);
		return parent::persist_token( $response['token'] );
	}

	public function update_picture( array $picture ): ?string {
		if ( $picture['error'] == 0 ) {
			if ( $picture['type'] != 'image/jpeg' && $picture['type'] != 'image/png' ) {
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
						PolicyMS_Public::get_plugin_setting(
							true,
							'api_access_token'
						),
				),
				true
			);

			return $response['token'];
		} elseif ( $picture['error'] == 4 ) {
			return null;
		}
	}

	public function get_data_copy() {
		$response = PolicyMS_Communication_Controller::api_request(
			'GET',
			'/accounts/users/data',
			array(),
			$this->token
		);

		return $response['account_data'];
	}

	public function disconnect_google(): string {
		$response = PolicyMS_Communication_Controller::api_request(
			'POST',
			'/accounts/users/sso/google/disconnect',
			array(),
			null,
			array(
				'Content-Type: application/json',
				'x-more-time: ' .
					PolicyMS_Public::get_plugin_setting(
						true,
						'api_access_token'
					),
				'x-access-token: ' . $this->token,
			)
		);
		return parent::persist_token( $response['token'] );
	}

	public function disconnect_keycloak(): string {
		$response = PolicyMS_Communication_Controller::api_request(
			'POST',
			'/accounts/users/sso/keycloak/disconnect',
			array(),
			null,
			array(
				'Content-Type: application/json',
				'x-more-time: ' .
					PolicyMS_Public::get_plugin_setting(
						true,
						'api_access_token'
					),
				'x-access-token: ' . $this->token,
			)
		);
		return parent::persist_token( $response['token'] );
	}

	public function disconnect_egi(): string {
		$response = PolicyMS_Communication_Controller::api_request(
			'POST',
			'/accounts/users/sso/egi-check-in/disconnect',
			array(),
			null,
			array(
				'Content-Type: application/json',
				'x-more-time: ' .
					PolicyMS_Public::get_plugin_setting(
						true,
						'api_access_token'
					),
				'x-access-token: ' . $this->token,
			)
		);
		return parent::persist_token( $response['token'] );
	}

	/**
	 * ------------
	 * Basic Methods (Static)
	 * ------------
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

		return parent::persist_token( $response['token'] );
	}

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
					PolicyMS_Public::get_plugin_setting(
						true,
						'api_access_token'
					),
			)
		);

		return parent::persist_token( $response['token'] );
	}

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
					PolicyMS_Public::get_plugin_setting(
						true,
						'api_access_token'
					),
				! empty( $token ) ? 'x-access-token: ' . $token : '',
			)
		);

		return parent::persist_token( $response['token'] );
	}

	public static function authenticate_egi( string $egi_code ) {
		$options = PolicyMS_Public::get_plugin_setting(
			true,
			'egi_redirection_page',
			'egi_client_id',
			'egi_client_secret',
			'egi_code_challenge',
			'egi_code_verifier'
		);

		// Retrieve EGI data.
		$curl = curl_init();
		curl_setopt( $curl, CURLOPT_URL, 'https://aai-demo.egi.eu/auth/realms/egi/protocol/openid-connect/token' );
		curl_setopt( $curl, CURLOPT_RETURNTRANSFER, 1 );
		curl_setopt( $curl, CURLOPT_POST, 1 );
		curl_setopt( $curl, CURLOPT_POSTFIELDS, 'grant_type=authorization_code&code=' . $egi_code . '&client_id=' . $options['egi_client_id'] . '&redirect_uri=' . substr( $options['egi_redirection_page'], 0, -1 ) . '&code_verifier=' . $options['egi_code_verifier'] . '&client_secret=' . $options['egi_client_secret'] );
		curl_setopt( $curl, CURLOPT_HTTPHEADER, array( 'Content-Type: application/x-www-form-urlencoded' ) );

		$result    = curl_exec( $curl );
		$curl_http = curl_getinfo( $curl, CURLINFO_HTTP_CODE );
		if ( curl_errno( $curl ) ) {
			curl_close( $curl );
			throw new PolicyMSAPIError( 'There was an unexpected error when contacting the EGI authentication server.', $curl_http );
		}
		curl_close( $curl );

		if ( $curl_http != 200 ) {
			throw new PolicyMSAPIError( print_r( $result, true ), $curl_http );
		}

		// Get final user token.
		$response = PolicyMS_Communication_Controller::api_request(
			'POST',
			'/accounts/users/sso/egi-check-in/login',
			json_decode( $result ),
			( self::is_authenticated() ) ? self::retrieve_token() : null
		);

		return parent::persist_token( $response['token'] );
	}

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
					PolicyMS_Public::get_plugin_setting(
						true,
						'api_access_token'
					),
			)
		);

		return parent::persist_token( $response['token'] );
	}


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
					PolicyMS_Public::get_plugin_setting(
						true,
						'api_access_token'
					),
				! empty( $token ) ? 'x-access-token: ' . $token : '',
			)
		);

		return parent::persist_token( $response['token'] );
	}

	public static function reset_password( string $email ) {
		PolicyMS_Communication_Controller::api_request(
			'POST',
			'/accounts/users/password/reset',
			array(
				'email' => $email,
			)
		);
	}

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
						$information['socials-title'],
						$information['socials-url']
					),
				),
			)
		);

		return parent::persist_token( $response['token'] );
	}

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
	 * ------------
	 * Internal Methods (Static)
	 * ------------
	 */

	protected static function get_account_data( string $id = null ): array {
		$response = PolicyMS_Communication_Controller::api_request(
			'GET',
			'/accounts/users/information' . ( isset( $id ) ? '/' . $id : '' ),
			array(),
			PolicyMS_Account::retrieve_token()
		);

		return $response['result'];
	}

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
			if ( ! in_array(
				$information['title'],
				array(
					'Mr.',
					'Ms.',
					'Mrs.',
					'Dr.',
					'Prof.',
					'Sir',
					'Miss',
					'Mx.',
					'-',
				)
			) ) {
				throw new InvalidArgumentException( 'Please select a valid title.' );
			}
		}

		// Check gender.
		if ( ! empty( $information['gender'] ) ) {
			if ( ! in_array(
				$information['gender'],
				array(
					'male',
					'female',
					'transgender',
					'genderqueer',
					'questioning',
					'-',
				)
			) ) {
				throw new InvalidArgumentException(
					'Please select a gender from the list.'
				);
			}
		}
	}

	public static function implode_urls( $titles, $urls ): array {
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
					return $combined_string != ':';
				}
			);
		} else {
			return array( ':' );
		}
	}
}
