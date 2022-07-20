<?php
/**
 * The class definition for description collections.
 *
 * @link       https://github.com/alexandrosraikos/policyms/
 * @since      2.0.0
 *
 * @package    PolicyMS
 * @subpackage PolicyMS/model
 */

/**
 * The class definition for description collections.
 *
 * Defines description collection properties and helper methods.
 *
 * @package    PolicyMS
 * @subpackage PolicyMS/model
 * @author     Alexandros Raikos <alexandros@araikos.gr>
 */
class PolicyMS_Description_Collection {

	/**
	 * Whether the collection is partitioned into pages.
	 *
	 * @var bool The collection pagination state.
	 *
	 * @since 2.0.0
	 */
	public bool $is_paginated = false;

	/**
	 * The total number of pages in the collection, if it's paginated.
	 *
	 * @var int
	 *
	 * @since 2.0.0
	 */
	public int $total_pages = 1;

	/**
	 * Initialize a description collection object instance.
	 *
	 * @param array $descriptions The array of `PolicyMS_Descriptions` instances.
	 * @param int   $total_pages The total number of pages, if any is specified.
	 *
	 * @since 2.0.0
	 */
	public function __construct(
			public array $descriptions,
			int $total_pages = 1
		) {
		$this->descriptions = $descriptions;
		// Check if top level values are directly descriptions.
		if ( $descriptions ) {
			$this->is_paginated = ! is_a(
				$descriptions[0],
				'PolicyMS_Description'
			);
		}
		$this->total_pages = ( $this->is_paginated ) ? $total_pages : 1;
	}

	/**
	 * Get a specific page of the collection.
	 *
	 * @param int $page The page number.
	 * @throws PolicyMSInvalidDataException If the page number doesn't exist.
	 *
	 * @since 2.0.0
	 */
	public function get_page( int $page = 1 ) {
		if ( $page > $this->total_pages || $page < 1 ) {
			throw new PolicyMSInvalidDataException(
				'The page requested is invalid.'
			);
		}
		if ( $this->is_paginated ) {
			return $this->descriptions[ $page - 1 ];
		} else {
			return $this->descriptions;
		}
	}

	/**
	 * Create a description collection from a raw API response.
	 *
	 * @param array  $response The API response.
	 * @param bool   $specify_pages Whether to include thee number of total pages.
	 * @param string $container_key The API response container array key for
	 * the description content.
	 *
	 * @return ?self The parsed description collection object.
	 *
	 * @since 2.0.0
	 */
	protected static function parse(
		array $response,
		bool $specify_pages = true,
		string $container_key = 'results'
	): ?self {
		$descriptions = array();
		if ( isset( $response[ $container_key ] ) ) {
			if ( $specify_pages ) {
				foreach ( $response[ $container_key ] as $number => $page ) {
					$descriptions[ $number ] = array();
					foreach ( $page as $description ) {
						$descriptions[ $number ][] = new PolicyMS_Description( $description['id'], $description );
					}
				}
				return new PolicyMS_Description_Collection(
					$descriptions,
					$response['pages']
				);
			} else {
				foreach ( $response[ $container_key ] as $number => $page ) {
					$descriptions = array();
					foreach ( $page as $description ) {
						$descriptions[] = new PolicyMS_Description( $description['id'], $description );
					}
				}
				return new PolicyMS_Description_Collection(
					$descriptions
				);
			}
		} else {
			return null;
		}
	}

	/**
	 * Get a collection of descriptions pending for approval.
	 * (Administrators only)
	 *
	 * @param ?string $type Specify the type.
	 * @return self
	 *
	 * @since 2.0.0
	 */
	public static function get_pending( ?string $type = null ): self {
		$token = PolicyMS_Account::retrieve_token();

		// Get all descriptions.
		if ( empty( $type ) ) {
			$response = PolicyMS_Communication_Controller::api_request(
				'GET',
				'/descriptions/permit/all?itemsPerPage=5',
				array(),
				$token
			);
		} else {
			$response = PolicyMS_Communication_Controller::api_request(
				'GET',
				'/descriptions/permit/' . $type,
				array(),
				$token
			);
		}

		return self::parse( $response, false );
	}

	/**
	 * Get a collection of a user's own descriptions.
	 *
	 * @param PolicyMS_User $user The owner of all the descriptions.
	 * @return self The owned descriptions.
	 *
	 * @since 2.0.0
	 */
	public static function get_owned( PolicyMS_User $user ): self {
		$response = PolicyMS_Communication_Controller::api_request(
			'GET',
			'/descriptions/provider/' . $user->id . '/all' . PolicyMS_Description_Filters::build_query( false ),
			array(),
			$user->token
		);

		return self::parse( $response, true );
	}

	/**
	 * Get a meta-collection of featured descriptions and statical data.
	 *
	 * @return array The formatted meta-collection array.
	 *
	 * @since 2.0.0
	 */
	public static function get_featured(): array {
		$response = PolicyMS_Communication_Controller::api_request(
			'GET',
			'/frontend/homepage'
		);

		$featured = array(
			'statistics'  => $response['statistics'],
			'latest'      => self::parse( $response, false, 'latest' ),
			'most_viewed' => self::parse( $response, false, 'most_viewed' ),
			'suggestions' => self::parse( $response, false, 'suggestions' ),
			'top_rated'   => self::parse( $response, false, 'top_rated' ),
		);

		return $featured;
	}

	/**
	 * Get all the descriptions, with any existing filter queries.
	 *
	 * @param string $category The description category, if any.
	 * @return ?self The description collection.
	 *
	 * @since 2.0.0
	 */
	public static function get_all( string $category = '' ): ?self {
		$filters = PolicyMS_Description_Filters::build_query();

		if ( $category ) {
			// Filter by type.
			$response = PolicyMS_Communication_Controller::api_request(
				'GET',
				'/descriptions/' . $category . $filters
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
}
