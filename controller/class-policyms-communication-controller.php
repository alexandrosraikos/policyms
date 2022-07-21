<?php

class PolicyMS_Communication_Controller {

	/**
	 * Return the error message derived from a file upload error code.
	 *
	 * @uses    PolicyMS_Public::account_registration()
	 *
	 * @since   1.0.0
	 * @author  Alexandros Raikos <alexandros@araikos.gr>
	 */
	public static function fileUploadErrorInterpreter( $code ) {
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
		return $errors[ $code ] ?? $code;
	}


	/**
	 * Send a request to the PolicyMS API.
	 * Documentation: https://documenter.getpostman.com/view/16776360/TzsZs8kn#intro
	 *
	 * @param string  $http_method The standardized HTTP method used for the request.
	 * @param string  $uri The API endpoint.
	 * @param array   $data The data to be sent according to the documented schema.
	 * @param ?string $token The encoded user access token.
	 * @param ?array  $headers Any additional HTTP headers for the request.
	 * @param bool    $skip_encoding Whether to skip the JSON encoding.
	 *
	 * @throws PolicyMSMissingOptionsException For missing WordPress settings.
	 * @throws Exception For generic connectivity issues.
	 * @throws PolicyMSAPIError For unexpected HTTP error codes.
	 *
	 * @since   1.0.0
	 * @author  Alexandros Raikos <alexandros@araikos.gr>
	 */
	public static function api_request(
		string $http_method,
		string $uri,
		array $data = array(),
		?string $token = null,
		?array $headers = null,
		bool $skip_encoding = false
	) {

		// Retrieve hostname URL.
		$options = get_option( 'policyms_plugin_settings' );
		if ( empty( $options['marketplace_host'] ) ) {
			throw new PolicyMSMissingOptionsException( 'No PolicyMS API hostname was defined in WordPress settings.' );
		}
		if ( empty( $options['api_access_token'] ) ) {
			throw new PolicyMSMissingOptionsException( 'No PolicyMS API access key was defined in WordPress settings.' );
		}

		if ( ! empty( $data ) ) {
			$data = ( $skip_encoding ) ? $data : wp_json_encode( $data );
		}

		$http_headers = $headers ?? array(
			'Content-Type' => 'application/json',
			'x-more-time'  => $options['api_access_token'],
		);

		if ( $token ) {
			$http_headers['x-access-token'] = $token;
		}

		$response = wp_remote_request(
			$options['marketplace_host'] . $uri,
			array(
				'method'      => $http_method,
				'httpversion' => '1.1',
				'headers'     => $http_headers,
				'body'        => $data ?? null,
			)
		);

		if ( is_wp_error( $response ) ) {
			throw new Exception(
				'Unable to reach the API server. More details: ' . $response->get_error_message()
			);
		} else {
			$http_code = wp_remote_retrieve_response_code( $response );
			$data      = json_decode(
				wp_remote_retrieve_body( $response ),
				true
			);

			if (
				( 200 !== $http_code
				&& 201 !== $http_code
				&& 403 !== $http_code )
				|| 'unsuccessful' === ( $data['_status'] ?? '' )
				) {
				throw new PolicyMSAPIError( $data['message'] ?? 'Unknown error.', $http_code );
			} else {
				return $data;
			}
		}
	}
}
