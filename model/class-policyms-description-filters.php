<?php
/**
 * The class definition for description filters.
 *
 * @link       https://github.com/alexandrosraikos/policyms/
 * @since      2.0.0
 *
 * @package    PolicyMS
 * @subpackage PolicyMS/model
 */

/**
 * The class definition for description filters.
 *
 * Defines description filter information and helper methods.
 *
 * @package    PolicyMS
 * @subpackage PolicyMS/model
 * @author     Alexandros Raikos <alexandros@araikos.gr>
 */
class PolicyMS_Description_Filters {

	/**
	 * The default array of sorting options for description collections.
	 *
	 * @var array
	 *
	 * @since 2.0.0
	 */
	public static array $sorting_options = array(
		'newest',
		'oldest',
		'rating-asc',
		'rating-desc',
		'views-asc',
		'views-desc',
		'title',
	);

	/**
	 * The default array of sizing options for description collections.
	 *
	 * @var array
	 *
	 * @since 2.0.0
	 */
	public static array $sizing_options = array( 12, 30, 60, 90 );

	/**
	 * Initialize a description filter collection.
	 *
	 * @param string $query A search term, if any.
	 * @param string $type A description collection type, if any.
	 * @param int    $views_gte A minimum number of views, if any.
	 * @param ?int   $views_lte A maximum number of views, if any.
	 * @param string $date_gte A minimum description update date, if any.
	 * @param string $date_lte A maximum description update date, if any.
	 */
	public function __construct(
		public string $query = '',
		public string $type = '',
		public int $views_gte = 0,
		public ?int $views_lte = null,
		public ?string $date_gte = null,
		public ?string $date_lte = null
		) {
			$this->query     = $query;
			$this->type      = $type;
			$this->views_gte = $views_gte;
			$this->views_lte = $views_lte ?? '1000+';
			$this->date_gte  = $date_gte ?? gmdate( 'Y-m-d' );
			$this->date_lte  = $date_lte ?? gmdate( 'Y-m-d', 0 );
	}

	/**
	 * Parses the default filter values from the API.
	 *
	 * @since 2.0.0
	 */
	public static function get_defaults() {
		$response = PolicyMS_Communication_Controller::api_request(
			'GET',
			'/descriptions/statistics/filtering',
			array()
		)['results'] ?? array();

		return new self(
			'',
			'',
			0,
			intval( $response['max_views'] ),
			null,
			$response['oldest']
		);
	}


	/**
	 * Parse the filter form values and create an API compatible query.
	 *
	 * @param string $nonce The filtering action (`policyms-description-archive-filtering`) nonce.
	 * @param bool   $pagination Whether to parse page numbers as well.
	 * @return string The API compatible query.
	 * @throws PolicyMSInvalidDataException On unexpected values.
	 * @throws PolicyMSUnauthorizedRequestException On failed nonce verification.
	 *
	 * @since 1.1.0
	 */
	public static function build_query(
		string $nonce,
		bool $pagination = true
	): string {
		if ( wp_verify_nonce( $nonce, 'policyms-description-archive-filtering' ) ) {

			// Verify sorting setting, if any.
			$sorting = null;
			if ( ! empty( $_GET['sort-by'] ) ) {
				$sorting = sanitize_text_field(
					wp_unslash( $_GET['sort-by'] )
				);
				if ( ! in_array( $sorting, self::$sorting_options, true ) ) {
					throw new PolicyMSInvalidDataException(
						'The ' . sanitize_key( $_GET['sort-by'] ) . ' sorting setting was not found.'
					);
				}
			}

			// Verify sorting setting, if any.
			$sizing = null;
			if ( ! empty( $_GET['items-per-page'] ) ) {
				$sizing = (int) $_GET['items-per-page'];
				if ( ! in_array( $sizing, self::$sizing_options, true ) ) {
					throw new PolicyMSInvalidDataException(
						'The ' . (int) $_GET['items-per-page'] . ' page size setting was not found.'
					);
				}
			}

			// Verify and add a default page setting.
			$page = 1;
			if ( ! empty( $_GET['descriptions-page'] ) ) {
				$page = ( $pagination ) ? intval( $_GET['descriptions-page'] ) : 1;
			}

			// Provider parameter.
			$provider = null;
			if ( empty( $_GET['provider'][0] ) ) {
				$provider = '';
			} else {
				// NOTE: Each provider value is sanitized appropriately.
				$provider = implode(
					',',
					array_map(
						fn( $v ) => sanitize_text_field( wp_unslash( $v ), true ),
						(array) $_GET['provider']
					)
				);
			}

			// Owner parameter.
			$owner = null;
			if ( ! empty( $_GET['owner'] ) ) {
				$owner = sanitize_text_field( wp_unslash( $_GET['owner'] ) );
			}

			// Title or description content query.
			$query = null;
			if ( ! empty( $_GET['search'] ) ) {
				$query = sanitize_text_field( wp_unslash( $_GET['search'] ) );
			}

			// Provider parameter.
			$keywords = null;
			if ( empty( $_GET['keywords'][0] ) ) {
				$keywords = '';
			} else {
				// NOTE: Each keyword value is sanitized appropriately.
				$keywords = implode(
					',',
					array_map(
						fn( $v ) => sanitize_text_field( wp_unslash( $v ), true ),
						(array) $_GET['keywords']
					)
				);
			}

			// Date parameters.
			$upload_date_gte = ! empty( $_GET['upload-date-gte'] )
				? sanitize_key( wp_unslash( $_GET['upload-date-gte'] ) )
				: null;
			$upload_date_lte = ! empty( $_GET['upload-date-lte'] )
				? sanitize_key( wp_unslash( $_GET['upload-date-lte'] ) )
				: null;
			$update_date_gte = ! empty( $_GET['update-date-gte'] )
				? sanitize_key( wp_unslash( $_GET['update-date-gte'] ) )
				: null;
			$update_date_lte = ! empty( $_GET['update-date-lte'] )
				? sanitize_key( wp_unslash( $_GET['update-date-lte'] ) )
				: null;

			// Views parameters.
			$views_gte = ! empty( $_GET['views-gte'] )
				? intval( $_GET['views-gte'] )
				: null;
			$views_lte = ! empty( $_GET['views-lte'] )
				? intval( $_GET['views-lte'] )
				: null;

			// TODO @vkoukos: Rename 'Fields of Use' to 'Keywords' (#128).
			return '?' . http_build_query(
				array(
					'sortBy'                   => $sorting,
					'page'                     => $page,
					'itemsPerPage'             => $sizing,
					'info.owner'               => $owner,
					'info.title'               => $query,
					'info.comments.in'         => $query,
					'info.contact'             => $query,
					'info.description.in'      => $query,
					'info.keywords'            => $keywords,
					'metadata.provider'        => $provider,
					'metadata.uploadDate.gte'  => $upload_date_gte,
					'metadata.uploadDate.lte'  => $upload_date_lte,
					'metadata.last_updated_by' => null,
					'metadata.views.gte'       => $views_gte,
					'metadata.views.lte'       => $views_lte,
					'metadata.updateDate.gte'  => $update_date_gte,
					'metadata.updateDate.lte'  => $update_date_lte,
				)
			);
		} else {
			throw new PolicyMSUnauthorizedRequestException(
				'The nonce was unable to be verified.'
			);
		}
	}
}
