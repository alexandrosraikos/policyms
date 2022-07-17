<?php
/**
 * The class definition for description assets.
 *
 * @link       https://github.com/alexandrosraikos/policyms/
 * @since      1.1.0
 *
 * @package    PolicyMS
 * @subpackage PolicyMS/model
 */

/**
 * The class definition for description assets.
 *
 * Defines description asset properties and helper methods.
 *
 * @package    PolicyMS
 * @subpackage PolicyMS/model
 * @author     Alexandros Raikos <alexandros@araikos.gr>
 */
class PolicyMS_Asset {
	/**
	 * The asset's filename.
	 *
	 * @var string The file name.
	 */
	public string $filename;

	/**
	 * The asset's checksum type.
	 *
	 * @var string The checksum type.
	 */
	public string $checksum;

	/**
	 * The asset's size.
	 *
	 * @var string The size.
	 */
	public string $size;

	/**
	 * The asset's update date.
	 *
	 * @var string The update date.
	 */
	public string $update_date;

	/**
	 * The asset's version.
	 *
	 * @var int The version number.
	 */
	public int $version;

	/**
	 * The asset's number of downloads.
	 *
	 * @var int The number of downloads.
	 */
	public int $downloads;


	/**
	 * Initialize a description asset object instance.
	 *
	 * @param string                     $id The asset ID.
	 * @param PolicyMS_Asset_Type|string $type The asset type instance or ID.
	 * @param array                      $metadata The raw API metadata array.
	 *
	 * @since 1.1.0
	 */
	public function __construct(
		public string $id,
		public PolicyMS_Asset_Type $type,
		array $metadata
		) {
		$this->id   = $id;
		$this->type = $type;

		$this->filename    = $metadata['filename'];
		$this->checksum    = $metadata['md5'];
		$this->size        = $metadata['size'];
		$this->update_date = $metadata['updateDate'];
		$this->version     = $metadata['version'];
		$this->downloads   = $metadata['downloads'];
	}

	/**
	 * Update an asset.
	 *
	 * @param string $key The identifier in the `$_FILES` cache.
	 *
	 * @since 1.1.0
	 */
	public function update( string $key ): void {
		$token = PolicyMS_Account::retrieve_token();

		self::handle_upload(
			$key,
			$this->type,
			function ( $file ) use ( $token ) {
				PolicyMS_Communication_Controller::api_request(
					'PUT',
					'/assets/' . $this->type->id . '/' . $this->id,
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

	/**
	 * Delete an asset.
	 *
	 * @param string $asset_category The asset's category.
	 * @param string $asset_id The asset's ID.
	 *
	 * @since 1.1.0
	 */
	public static function delete( string $asset_category, string $asset_id ): void {
		PolicyMS_Communication_Controller::api_request(
			'DELETE',
			'/assets/' . $asset_category . '/' . $asset_id,
			array(),
			PolicyMS_Account::retrieve_token()
		);
	}

	/**
	 * Pull the asset data (currently only supports image thumbnails).
	 *
	 * @since 1.1.0
	 */
	public function pull() {
		$token    = PolicyMS_Account::retrieve_token();
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

	/**
	 * Generate a temporary asset one time download code.
	 *
	 * @param string $category The asset's category.
	 * @param string $id The asset's ID.
	 *
	 * @return string The download OTC.
	 *
	 * @since 1.2.0
	 */
	public static function get_download_url( string $category, string $id ): string {
		$response = PolicyMS_Communication_Controller::api_request(
			'GET',
			'/assets/' . $category . '/' . $id,
			array(),
			PolicyMS_Account::retrieve_token()
		);

		return $response['otc'];
	}

	/**
	 * Abstracts all file upload checks and allows for direct
	 * file usage on success using a completion.
	 *
	 * @param string              $key The `$_FILES` array key for the upload.
	 * @param PolicyMS_Asset_Type $type The assorted type for the file.
	 * @param callable            $completion The succeeding actions with the successfully checked file.
	 * @throws PolicyMSInvalidDataException On invalid or missing file.
	 *
	 * @since 1.1.0
	 */
	protected static function handle_upload(
		string $key,
		PolicyMS_Asset_Type $type,
		callable $completion
	) {

		/**
		 * Check size and type compatibility.
		 *
		 * @var callable
		 */
		$validate = function (
			stdClass $file,
			PolicyMS_Asset_Type $type
		) {
			$byte_size_limit = ( ( new PolicyMS_User() )->is_admin() ) ? 100000000000 : 100000000;

			if ( $file->size > $byte_size_limit ) {
				throw new PolicyMSInvalidDataException(
					'The file ' . $file->name . ' exceeds the size limit. Please upload a file less or equal to ' . ( $byte_size_limit / 1000000 ) . 'MB in size.'
				);
			}
			if ( ! $type->is_supported( $file->mimetype ) ) {
				throw new PolicyMSInvalidDataException(
					'Please provide a supported file format.'
				);
			}
		};

		/**
		 * Check for PHP errors, optionally ignores code 4 (UPLOAD_ERR_NO_FILE).
		 *
		 * @var callable
		 */
		$upload_errors = function ( int $error_id, bool $include_missing_files ): bool {
			// Throw on file error.
			if ( 0 !== $error_id ) {
				if ( 4 === $error_id ) {
					return $include_missing_files || false;
				} else {
					throw new PolicyMSInvalidDataException(
						'An error occured when uploading the new files: ' . PolicyMS_Communication_Controller::fileUploadErrorInterpreter( $error_id )
					);
				}
			}
		};

		// Check if multiple files were uploaded.
		if ( ! empty( $_FILES[ $key ]['tmp_name'] ) &&
			! empty( $_FILES[ $key ]['type'] ) &&
			! empty( $_FILES[ $key ]['name'] ) &&
			! empty( $_FILES[ $key ]['size'] ) &&
			! empty( $_FILES[ $key ]['error'] )
		) {
			if ( is_array( $_FILES[ $key ]['name'] ) ) {
				// Check for errors on each file before proceeding.
				// NOTE: The `$error_id` traversed in this foreach is sanitized by typecasting.
				foreach ( $_FILES[ $key ]['error'] as $index => $error_id ) {
					if ( ! $upload_errors( (int) $error_id, false ) ) {
						if (
							! empty( $_FILES[ $key ]['tmp_name'][ $index ] ) &&
							! empty( $_FILES[ $key ]['type'][ $index ] ) &&
							! empty( $_FILES[ $key ]['name'][ $index ] ) &&
							! empty( $_FILES[ $key ]['size'][ $index ] )
						) {
							$file           = new stdClass();
							$file->path     = sanitize_text_field( wp_unslash( $_FILES[ $key ]['tmp_name'][ $index ] ) );
							$file->mimetype = sanitize_mime_type( wp_unslash( $_FILES[ $key ]['type'][ $index ] ) );
							$file->name     = sanitize_file_name( wp_unslash( $_FILES[ $key ]['name'][ $index ] ) );
							$file->size     = (int) $_FILES[ $key ]['size'][ $index ];

							// Throw on incompatible specs.
							$validate( $file, $type );

							// Run file callback.
							$completion( $file );
						}
					}
				}
			} else {
				if ( ! $upload_errors( (int) $_FILES[ $key ]['error'], true ) ) {
					$file           = new stdClass();
					$file->path     = sanitize_text_field( wp_unslash( $_FILES[ $key ]['tmp_name'] ) );
					$file->mimetype = sanitize_mime_type( wp_unslash( $_FILES[ $key ]['type'] ) );
					$file->name     = sanitize_file_name( wp_unslash( $_FILES[ $key ]['name'] ) );
					$file->size     = (int) $_FILES[ $key ]['size'];

					// Throw on incompatible specs.
					$validate( $file, $type );

					// Run file callback.
					$completion( $file );
				} else {
					throw new PolicyMSInvalidDataException(
						'The file has not been received.'
					);
				}
			}
		} else {
			throw new PolicyMSInvalidDataException(
				'The file has not been received.'
			);
		}
	}

	/**
	 * Create a new asset.
	 *
	 * @param string               $key The `$_FILES` array key for the upload.
	 * @param PolicyMS_Description $description The associated description.
	 *
	 * @since 1.1.0
	 */
	public static function create(
		string $key,
		PolicyMS_Description $description,
	): void {
		$token = PolicyMS_Account::retrieve_token();

		// When creating, the key is identical.
		$type = PolicyMS_Asset_Type::get( $key );

		// Handle file whether array or singular ID.
		self::handle_upload(
			$key,
			$type,
			function ( $file ) use ( $type, $token, $description ) {
				PolicyMS_Communication_Controller::api_request(
					'POST',
					'/assets/' . $type->id . '/' . $description->id,
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
