<?php

class PolicyMS_Description {


	public string $id;

	public string $type;

	public array $information;

	public ?array $links;

	public string $image_id;

	public array $metadata;

	public ?array $assets;

	public ?PolicyMS_Review $user_review;

	public function __construct( string $id, ?array $fetched = null ) {

		if ( empty( $fetched ) ) {
			$response = PolicyMS_Communication_Controller::api_request(
				'GET',
				'/descriptions/all/' . $id,
				array(),
				PolicyMS_User::is_authenticated() ?
					PolicyMS_Account::retrieve_token() :
					null
			);

			$this->match_field( $response['results'][0][0] );
		} else {
			$this->match_field( $fetched );
		}
	}

	public function update( array $information, ?array $file_identifiers = null ) {
		if ( ! empty( $file_identifiers ) ) {
			foreach ( $file_identifiers as $file_id ) {
				// Check for new files.
				if ( $file_id == 'files' ||
					$file_id == 'images' ||
					$file_id == 'videos'
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
						if ( $category == $file_category ) {
							foreach ( $assets as $asset ) {
								$id = explode( '-', $file_id, 2 )[1];
								if ( $asset->id == $id ) {
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

		$data =
			array(
				'title'       => stripslashes( $information['title'] ),
				'type'        => $information['type'],
				'subtype'     => strtolower( $information['subtype'] ),
				'owner'       => stripslashes( $information['owner'] ),
				'description' => stripslashes( $information['description'] ),
				'links'       => PolicyMS_User::implode_urls(
					$information['links-title'],
					$information['links-url']
				),
				'fieldOfUse'  => $information['fieldOfUse'],
				'comments'    => stripslashes( $information['comments'] ),
			);

		PolicyMS_Communication_Controller::api_request(
			'PUT',
			'/descriptions/all/' . $this->id,
			$data,
			PolicyMS_Account::retrieve_token()
		);
	}

	public function delete() {
		PolicyMS_Communication_Controller::api_request(
			'DELETE',
			'/descriptions/all/' . $this->id,
			array(),
			PolicyMS_Account::retrieve_token()
		);
	}

	public function is_provider( PolicyMS_User $provider ) {
		return $this->metadata['provider'] == $provider->id;
	}

	public function approve( string $decision ) {
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

	public static function set_default_image( string $description_id, string $image_id ) {
		PolicyMS_Communication_Controller::api_request(
			'PUT',
			'/descriptions/image/' . $description_id . '/' . $image_id,
			array(),
			PolicyMS_Account::retrieve_token()
		);
	}

	public static function remove_default_image( string $description_id ) {
		PolicyMS_Communication_Controller::api_request(
			'DELETE',
			'/descriptions/image/' . $description_id,
			array(),
			PolicyMS_Account::retrieve_token()
		);
	}

	public function get_reviews( int $page = 1 ) {
		if ( empty( $this->reviews ) ) {
			$this->reviews = PolicyMS_Review::get_reviews( $this, $page );
		}
		return $this->reviews;
	}

	protected function match_field( array $description ) {
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

		if ( empty( $description['info'] || empty( $description['metadata'] ) || empty( $description['main_image'] ) ) ) {
			throw new PolicyMSInvalidDataException(
				'The description did not match the expected schema.'
			);
		} else {
			$this->type        = $description['info']['type'];
			$this->information = $description['info'];
			$this->links       = $description['links'] ?? null;
			$this->metadata    = $description['metadata'];
			$this->image_id    = $description['main_image'];
		}

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

	protected static function parse( array $response, bool $specify_pages = true, string $container_key = 'results' ) {
		$descriptions = array();
		if ( isset( $response[ $container_key ] ) ) {
			foreach ( $response[ $container_key ] as $number => $page ) {
				$descriptions[ $number ] = array();
				foreach ( $page as $description ) {
					$descriptions[ $number ][] = new self( $description['id'], $description );
				}
			}
			if ( $specify_pages ) {
				return array(
					'pages'   => $response['pages'],
					'content' => $descriptions,
				);
			} else {
				return $descriptions;
			}
		} else {
			return array();
		}
	}

	protected static function parse_filter_query( bool $pagination = true ) {

		// Check arguments
		if ( ! empty( $_GET['sort-by'] ) ) {
			if ( $_GET['sort-by'] != 'newest' &&
				$_GET['sort-by'] != 'oldest' &&
				$_GET['sort-by'] != 'rating-asc' &&
				$_GET['sort-by'] != 'rating-desc' &&
				$_GET['sort-by'] != 'views-asc' &&
				$_GET['sort-by'] != 'views-desc' &&
				$_GET['sort-by'] != 'title'
			) {
				throw new PolicyMSInvalidDataException(
					'The ' . $_GET['sort-by'] . ' sorting setting was not found.'
				);
			}
		}

		// Page parameter.
		$page = ( $pagination ) ? ( filter_var( $_GET['descriptions-page'] ?? 1, FILTER_SANITIZE_NUMBER_INT ) ) : null;

		// Provider parameter.
		$provider = '';
		if ( empty( $_GET['provider'][0] ) ) {
			$provider = null;
		} else {
			$provider = implode( ',', $_GET['provider'] );
		}

		return '?' . http_build_query(
			array(
				'sortBy'                   => ! empty( $_GET['sort-by'] ) ? sanitize_key( $_GET['sort-by'] ) : null,
				'page'                     => $page,
				'itemsPerPage'             => filter_var( $_GET['items-per-page'] ?? 10, FILTER_SANITIZE_NUMBER_INT ),
				'info.owner'               => ! empty( $_GET['owner'] ) ? sanitize_key( $_GET['owner'] ) : null,
				'info.title'               => ! empty( $_GET['search'] ) ? sanitize_text_field( $_GET['search'] ) : null,
				'info.subtype'             => ! empty( $_GET['subtype'] ) ? sanitize_key( $_GET['subtype'] ) : null,
				'info.comments.in'         => ! empty( $_GET['comments'] ) ? sanitize_key( $_GET['comments'] ) : null,
				'info.contact'             => ! empty( $_GET['contact'] ) ? sanitize_key( $_GET['contact'] ) : null,
				'info.description.in'      => ! empty( $_GET['search'] ) ? sanitize_text_field( $_GET['search'] ) : null,
				'info.fieldOfUse'          => ! empty( $_GET['field-of-use'] ) ? sanitize_key( $_GET['field-of-use'] ) : null,
				'metadata.provider'        => $provider,
				'metadata.uploadDate.gte'  => ! empty( $_GET['upload-date-gte'] ) ? $_GET['upload-date-gte'] : null,
				'metadata.uploadDate.lte'  => ! empty( $_GET['upload-date-lte'] ) ? $_GET['upload-date-lte'] : null,
				'metadata.last_updated_by' => ! empty( $_GET['last-updated-by'] ) ? sanitize_key( $_GET['last-updated-by'] ) : null,
				'metadata.views.gte'       => ! empty( $_GET['views-gte'] ) ? filter_var( $_GET['views-gte'], FILTER_VALIDATE_INT ) : null,
				'metadata.views.lte'       => ! empty( $_GET['views-lte'] ) ? filter_var( $_GET['views-lte'], FILTER_VALIDATE_INT ) : null,
				'metadata.updateDate.gte'  => ! empty( $_GET['update-date-gte'] ) ? $_GET['update-date-gte'] : null,
				'metadata.updateDate.lte'  => ! empty( $_GET['upload-date-lte'] ) ? $_GET['upload-date-lte'] : null,
			)
		);
	}

	public static function get_filters_range() {
		return PolicyMS_Communication_Controller::api_request(
			'GET',
			'/descriptions/statistics/filtering',
			array()
		)['results'] ?? array();
	}

	public static function get_pending( ?string $type = null ) {
		$token = PolicyMS_Account::retrieve_token();

		// Get all descriptions.
		if ( empty( $type ) ) {
			$response = PolicyMS_Communication_Controller::api_request(
				'GET',
				'/descriptions/permit/all?itemsPerPage=5',
				array(),
				$token
			);
		}

		// Filtering by type.
		else {
			$response = PolicyMS_Communication_Controller::api_request(
				'GET',
				'/descriptions/permit/' . $type,
				array(),
				$token
			);
		}

		return self::parse( $response, false );
	}

	public static function get_owned( PolicyMS_User $user, string $token ) {
		$response = PolicyMS_Communication_Controller::api_request(
			'GET',
			'/descriptions/provider/' . $user->id . '/all' . self::parse_filter_query( false ),
			array(),
			$token
		);

		return self::parse( $response, false );
	}

	public static function get_featured() {
		$response = PolicyMS_Communication_Controller::api_request(
			'GET',
			'/frontend/homepage'
		);

		$featured = array(
			'latest'      => self::parse( $response, false, 'latest' ),
			'most_viewed' => self::parse( $response, false, 'most_viewed' ),
			'statistics'  => $response['statistics'],
			'suggestions' => self::parse( $response, false, 'suggestions' ),
			'top_rated'   => self::parse( $response, false, 'top_rated' ),
		);

		return $featured;
	}

	public static function get_all() {
		$filters = self::parse_filter_query();

		if ( ! empty( $_GET['type'] ) ) {
			// Filter by type.
			$response = PolicyMS_Communication_Controller::api_request(
				'GET',
				'/descriptions/' . sanitize_key( $_GET['type'] ) . $filters
			);
		} else {
			// Get all descriptions.
			$response = PolicyMS_Communication_Controller::api_request(
				'GET',
				'/descriptions/all' . $filters,
			);
		}

		return self::parse( $response );
	}

	public static function create( array $information ): string {
		return PolicyMS_Communication_Controller::api_request(
			'POST',
			'/descriptions/' . $information['type'],
			$information,
			PolicyMS_Account::retrieve_token()
		)['id'];
	}
}
