<?php

use PolicyCloud_Marketplace_Review as GlobalPolicyCloud_Marketplace_Review;

require_once plugin_dir_path(dirname(__FILE__)) . 'public/class-policycloud-marketplace-description.php';
require_once plugin_dir_path(dirname(__FILE__)) . 'public/class-policycloud-marketplace-user.php';

class PolicyCloud_Marketplace_Review
{

    public string $title;
    public string $comment;
    public int $rating;
    public string $description_id;
    public string $user_id;
    public string $creation_date;
    public string $update_date;
    public int $version;
    public string $collection;

    public function __construct(
        string $title,
        string $comment,
        int $rating,
        string $description_id,
        string $user_id,
        string $creation_date,
        string $update_date,
        int $version,
        string $collection
    ) {
        $this->title = $title;
        $this->comment = $comment;
        $this->rating = $rating;
        $this->description_id = $description_id;
        $this->user_id = $user_id;
        $this->creation_date = $creation_date;
        $this->update_date = $update_date;
        $this->version = $version;
        $this->collection = $collection;
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
                                    $review['title'],
                                    $review['comment'],
                                    $review['rating'],
                                    $review['did'],
                                    $review['username'],
                                    $review['initial_review_date'],
                                    $review['updated_review_date'],
                                    $review['review_version'],
                                    $review['collection']
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
                                $review['title'],
                                $review['comment'],
                                $review['rating'],
                                $review['did'],
                                $review['username'],
                                $review['initial_review_date'],
                                $review['updated_review_date'],
                                $review['review_version'],
                                $review['collection']
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

    public static function get_reviews(PolicyCloud_Marketplace_Description $description, string $filters = null)
    {
        $response = PolicyCloud_Marketplace::api_request(
            'GET',
            '/descriptions/reviews/' . $description->id . $filters,
            [],
            PolicyCloud_Marketplace_Account::retrieve_token(),
        );

        return self::parse($response, false);
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
