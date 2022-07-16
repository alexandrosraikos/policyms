<?php
/**
 * The class definition for description filters.
 *
 * @link       https://github.com/alexandrosraikos/policyms/
 * @since      1.1.0
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
	 * @param bool $pagination Whether to parse page numbers as well.
	 * @return string The API compatible query.
	 * @throws PolicyMSInvalidDataException On unexpected values.
	 *
	 * @since 1.1.0
	 */
	public static function build_query( bool $pagination = true ): string {
		// Check arguments.
		if ( ! empty( $_GET['sort-by'] ) ) {
			if ( 'newest' !== $_GET['sort-by'] &&
				'oldest' !== $_GET['sort-by'] &&
				'rating-asc' !== $_GET['sort-by'] &&
				'rating-desc' !== $_GET['sort-by'] &&
				'views-asc' !== $_GET['sort-by'] &&
				'views-desc' !== $_GET['sort-by'] &&
				'title' !== $_GET['sort-by']
			) {
				throw new PolicyMSInvalidDataException(
					'The ' . sanitize_key( $_GET['sort-by'] ) . ' sorting setting was not found.'
				);
			}
		}

		// Page parameter.
		$page = ( $pagination )
		? (
			filter_var(
				wp_unslash( $_GET['descriptions-page'] ?? 1 ),
				FILTER_SANITIZE_NUMBER_INT
			)
		)
		: null;

		// Provider parameter.
		$provider = '';
		if ( empty( $_GET['provider'][0] ) ) {
			$provider = null;
		} else {
			$provider = implode( ',', $_GET['provider'] );
		}
		// TODO @alexandrosraikos: Remove 'subtype' entirely. (#128)
		// TODO @alexandrosraikos: Rename 'Fields of Use' to 'Keywords'. (#128)
		return '?' . http_build_query(
			array(
				'sortBy'                   => ! empty( $_GET['sort-by'] ) ? sanitize_key( $_GET['sort-by'] ) : null,
				'page'                     => $page,
				'itemsPerPage'             => filter_var( wp_unslash( $_GET['items-per-page'] ?? 10 ), FILTER_SANITIZE_NUMBER_INT ),
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
}
