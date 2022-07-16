<?php
/**
 * The abstract class requirement for accounts.
 *
 * @link       https://github.com/alexandrosraikos/policyms/
 * @since      1.1.0
 *
 * @package    PolicyMS
 * @subpackage PolicyMS/model
 */

/**
 * The abstract class requirement for accounts.
 *
 * Defines basic authentication properties and functionality,
 * as well as some basic properties.
 *
 * @package    PolicyMS
 * @subpackage PolicyMS/model
 * @author     Alexandros Raikos <alexandros@araikos.gr>
 */
abstract class PolicyMS_Account {

	/**
	 * The universal account identifier.
	 *
	 * @var string $id The alphanumeric identifier.
	 *
	 * @since 1.1.0
	 */
	public string $id;

	/**
	 * The retrieved access token.
	 *
	 * @var string $token The alphanumeric access token.
	 *
	 * @since 1.1.0
	 */
	protected string $token;

	/**
	 * A universal definition for privacy settings.
	 *
	 * @var array $privacy_switches An array of `'key' => 'label'` pairs.
	 *
	 * @since 2.0.0
	 */
	public static array $privacy_switches = array(
		'0' => 'Private',
		'1' => 'Public',
	);

	/**
	 * Initialize an account.
	 *
	 * @param string $id The universal identifier.
	 * @throws PolicyMSUnauthorizedRequestException If the token cannot be found.
	 *
	 * @since 1.1.0
	 */
	public function __construct( string $id ) {
		$this->id    = $id;
		$this->token = self::retrieve_token();
	}

	/**
	 * Encrypt the user access token for persistence.
	 *
	 * @param string $token The access token.
	 * @return string The encrypted access token.
	 * @throws ErrorException When the token cannot be encrypted.
	 *
	 * @since 1.1.0
	 */
	protected static function encrypt_token( string $token ): string {
		$encrypted = openssl_encrypt(
			$token,
			'AES-128-ECB',
			PolicyMS_Public::get_setting( true, 'encryption_key' )
		);
		if ( ! $encrypted ) {
			throw new ErrorException(
				'The token could not be encrypted.'
			);
		}
		return $encrypted;
	}

	/**
	 * Decrypt the user access token for authenticated API calls.
	 *
	 * @param string $token The encrypted access token.
	 * @return string The decrypted access token.
	 * @throws ErrorException When the token cannot be decrypted.
	 *
	 * @since 1.1.0
	 */
	private static function decrypt_token( string $token ): string {
		$decrypted = openssl_decrypt(
			$token,
			'AES-128-ECB',
			PolicyMS_Public::get_setting( true, 'encryption_key' )
		);
		if ( ! $decrypted ) {
			throw new ErrorException(
				'The token could not be decrypted.'
			);
		}
		return $decrypted;
	}

	/**
	 * Retrieve and decrypt the token from the user's browser.
	 *
	 * @return string The decrypted access token.
	 * @throws PolicyMSUnauthorizedRequestException When the token cannot be found.
	 *
	 * @since   1.1.0
	 */
	public static function retrieve_token() {
		// Retrieve saved token.
		if ( ! empty( $_COOKIE['pcmapi-token'] ) ) {
			return self::decrypt_token( sanitize_key( $_COOKIE['pcmapi-token'] ) );
		} else {
			throw new PolicyMSUnauthorizedRequestException( 'The token could not be found.' );
		};
	}

	/**
	 * Checks if a readable token exists in the user's browser.
	 *
	 * @return bool
	 *
	 * @since 1.2.0
	 */
	public static function is_authenticated(): bool {
		try {
			return ! empty( self::retrieve_token() );
		} catch ( PolicyMSUnauthorizedRequestException $e ) {
			return false;
		}
	}

	/**
	 * Register the account with the API.
	 *
	 * @param array $information The array of account information.
	 *
	 * @since 1.1.0
	 */
	abstract public static function register( array $information);

	/**
	 * Authenticate the account with the API.
	 *
	 * @param string $id The account identifier.
	 * @param string $password The account password.
	 * @return string The access token.
	 *
	 * @since 1.1.0
	 */
	abstract public static function authenticate( string $id, string $password): string;


	/**
	 * Get the account's role.
	 *
	 * @return string The account role.
	 *
	 * @since 1.1.0
	 */
	abstract public function get_role(): string;

	/**
	 * Get a copy of all the account's data.
	 *
	 * @return string An encoded JSON string.
	 *
	 * @since 2.0.0
	 */
	abstract public function get_data_copy(): array;
}
