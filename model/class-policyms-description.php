<?php
/**
 * The class definition for descriptions.
 *
 * @link       https://github.com/alexandrosraikos/policyms/
 * @since      1.1.0
 *
 * @package    PolicyMS
 * @subpackage PolicyMS/model
 */

/**
 * The class definition for descriptions.
 *
 * Defines description information and functionality.
 *
 * @package    PolicyMS
 * @subpackage PolicyMS/model
 * @author     Alexandros Raikos <alexandros@araikos.gr>
 */
class PolicyMS_Description {

	/**
	 * The unique description ID.
	 *
	 * @var string The unique alphanumeric ID.
	 *
	 * @since 1.1.0
	 */
	public string $id;

	/**
	 * The primary taxonomy (type/collection) of the description.
	 *
	 * @var string The type.
	 *
	 * @since 1.1.0
	 */
	public string $type;

	/**
	 * The basic decription information.
	 *
	 * @var array The formatted information
	 *
	 * @since 1.1.0
	 */
	public array $information;

	/**
	 * The line formatted array of related links.
	 *
	 * @var ?array The links.
	 *
	 * @since 1.1.0
	 */
	public ?array $links;

	/**
	 * The ID of the cover image.
	 *
	 * @var string The ID of the cover image.
	 *
	 * @since 1.1.0
	 */
	public string $image_id;

	/**
	 * The description's metadata.
	 *
	 * @var array The formatted metadata array.
	 *
	 * @since 1.1.0
	 */
	public array $metadata;

	/**
	 * The description's assets, if any.
	 *
	 * @var ?array The formatted assets array.
	 *
	 * @since 1.1.0
	 */
	public ?array $assets;

	/**
	 * The description's unique site URL.
	 *
	 * @var ?string The unique description URL.
	 *
	 * @since 1.1.0
	 */
	public ?string $url;

	/**
	 * The URL for the cover's thumbnail.
	 *
	 * @var ?string The cover thumbnail URL.
	 *
	 * @since 1.1.0
	 */
	public ?string $cover_thumbnail_url;

	/**
	 * The requesting user's review of the description instance.
	 *
	 * @var ?PolicyMS_Review The user's review.
	 *
	 * @since 1.1.0
	 */
	public ?PolicyMS_Review $user_review;

	/**
	 * The default description categories.
	 *
	 * @var array The categories.
	 *
	 * @since 2.0.0
	 */
	public static array $categories = array(
		'tools'     => 'Tools',
		'policies'  => 'Policies',
		'datasets'  => 'Datasets',
		'webinars'  => 'Webinars',
		'tutorials' => 'Tutorials',
		'documents' => 'Documents',
		'other'     => 'Other',
	);

	/**
	 * Initialize a description object instance from data or via the API.
	 *
	 * @param string $id The unique description ID.
	 * @param ?array $fetched Any pre-fetched description data from the API.
	 *
	 * @since 1.1.0
	 */
	public function __construct( string $id, ?array $fetched = null ) {
		$this->match_field(
			$fetched ?? PolicyMS_Communication_Controller::api_request(
				'GET',
				'/descriptions/all/' . $id,
				array(),
				PolicyMS_User::is_authenticated() ?
				PolicyMS_Account::retrieve_token() :
				null
			)[0][0]
		);
	}

	/**
	 * Update the description's information.
	 *
	 * @param array  $information The new information fields.
	 * @param ?array $file_identifiers Any available new uploaded file IDs
	 * for including and forwarding uploaded $_FILES.
	 *
	 * @since 1.1.0
	 */
	public function update( array $information, ?array $file_identifiers = null ) {
		// Upload new or update existing files.
		if ( ! empty( $file_identifiers ) ) {
			foreach ( $file_identifiers as $file_id ) {
				// Check for new files.
				if ( 'files' === $file_id ||
					'images' === $file_id ||
					'videos' === $file_id
				) {
					PolicyMS_Asset::create(
						$file_id,
						$this
					);
				} elseif ( substr( $file_id, 0, 6 ) === 'files-' ||
				substr( $file_id, 0, 7 ) === 'images-' ||
				substr( $file_id, 0, 7 ) === 'videos-'
				) {
					foreach ( $this->assets as $category => $assets ) {
						$file_category = explode( '-', $file_id )[0];
						if ( $category === $file_category ) {
							foreach ( $assets as $asset ) {
								$id = explode( '-', $file_id, 2 )[1];
								if ( $asset->id === $id ) {
									$asset->update(
										$file_id
									);
								}
							}
						}
					}
				}
			}
		}

		// TODO @alexandrosraikos: Remove 'subtype' entirely. (#128)
		// TODO @alexandrosraikos: Rename 'Fields of Use' to 'Keywords'. (#128)
		// Prepare the data.
		$data = array(
			'title'       => stripslashes( $information['title'] ),
			'type'        => $information['type'],
			'subtype'     => strtolower( $information['subtype'] ),
			'owner'       => stripslashes( $information['owner'] ),
			'description' => stripslashes( $information['description'] ),
			'links'       => PolicyMS_User::implode_urls(
				$information['links-title'],
				$information['links-url']
			),
			'keywords'    => $information['keywords'],
			'comments'    => stripslashes( $information['comments'] ),
		);

		// Submit to the API.
		PolicyMS_Communication_Controller::api_request(
			'PUT',
			'/descriptions/all/' . $this->id,
			$data,
			PolicyMS_Account::retrieve_token()
		);
	}

	/**
	 * Delete the description.
	 *
	 * @since 1.1.0
	 */
	public function delete() {
		PolicyMS_Communication_Controller::api_request(
			'DELETE',
			'/descriptions/all/' . $this->id,
			array(),
			PolicyMS_Account::retrieve_token()
		);
	}

	/**
	 * Check if the given user is the provider.
	 *
	 * @param PolicyMS_User $provider The user to check.
	 *
	 * @since 1.1.0
	 */
	public function is_provider( PolicyMS_User $provider ) {
		return $this->metadata['provider'] === $provider->id;
	}

	/**
	 * Check if the description is approved.
	 *
	 * @since 2.0.0
	 */
	public function is_approved() {
		return 0 !== $this->metadata['approved'];
	}

	/**
	 * Approve or reject the description (administrators only).
	 *
	 * @param string $decision Either 'approve' or 'disapprove'.
	 *
	 * @since 1.1.0
	 */
	public function approve( string $decision ) {
		// TODO @alexandrosraikos: Handle disapproval also, with reason for rejection (#135).
		PolicyMS_Communication_Controller::api_request(
			'POST',
			'/descriptions/permit/all/' . $this->id,
			array(),
			PolicyMS_Account::retrieve_token(),
			array(
				'x-access-token: ' . PolicyMS_Account::retrieve_token(),
				'x-permission: ' . $decision,
			)
		);
	}

	public function reject( string $reason ) {
		// TODO @alexandrosraikos/@vkoukos: Add the API request.
	}

	/**
	 * Set the default cover image for the description.
	 *
	 * @param string $description_id The ID of the description.
	 * @param string $image_id The ID of the image.
	 *
	 * @since 1.1.0
	 */
	public static function set_default_image( string $description_id, string $image_id ) {
		PolicyMS_Communication_Controller::api_request(
			'PUT',
			'/descriptions/image/' . $description_id . '/' . $image_id,
			array(),
			PolicyMS_Account::retrieve_token()
		);
	}


	/**
	 * Remove the default cover image for the description.
	 *
	 * @param string $description_id The ID of the description.
	 *
	 * @since 1.1.0
	 */
	public static function remove_default_image( string $description_id ) {
		PolicyMS_Communication_Controller::api_request(
			'DELETE',
			'/descriptions/image/' . $description_id,
			array(),
			PolicyMS_Account::retrieve_token()
		);
	}

	/**
	 * Get all of the description's reviews.
	 *
	 * @param int $page The page number.
	 *
	 * @since 1.1.0
	 */
	public function get_reviews( int $page = 1 ) {
		if ( empty( $this->reviews ) ) {
			$this->reviews = PolicyMS_Review::get_reviews( $this, $page );
		}
		return $this->reviews;
	}

	/**
	 * Parse the API response fields to populate `PolicyMS_Description` properties.
	 *
	 * @param array $description The description data.
	 * @throws PolicyMSInvalidDataException When the ID cannot be found.
	 *
	 * @since 1.1.0
	 */
	protected function match_field( array $description ) {

		// Check for existing IDs.
		if ( empty( $description['id'] ) ) {
			if ( empty( $description['_id'] ) ) {
				throw new PolicyMSInvalidDataException(
					'The ID of the description was not found.'
				);
			} else {
				$this->id = $description['_id'];
			}
		} else {
			$this->id = $description['id'];
		}

		// Populate main variables.
		if ( empty( $description['info'] || empty( $description['metadata'] ) || empty( $description['main_image'] ) ) ) {
			throw new PolicyMSInvalidDataException(
				'The description did not match the expected schema.'
			);
		} else {
			$this->type                = $description['info']['type'];
			$this->information         = $description['info'];
			$this->links               = $description['links'] ?? null;
			$this->metadata            = $description['metadata'];
			$this->image_id            = $description['main_image'];
			$this->url                 = PolicyMS_Public::get_setting( true, 'description_page' )
				. '?did=' . $description['id'];
			$this->cover_thumbnail_url = PolicyMS_Public::get_setting( true, 'marketplace_host' )
				. '/descriptions/image/' . $description['id'];
		}

		// Populate assets.
		if ( ! empty( $description['assets'] ) ) {
			$this->assets = array();
			foreach ( $description['assets'] as $category => $assets ) {
				$this->assets[ $category ] = array();
				foreach ( $assets as $asset ) {
					array_push(
						$this->assets[ $category ],
						new PolicyMS_Asset(
							$asset['id'],
							$category,
							$asset
						)
					);
				}
			}
		}

		// Populate user created review.
		if ( ! empty( $description['your_review'][0] ) ) {
			$this->user_review = new PolicyMS_Review(
				$description['your_review'][0]['comment'],
				$description['your_review'][0]['rating'],
				$description['id'],
				$description['your_review'][0]['uid'],
				$description['your_review'][0]['reviewer'],
				$description['your_review'][0]['updated_review_date'],
				$description['your_review'][0]['review_version'],
			);
		}
	}

	/**
	 * Create a new description.
	 *
	 * @param array $information The new description's information.
	 *
	 * @return string The new description's ID.
	 */
	public static function create( array $information ): string {
		return PolicyMS_Communication_Controller::api_request(
			'POST',
			'/descriptions/' . $information['type'],
			$information,
			PolicyMS_Account::retrieve_token()
		)['id'];
	}
}
