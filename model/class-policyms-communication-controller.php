<?php

class PolicyMS_Communication_Controller {

	/**
	 * Return the error message derived from a file upload error code.
	 *
	 * @uses    PolicyMS_Public::account_registration()
	 *
	 * @since   1.0.0
	 * @author  Alexandros Raikos <araikos@unipi.gr>
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
	 * @param string $http_method The standardized HTTP method used for the request.
	 * @param array  $data The data to be sent according to the documented schema.
	 * @param string $token The encoded user access token.
	 * @param array  $additional_headers Any additional HTTP headers for the request.
	 *
	 * @throws InvalidArgumentException For missing WordPress settings.
	 * @throws ErrorException For connectivity and other API issues.
	 *
	 * @since   1.0.0
	 * @author  Alexandros Raikos <araikos@unipi.gr>
	 */
	public static function api_request( $http_method, $uri, $data = array(), $token = null, $headers = null, $skip_encoding = false ) {

		// Retrieve hostname URL.
		$options = get_option( 'policyms_plugin_settings' );
		if ( empty( $options['marketplace_host'] ) ) {
			throw new InvalidArgumentException( 'No PolicyMS API hostname was defined in WordPress settings.' );
		}
		if ( empty( $options['api_access_token'] ) ) {
			throw new InvalidArgumentException( 'No PolicyMS API access key was defined in WordPress settings.' );
		}

		if ( ! empty( $data ) ) {
			$data = ( $skip_encoding ) ? $data : json_encode( $data );
		}
		// Contact Marketplace login API endpoint.
		$curl = curl_init();
		curl_setopt_array(
			$curl,
			array(
				CURLOPT_URL            => $options['marketplace_host'] . $uri,
				CURLOPT_RETURNTRANSFER => true,
				CURLOPT_ENCODING       => '',
				CURLOPT_MAXREDIRS      => 10,
				CURLOPT_TIMEOUT        => 0,
				CURLOPT_FOLLOWLOCATION => true,
				CURLOPT_HTTP_VERSION   => CURL_HTTP_VERSION_1_1,
				CURLOPT_CUSTOMREQUEST  => $http_method,
				CURLOPT_POSTFIELDS     => ( ! empty( $data ) ) ? $data : null,
				CURLOPT_HTTPHEADER     => $headers ?? array( 'Content-Type: application/json', ( ! empty( $token ) ? ( 'x-access-token: ' . $token ) : null ), 'x-more-time: ' . $options['api_access_token'] ),
			)
		);

		// Get the data.
		$response  = curl_exec( $curl );
		$curl_http = curl_getinfo( $curl, CURLINFO_HTTP_CODE );

		// Handle errors.
		if ( curl_errno( $curl ) ) {
			throw new Exception( 'Unable to reach the Marketplace server. More details: ' . curl_error( $curl ) );
		}

		curl_close( $curl );
		if ( $curl_http != 200 && $curl_http != 201 && $curl_http != 403 ) {
			throw new PolicyMSAPIError(
				'The PolicyMS API encountered an HTTP ' . $curl_http . ' status code. More information: ' . $response ?? '',
				$curl_http
			);
		} else {
			if ( isset( $response ) ) {
				if ( is_string( $response ) ) {
					$decoded = json_decode( $response, true );
					if ( json_last_error() === JSON_ERROR_NONE ) {
						if ( $decoded['_status'] == 'successful' ) {
							return $decoded;
						} else {
							throw new PolicyMSAPIError(
								'PolicyMS error when contacting ' . $uri . ': ' . $decoded['message'],
								$curl_http
							);
						}
					} else {
						return $response;
					}
				}
			} else {
				curl_close( $curl );
				throw new ErrorException( 'There was no response.' );
			};
		}
	}
}
