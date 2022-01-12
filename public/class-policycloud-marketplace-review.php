<?php
require_once plugin_dir_path(dirname(__FILE__)) . 'public/class-policycloud-marketplace-description.php';
require_once plugin_dir_path(dirname(__FILE__)) . 'public/class-policycloud-marketplace-user.php';

class PolicyCloud_Marketplace_Review
{

    public string $comment;
    public int $rating;
    public ?string $description_id;
    public ?string $description_title;
    public ?string $description_collection;
    public string $user_id;
    public string $update_date;
    public int $version;

    public function __construct(
        string $comment,
        int $rating,
        ?string $description_id,
        string $user_id,
        string $update_date,
        int $version,
        string $description_title = null,
        string $description_collection = null
    ) {
        $this->comment = $comment;
        $this->rating = $rating;
        $this->description_id = $description_id;
        $this->description_title = $description_title ?? null;
        $this->description_collection = $description_collection ?? null;
        $this->user_id = $user_id;
        $this->update_date = $update_date;
        $this->version = $version;
    }

    protected static function parse(array $fetched, bool $specify_pages = true): array
    {
        if (empty($fetched['results'])) {
            return [];
        }

        if ($specify_pages) {
            return [
                'content' => array_map(
                    function ($page) {
                        return array_map(
                            function ($review) {
                                return new PolicyCloud_Marketplace_Review(
                                    $review['comment'],
                                    $review['rating'],
                                    $review['did'] ?? null,
                                    $review['username'],
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
                'pages' => $fetched['pages']
            ];
        } else {
            return array_map(
                function ($page) {
                    return array_map(
                        function ($review) {
                            return new PolicyCloud_Marketplace_Review(
                                $review['comment'],
                                $review['rating'],
                                $review['did'] ?? null,
                                $review['username'],
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

    public static function get_owned(PolicyCloud_Marketplace_User $user, string $token)
    {
        $response = PolicyCloud_Marketplace::api_request(
            'GET',
            '/descriptions/review/' . $user->id,
            [],
            $token
        );

        return self::parse($response, false);
    }

    public static function get_reviews(PolicyCloud_Marketplace_Description $description, int $page = 1)
    {
        $response = PolicyCloud_Marketplace::api_request(
            'GET',
            '/descriptions/reviews/' . $description->id . '?itemsPerPage=5&page=' . $page,
            [],
            PolicyCloud_Marketplace_Account::retrieve_token(),
        );

        return self::parse($response, true);
    }

    public static function update(string $description_id, int $rating, string $comment)
    {
        PolicyCloud_Marketplace::api_request(
            'PUT',
            '/descriptions/review/' . $description_id,
            [
                'rating' => $rating,
                'comment' => $comment
            ],
            PolicyCloud_Marketplace_Account::retrieve_token(),
        );
    }

    public static function create(string $description_id, int $rating, string $comment)
    {
        PolicyCloud_Marketplace::api_request(
            'POST',
            '/descriptions/review/' . $description_id,
            [
                'rating' => $rating,
                'comment' => $comment
            ],
            PolicyCloud_Marketplace_Account::retrieve_token(),
        );
    }

    public static function delete(string $description_id, string $author_id): void
    {
        PolicyCloud_Marketplace::api_request(
            'DELETE',
            '/descriptions/review/' . $description_id,
            [],
            PolicyCloud_Marketplace_Account::retrieve_token(),
            [
                'x-access-token: '.PolicyCloud_Marketplace_Account::retrieve_token(),
                'x-username: '.$author_id
            ]
        );
    }

    public function get_author(): PolicyCloud_Marketplace_User
    {
        return new PolicyCloud_Marketplace_User($this->user_id);
    }

    public function get_description(): PolicyCloud_Marketplace_Description
    {
        return new PolicyCloud_Marketplace_Description($this->description_id);
    }
}
