<?php
/**
 * The class definition for the OAuth controller.
 *
 * @link       https://github.com/alexandrosraikos/policyms/
 * @since      2.0.0
 *
 * @package    PolicyMS
 * @subpackage PolicyMS/model
 */

/**
 * The class definition for the OAuth controller.
 *
 * Defines basic authentication properties, markup and methods.
 *
 * @package    PolicyMS
 * @subpackage PolicyMS/model
 * @author     Alexandros Raikos <alexandros@araikos.gr>
 */
class PolicyMS_OAuth_Controller {

	/**
	 * An associative array of supported services and titles.
	 *
	 * @var array $supported_services
	 *  The supported SSO service identifiers and titles.
	 */
	public static array $supported_services = array(
		'google'   => 'Google',
		'keycloak' => 'PolicyCLOUD (Internal)',
		'egi'      => 'EGI Check-in',
	);

	/**
	 * Construct an SSO controller.
	 *
	 * @param PolicyMS_User $user An associated user object, if any.
	 *
	 * @since 2.0.0
	 */
	public function __construct( public ?PolicyMS_User $user = null ) {
		self::enqueue_scripts();
		$this->user = $user;
	}

	/**
	 * Enqueue all OAuth related client scripts for interactivity.
	 *
	 * @param bool $remote Whether to load remote scripts as well.
	 *
	 * @since 2.0.0
	 */
	private static function enqueue_scripts( bool $remote = true ) {
		if ( $remote ) {
			wp_enqueue_script(
				'policyms-google-oauth',
				'https://accounts.google.com/gsi/client',
				array(),
				'2.0.0',
				true
			);
		}
		wp_enqueue_script(
			'policyms-oauth-controller',
			plugin_dir_url( __FILE__ ) . 'js/policyms-oauth-controller.js',
			array(
				'policyms',
				$remote ? 'policyms-google-oauth' : '',
			),
			'2.0.0',
			true
		);
	}

	/**
	 * Check if a given OAuth service identifier exists.
	 *
	 * @param string $service The service identifier.
	 * @param bool   $throw Whether to throw an exception if not found.
	 * @throws PolicyMSInvalidDataException When the service is not supported.
	 * @return bool
	 *
	 * @since 2.0.0
	 */
	private function exists( string $service, bool $throw = true ): bool {
		$exists = array_key_exists( $service, self::$supported_services );
		if ( ! $exists && $throw ) {
			throw new PolicyMSInvalidDataException(
				sprintf( "The SSO service %s doesn't exist.", $service )
			);
		}
		return $exists;
	}


	/**
	 * Get the connect button HTML of a given service.
	 *
	 * @param string $service The OAuth service identifier.
	 * @param bool   $register Whether the context is registration.
	 * @return string The connect button HTML.
	 *
	 * @since 2.0.0
	 */
	private function get_connect_html( string $service, bool $register ) {
		$context  = $register ? 'registration' : 'authentication';
		$home_url = ( empty( wp_parse_url( get_site_url() )['path'] )
		? '/'
		: wp_parse_url( get_site_url() )['path'] );
		switch ( $service ) {
			case 'google':
				$nonce = ( $register )
				? wp_create_nonce( 'policyms_account_user_registration_google' )
				: wp_create_nonce( 'policyms_account_user_authentication_google' );

				return <<<HTML
					<div 
						id="google-signin" 
						class="policyms-oauth-connect-google action minimal"
						data-context="{$context}"
						data-nonce="{$nonce}"
						data-redirect="{$home_url}">
					</div>
				HTML;

			case 'keycloak':
				$keycloak_button_label = $register ? 'Sign up with PolicyCLOUD (Internal)' : 'Sign in with PolicyCLOUD (Internal)';

				$nonce = $register
					? wp_create_nonce( 'policyms_account_user_registration_keycloak' )
					: wp_create_nonce( 'policyms_account_user_authentication_keycloak' );

				return <<<HTML
					<button 
						id="keycloak" 
						class="keycloak" 
						data-context="{$context}"
						data-action="show-keycloak-modal"
						data-nonce="{$nonce}"
						data-redirect="{$home_url}">
						{$keycloak_button_label}
					</button>
				HTML;

			case 'egi':
				$egi_button_label = $register
					? 'Sign up with EGI Check-in'
					: 'Sign in with EGI Check-in';

				$egi_settings        = PolicyMS_Public::get_setting(
					true,
					'egi_redirection_page',
					'egi_client_id',
					'egi_code_challenge'
				);
				$egi_redirection_url = substr( $egi_settings['egi_redirection_page'], 0, -1 );
				return <<<HTML
					<button 
						id="egi" 
						data-action="policyms-redirect-egi"
						class="egi" 
						onClick="window.location.href = 'https://aai-demo.egi.eu/auth/realms/egi/protocol/openid-connect/auth?client_id= {$egi_settings['egi_client_id']}&scope=profile%20openid%20email&redirect_uri={$egi_redirection_url}&response_type=code&code_challenge={$egi_settings['egi_code_challenge']}&code_challenge_method=S256'">
						{$egi_button_label}
					</button>
				HTML;
		}
	}

	/**
	 * Get the disconnect button HTML of a given service.
	 *
	 * @param string $service The OAuth service identifier.
	 * @param bool   $password_protected Whether the account is password protected.
	 * @return string The disconnect button HTML.
	 *
	 * @since 2.0.0
	 */
	private function get_disconnect_html(
		string $service,
		bool $password_protected
		) {
		$password_protected_attribute = $password_protected ? 'password-protected' : '';
		$reset_password_url           = PolicyMS_Public::get_setting( true, 'password_reset_page' );

		switch ( $service ) {
			case 'google':
				$nonce = wp_create_nonce( 'policyms_account_disconnect_google' );
				return <<<HTML
					<button 
						class="action destructive minimal" 
						data-action="policyms-disconnect-google"
						data-nonce="{$nonce}"
						data-redirect={$reset_password_url}
						{$password_protected_attribute}>
						Disconnect
					</button>
				HTML;

			case 'keycloak':
				$nonce = wp_create_nonce( 'policyms_account_disconnect_keycloak' );
				return <<<HTML
					<button 
						class="action destructive minimal" 
						data-action="policyms-disconnect-keycloak" 
						data-nonce="{$nonce}"
						data-redirect={$reset_password_url}
						{$password_protected_attribute}>
						Disconnect
					</button>
				HTML;

			case 'egi':
				$nonce = wp_create_nonce( 'policyms_account_disconnect_egi' );
				return <<<HTML
					<button 
						class="action destructive minimal" 
						data-action="policyms-disconnect-egi" 
						data-nonce="{$nonce}"
						data-redirect={$reset_password_url}
						{$password_protected_attribute}>
						Disconnect
					</button>
				HTML;
		}
	}


	/**
	 * Get the button HTML for an OAuth portal.
	 *
	 * @param string $service The desired service.
	 * @param bool   $exclusive Whether it is a registration on connect.
	 * @return string
	 *
	 * @since 2.0.0
	 */
	public function get_html(
		string $service,
		bool $exclusive = true
		) {
		if ( self::exists( $service ) ) {
			if ( $this->user ) {
				return $this->get_disconnect_html(
					$service,
					$this->user->is_password_protected()
				);
			} else {
				return $this->get_connect_html(
					$service,
					$exclusive
				);
			}
		}
	}

	/**
	 * Get the EGI redirection shortcode.
	 *
	 * @since 2.0.0
	 */
	public static function get_egi_redirection_shortcode() {
		PolicyMS_Public::exception_handler(
			function () {
				// A nonce verification doesn't exist for this sequence (external data source).
				if ( ! empty( $_GET['code'] ) ) {
					self::enqueue_scripts( false );
					$token = PolicyMS_User::authenticate_egi(
						sanitize_key( $_GET['code'] )
					);

					$alert = notice_html(
						"Please wait while you're being redirected...",
						'notice'
					);

					$account_page_url = PolicyMS_Public::get_setting(
						true,
						'account_page'
					);

					return <<<HTML
						{$alert}
						<div 
							data-action="policyms-handle-egi-redirect"
							style="display:none"
							data-egi-redirect="{$account_page_url}"
							data-egi-token="{$token}">
						</div>
					HTML;
				} else {
					return notice_html( 'An EGI code was not found.' );
				}
			}
		);
	}

	/**
	 * Get the appropriate buttons for all portals.
	 *
	 * @since 2.0.0
	 */
	public function get_all_html(): string {
		$sso_buttons = '';
		foreach ( self::$supported_services as $sso_id => $sso_service ) {
			$sso_buttons .= $this->get_html( $sso_id );
		}

		return <<<HTML
			<div class="policyms-oauth-buttons">
				{$sso_buttons}
			</div>
		HTML;
	}
}
