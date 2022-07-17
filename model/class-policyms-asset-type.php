<?php
/**
 * The class definition for description asset types.
 *
 * @link       https://github.com/alexandrosraikos/policyms/
 * @since      1.1.0
 *
 * @package    PolicyMS
 * @subpackage PolicyMS/model
 */

/**
 * The class definition for description asset types.
 *
 * Defines description asset properties and helper methods.
 *
 * @package    PolicyMS
 * @subpackage PolicyMS/model
 * @author     Alexandros Raikos <alexandros@araikos.gr>
 */
class PolicyMS_Asset_Type {

	/**
	 * Initialize an asset type object instance.
	 *
	 * @param string $id The type ID.
	 * @param string $label The type label.
	 * @param string $label_plural The type label in plural form.
	 * @param string $mimetypes The comma-separated mimetypes (RFC6838).
	 * @param string $notice Any file type related notice for the user.
	 */
	public function __construct(
		public string $id,
		public string $label,
		public string $label_plural,
		public string $mimetypes,
		public string $notice = '' ) {
		$this->id           = $id;
		$this->label        = $label;
		$this->label_plural = $label_plural;
		$this->mimetypes    = $mimetypes;
		$this->notice       = $notice;
	}


	/**
	 * Extract an array of allowed file extensions from
	 * the mimetype string.
	 *
	 * @return array
	 *
	 * @since 2.0.0
	 */
	public function get_extensions(): array {
		return array_map(
			function ( $mimetypes ) {
				return explode( '/', $mimetypes )[1];
			},
			explode( ',', $this->mimetypes )
		);
	}

	/**
	 * Check whether a mimetype is supported within this asset type.
	 *
	 * @param string $mimetype The mimetype to check.
	 * @return bool
	 *
	 * @since 2.0.0
	 */
	public function is_supported( string $mimetype ): bool {
		return in_array( $mimetype, explode( ',', $this->mimetypes ) );
	}

	/**
	 * Check whether the asset type belongs to the gallery.
	 *
	 * @return bool
	 *
	 * @since 2.0.0
	 */
	public function in_gallery() {
		return ( 'image' === $this->id || 'video' === $this->id );
	}

	/**
	 * Retrieve all the supported types in object format.
	 *
	 * @return array The supported types.
	 */
	public static function get_supported_types() {
		return (
			array(
				new self(
					'image',
					'Image',
					'Images',
					'image/jpeg,image/png'
				),
				new self(
					'video',
					'Video',
					'Videos',
					'video/mp4,video/ogg,video/webm',
					'Uploaded gallery videos are publicly accessible. Please do not include sensitive or protected information.'
				),
				new self(
					'file',
					'File',
					'Files',
					''
				),
			)
		);
	}

	/**
	 * Get an asset type object from an asset type ID.
	 *
	 * @param  string $id The asset type identifier.
	 * @return self
	 *
	 * @since 2.0.0
	 */
	public static function get( string $id ) {
		foreach ( self::get_supported_types() as $supported_type ) {
			if ( $id === $supported_type->id ) {
				return $supported_type;
			}
		}
	}
}
