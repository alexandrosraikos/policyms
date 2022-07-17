<?php
/**
 * The class definition for reviews.
 *
 * @link       https://github.com/alexandrosraikos/policyms/
 * @since      1.1.0
 *
 * @package    PolicyMS
 * @subpackage PolicyMS/model
 */

/**
 * The class definition for reviews.
 *
 * Defines review information and functionality.
 *
 * @package    PolicyMS
 * @subpackage PolicyMS/model
 * @author     Alexandros Raikos <alexandros@araikos.gr>
 */
class PolicyMS_Review {

	/**
	 * Initialize a review object instance.
	 *
	 * @param string  $comment The review comment.
	 * @param int     $rating The review rating.
	 * @param ?string $description_id The associated description ID.
	 * @param string  $uid The revieweing user's ID.
	 * @param string  $reviewer The reviewer's full name.
	 * @param string  $update_date The last updated date.
	 * @param int     $version The review version.
	 * @param ?string $description_title The description's title.
	 * @param ?string $description_collection The description's collection.
	 */
	public function __construct(
		public string $comment,
		public int $rating,
		public ?string $description_id,
		public string $uid,
		public string $reviewer,
		public string $update_date,
		public int $version,
		public ?string $description_title = null,
		public ?string $description_collection = null
	) {
		$this->comment                = $comment;
		$this->rating                 = $rating;
		$this->description_id         = $description_id;
		$this->description_title      = $description_title ?? null;
		$this->description_collection = $description_collection ?? null;
		$this->uid                    = $uid;
		$this->reviewer               = $reviewer;
		$this->update_date            = $update_date;
		$this->version                = $version;
	}

	/**
	 * Parse an array of retrieved review pages.
	 *
	 * @param array $fetched The fetched review API data.
	 * @param bool  $specify_pages Whether to include the number of pages in the root level.
	 * @return array An array of reviews and, if specified, page numbers.
	 *
	 * @since 1.1.0
	 */
	protected static function parse( array $fetched, bool $specify_pages = true ): array {
		if ( empty( $fetched['results'] ) ) {
			return array();
		}

		if ( $specify_pages ) {
			// Include the number of pages.
			return array(
				'content' => array_map(
					function ( $page ) {
						return array_map(
							function ( $review ) {
								return new PolicyMS_Review(
									$review['comment'],
									$review['rating'],
									$review['did'] ?? null,
									$review['uid'],
									$review['reviewer'],
									$review['updated_review_date'],
									$review['review_version'],
									$review['title'] ?? null,
									$review['collection'] ?? null
								);
							},
							$page
						);
					},
					$fetched['results']
				),
				'pages'   => $fetched['pages'],
			);
		} else {
			// Just get the content.
			return array_map(
				function ( $page ) {
					return array_map(
						function ( $review ) {
							return new PolicyMS_Review(
								$review['comment'],
								$review['rating'],
								$review['did'] ?? null,
								$review['uid'],
								$review['reviewer'],
								$review['updated_review_date'],
								$review['review_version'],
								$review['title'] ?? null,
								$review['collection'] ?? null
							);
						},
						$page
					);
				},
				$fetched['results']
			);
		}
	}

	/**
	 * Get all reviews made by a specific user.
	 *
	 * @param PolicyMS_User $user The user.
	 * @return array A formatted array of reviews.
	 *
	 * @since 1.1.0
	 */
	public static function get_owned( PolicyMS_User $user ): array {
		$response = PolicyMS_Communication_Controller::api_request(
			'GET',
			'/descriptions/review/' . $user->id,
			array(),
			$user->token
		);

		return self::parse( $response, true );
	}

	/**
	 * Get the reviews of a specific description.
	 *
	 * @param PolicyMS_Description $description The description.
	 * @param int                  $page The page number, if specified.
	 * @return array A formatted array of reviews.
	 *
	 * @since 1.1.0
	 */
	public static function get_reviews( PolicyMS_Description $description, int $page = 1 ): array {
		$response = PolicyMS_Communication_Controller::api_request(
			'GET',
			'/descriptions/reviews/' . $description->id . '?itemsPerPage=5&page=' . $page,
			array(),
			PolicyMS_Account::retrieve_token(),
		);

		return self::parse( $response, true );
	}

	/**
	 * Update a review's content.
	 *
	 * @param string $description_id The ID of the description.
	 * @param int    $rating The rating.
	 * @param string $comment The comment.
	 *
	 * @since 1.1.0
	 */
	public static function update( string $description_id, int $rating, string $comment ) {
		PolicyMS_Communication_Controller::api_request(
			'PUT',
			'/descriptions/review/' . $description_id,
			array(
				'rating'  => $rating,
				'comment' => $comment,
			),
			PolicyMS_Account::retrieve_token(),
		);
	}

	/**
	 * Create a new review.
	 *
	 * @param string $description_id The ID of the description.
	 * @param int    $rating The rating.
	 * @param string $comment The comment.
	 *
	 * @since 1.1.0
	 */
	public static function create( string $description_id, int $rating, string $comment ) {
		PolicyMS_Communication_Controller::api_request(
			'POST',
			'/descriptions/review/' . $description_id,
			array(
				'rating'  => $rating,
				'comment' => $comment,
			),
			PolicyMS_Account::retrieve_token(),
		);
	}

	/**
	 * Delete a review.
	 *
	 * @param string $description_id The ID of the description.
	 * @param string $author_id The user ID of the author.
	 *
	 * @since 1.1.0
	 */
	public static function delete( string $description_id, string $author_id ): void {
		PolicyMS_Communication_Controller::api_request(
			'DELETE',
			'/descriptions/review/' . $description_id,
			array(),
			PolicyMS_Account::retrieve_token(),
			array(
				'x-access-token: ' . PolicyMS_Account::retrieve_token(),
				'x-uid: ' . $author_id,
			)
		);
	}

	/**
	 * Get the review author's user ID.
	 *
	 * @since 1.1.0
	 */
	public function get_author(): PolicyMS_User {
		return new PolicyMS_User( $this->uid );
	}

	/**
	 * Get the associated description of the review.
	 *
	 * @return PolicyMS_Description The description object instance.
	 *
	 * @since 1.1.0
	 */
	public function get_description(): PolicyMS_Description {
		return new PolicyMS_Description( $this->description_id );
	}
}
