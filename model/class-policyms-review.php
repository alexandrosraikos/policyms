<?php

class PolicyMS_Review {


	public string $comment;
	public int $rating;
	public ?string $description_id;
	public ?string $description_title;
	public ?string $description_collection;
	public string $uid;
	public string $reviewer;
	public string $update_date;
	public int $version;

	public function __construct(
		string $comment,
		int $rating,
		?string $description_id,
		string $uid,
		string $reviewer,
		string $update_date,
		int $version,
		string $description_title = null,
		string $description_collection = null
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

	protected static function parse( array $fetched, bool $specify_pages = true ): array {
		if ( empty( $fetched['results'] ) ) {
			return array();
		}

		if ( $specify_pages ) {
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

	public static function get_owned( PolicyMS_User $user, string $token ) {
		$response = PolicyMS_Communication_Controller::api_request(
			'GET',
			'/descriptions/review/' . $user->id,
			array(),
			$token
		);

		return self::parse( $response, false );
	}

	public static function get_reviews( PolicyMS_Description $description, int $page = 1 ) {
		$response = PolicyMS_Communication_Controller::api_request(
			'GET',
			'/descriptions/reviews/' . $description->id . '?itemsPerPage=5&page=' . $page,
			array(),
			PolicyMS_Account::retrieve_token(),
		);

		return self::parse( $response, true );
	}

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

	public function get_author(): PolicyMS_User {
		return new PolicyMS_User( $this->uid );
	}

	public function get_description(): PolicyMS_Description {
		return new PolicyMS_Description( $this->description_id );
	}
}
