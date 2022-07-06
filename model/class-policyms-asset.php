<?php

class PolicyMS_Asset {

	public string $id;
	public string $category;

	public string $filename;
	public string $checksum;
	public string $size;
	public string $update_date;
	public int $version;
	public int $downloads;


	public function __construct( string $id, string $category, array $metadata ) {
		$this->id       = $id;
		$this->category = $category;

		$this->filename    = $metadata['filename'];
		$this->checksum    = $metadata['md5'];
		$this->size        = $metadata['size'];
		$this->update_date = $metadata['updateDate'];
		$this->version     = $metadata['version'];
		$this->downloads   = $metadata['downloads'];
	}

	public function update( string $file_identifier ): void {
		$token = PolicyMS_Account::retrieve_token();

		self::handle_retrieved_file(
			$file_identifier,
			$this->category,
			function ( $file ) use ( $token ) {
				PolicyMS_Communication_Controller::api_request(
					'PUT',
					'/assets/' . $this->category . '/' . $this->id,
					array(
						'asset' => new CURLFile( $file['path'], $file['mimetype'], $file['name'] ),
					),
					$token,
					array(
						'x-access-token: ' . $token,
					),
					true
				);
			}
		);
	}

	public static function delete( string $asset_category, $asset_id ): void {
		PolicyMS_Communication_Controller::api_request(
			'DELETE',
			'/assets/' . $asset_category . '/' . $asset_id,
			array(),
			PolicyMS_Account::retrieve_token()
		);
	}

	public function pull() {
		$token = PolicyMS_Account::retrieve_token();

		// Currently only supports images.
		$response = PolicyMS_Communication_Controller::api_request(
			'GET',
			'/images/' . $this->id . '?thumbnail=yes',
			array(),
			$token,
			array(
				'Content-Type: application/octet-stream',
				( ! empty( $token ) ? ( 'x-access-token: ' . $token ) : null ),
			),
		);

		return $response;
	}

	public static function get_download_url( string $category, string $id ): string {
		$response = PolicyMS_Communication_Controller::api_request(
			'GET',
			'/assets/' . $category . '/' . $id,
			array(),
			PolicyMS_Account::retrieve_token()
		);

		return $response['otc'];
	}

	protected static function check_specs( $type, array $file ): void {
		$byte_size_limit = ( ( new PolicyMS_User() )->is_admin() ) ? 100000000000 : 100000000;

		if ( $file['size'] > $byte_size_limit ) {
			throw new PolicyMSInvalidDataException(
				'The file ' . $file['name'] . ' exceeds the size limit. Please upload a file less or equal to ' . ( $byte_size_limit / 1000000 ) . 'MB in size.'
			);
		}
		switch ( $type ) {
			case 'images':
				if ( $file['mimetype'] != 'image/jpeg' &&
					$file['mimetype'] != 'image/png'
				) {
					throw new PolicyMSInvalidDataException(
						'Supported formats for asset images are .png and .jpg/.jpeg.'
					);
				}
				break;
			case 'videos':
				if ( $file['mimetype'] != 'video/mp4' &&
					$file['mimetype'] != 'video/ogg' &&
					$file['mimetype'] != 'video/webm'
				) {
					throw new PolicyMSInvalidDataException(
						'Supported formats for asset videos are .mp4, .ogg and .webm.'
					);
				}
				break;
			case 'files':
				break;
			default:
				throw new PolicyMSInvalidDataException(
					'There is no asset category of this type.'
				);
				break;
		}
	}

	protected static function handle_retrieved_file( string $name, string $category, callable $completion ) {
		if ( empty( $_FILES[ $name ] ) ) {
			throw new PolicyMSInvalidDataException(
				sprintf(
					'The file %s has not been received.',
					$name
				)
			);
		}

		// Check if multiple files were uploaded.
		if ( is_array( $_FILES[ $name ]['name'] ) ) {
			$files = array();
			// Check for errors on each file before proceeding.
			foreach ( $_FILES[ $name ]['error'] as $key => $error ) {
				// Throw on file error.
				if ( $error != 0 ) {
					if ( $error == 4 ) {
						continue;
					} else {
						throw new PolicyMSInvalidDataException(
							'An error occured when uploading the new files: ' . PolicyMS::fileUploadErrorInterpreter( $error )
						);
					}
				}

				$file = array(
					'path'     => $_FILES[ $name ]['tmp_name'][ $key ],
					'mimetype' => $_FILES[ $name ]['type'][ $key ],
					'name'     => $_FILES[ $name ]['name'][ $key ],
					'size'     => $_FILES[ $name ]['size'][ $key ],
				);

				// Throw on incompatible specs.
				self::check_specs( $category, $file );

				// Add accepted file to array.
				array_push( $files, $file );
			}

			// Get data and run completion.
			foreach ( $files as $file ) {
				$completion( $file );
			}
		} else {
			$error = $_FILES[ $name ]['error'];
			if ( $error != 0 ) {
				if ( $error != 4 ) {
					throw new PolicyMSInvalidDataException(
						'An error occured when uploading the new file: ' . PolicyMS::fileUploadErrorInterpreter( $error )
					);
				}
			} else {
				$file = array(
					'path'     => $_FILES[ $name ]['tmp_name'],
					'mimetype' => $_FILES[ $name ]['type'],
					'name'     => $_FILES[ $name ]['name'],
					'size'     => $_FILES[ $name ]['size'],
				);
				self::check_specs( $category, $file );
				$completion( $file );
			}
		}
	}

	public static function create( string $name, PolicyMS_Description $description, int $index = null ): void {
		$token    = PolicyMS_Account::retrieve_token();
		$category = $name;

		// Handle file whether array or singular ID.
		self::handle_retrieved_file(
			$name,
			$category,
			function ( $file ) use ( $category, $token, $description ) {

				PolicyMS_Communication_Controller::api_request(
					'POST',
					'/assets/' . $category . '/' . $description->id,
					array(
						'asset' => new CURLFile( $file['path'], $file['mimetype'], $file['name'] ),
					),
					$token,
					array(
						'x-access-token: ' . $token,
					),
					true
				);
			}
		);
	}
}
