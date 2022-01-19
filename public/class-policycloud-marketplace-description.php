<?php

require_once plugin_dir_path(dirname(__FILE__)) . 'public/class-policycloud-marketplace-asset.php';
require_once plugin_dir_path(dirname(__FILE__)) . 'public/class-policycloud-marketplace-review.php';

class PolicyCloud_Marketplace_Description
{

    public string $id;

    public string $type;

    public array $information;

    public ?array $links;

    public string $image_id;

    public array $metadata;

    public ?array $assets;

    public ?PolicyCloud_Marketplace_Review $user_review;

    public function __construct(string $id, ?array $fetched = null)
    {

        if (empty($fetched)) {
            $response = PolicyCloud_Marketplace::api_request(
                'GET',
                '/descriptions/all/' . $id,
                [],
                PolicyCloud_Marketplace_User::is_authenticated() ?
                    PolicyCloud_Marketplace_Account::retrieve_token() :
                    null
            );

            $this->match_field($response['results'][0][0]);
        } else {
            $this->match_field($fetched);
        }
    }

    public function update(array $information, ?array $file_identifiers = null)
    {
        if (!empty($file_identifiers)) {
            foreach ($file_identifiers as $file_id) {
                // Check for new files.
                if (
                    $file_id == 'files' ||
                    $file_id == 'images' ||
                    $file_id == 'videos'
                ) {
                    PolicyCloud_Marketplace_Asset::create(
                        $file_id,
                        $this
                    );
                } elseif (
                    substr($file_id, 0, 6) === "files-" ||
                    substr($file_id, 0, 7) === "images-" ||
                    substr($file_id, 0, 7) === "videos-"
                ) {
                    foreach ($this->assets as $category => $assets) {
                        $file_category = explode('-', $file_id)[0];
                        if ($category == $file_category) {
                            foreach ($assets as $asset) {
                                $id = explode('-', $file_id, 2)[1];
                                if ($asset->id == $id) {
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
            [
                "title" => $information['title'],
                "type" => $information['type'],
                "subtype" => $information['subtype'],
                "owner" => $information['owner'],
                "description" => stripslashes($information['description']),
                "links" => PolicyCloud_Marketplace_User::implode_urls(
                    $information['links-title'],
                    $information['links-url']
                ),
                "fieldOfUse" => $information['fieldOfUse'],
                "comments" => $information['comments']
            ];

        PolicyCloud_Marketplace::api_request(
            'PUT',
            '/descriptions/all/' . $this->id,
            $data,
            PolicyCloud_Marketplace_Account::retrieve_token()
        );
    }

    public function delete()
    {
        PolicyCloud_Marketplace::api_request(
            'DELETE',
            '/descriptions/all/' . $this->id,
            [],
            PolicyCloud_Marketplace_Account::retrieve_token()
        );
    }

    public function is_provider(PolicyCloud_Marketplace_User $provider)
    {
        return $this->metadata['provider'] == $provider->id;
    }

    public function approve(string $decision)
    {
        PolicyCloud_Marketplace::api_request(
            'POST',
            '/descriptions/permit/all/' . $this->id,
            [],
            PolicyCloud_Marketplace_Account::retrieve_token(),
            [
                'x-access-token: ' . PolicyCloud_Marketplace_Account::retrieve_token(),
                'x-permission: ' . $decision
            ]
        );
    }

    public static function set_default_image(string $description_id, string $image_id)
    {
        PolicyCloud_Marketplace::api_request(
            'PUT',
            '/descriptions/image/' . $description_id . '/' . $image_id,
            [],
            PolicyCloud_Marketplace_Account::retrieve_token()
        );
    }

    public static function remove_default_image(string $description_id)
    {
        PolicyCloud_Marketplace::api_request(
            'DELETE',
            '/descriptions/image/' . $description_id,
            [],
            PolicyCloud_Marketplace_Account::retrieve_token()
        );
    }

    public function get_reviews(int $page = 1)
    {
        if (empty($this->reviews)) {
            $this->reviews = PolicyCloud_Marketplace_Review::get_reviews($this, $page);
        }
        return $this->reviews;
    }

    protected function match_field(array $description)
    {
        if (empty($description['id'])) {
            if (empty($description['_id'])) {
                throw new PolicyCloudMarketplaceInvalidDataException(
                    "The ID of the description was not found."
                );
            } else {
                $this->id = $description['_id'];
            }
        } else {
            $this->id = $description['id'];
        }

        if (empty($description['info'] || empty($description['metadata']) || empty($description['main_image']))) {
            throw new PolicyCloudMarketplaceInvalidDataException(
                "The description did not match the expected schema."
            );
        } else {
            $this->type = $description['info']['type'];
            $this->information = $description['info'];
            $this->links = $description['links'] ?? null;
            $this->metadata = $description['metadata'];
            $this->image_id = $description['main_image'];
        }

        if (!empty($description['assets'])) {
            $this->assets = [];
            foreach ($description['assets'] as $category => $assets) {
                $this->assets[$category] = [];
                foreach ($assets as $asset) {
                    array_push(
                        $this->assets[$category],
                        new PolicyCloud_Marketplace_Asset(
                            $asset['id'],
                            $category,
                            $asset
                        )
                    );
                }
            }
        }

        if (!empty($description['your_review'][0])) {
            $this->user_review = new PolicyCloud_Marketplace_Review(
                $description['your_review'][0]['comment'],
                $description['your_review'][0]['rating'],
                $description['id'],
                $description['your_review'][0]['username'],
                $description['your_review'][0]['updated_review_date'],
                $description['your_review'][0]['review_version'],
            );
        }
    }

    protected static function parse(array $response, bool $specify_pages = true, string $container_key = 'results')
    {
        $descriptions = [];
        if (isset($response[$container_key])) {
            foreach ($response[$container_key] as $number => $page) {
                $descriptions[$number] = [];
                foreach ($page as $description) {
                    $descriptions[$number][] = new self($description['id'], $description);
                }
            }
            if ($specify_pages) {
                return [
                    'pages' => $response['pages'],
                    'content' => $descriptions
                ];
            } else {
                return $descriptions;
            }
        } else {
            return [];
        }
    }

    protected static function parse_filter_query(bool $pagination = true)
    {

        // Check arguments
        if (!empty($_GET['sort-by'])) {
            if (
                $_GET['sort-by'] != 'newest' &&
                $_GET['sort-by'] != 'oldest' &&
                $_GET['sort-by'] != 'rating-asc' &&
                $_GET['sort-by'] != 'rating-desc' &&
                $_GET['sort-by'] != 'views-asc' &&
                $_GET['sort-by'] != 'views-desc' &&
                $_GET['sort-by'] != 'title'
            ) {
                throw new PolicyCloudMarketplaceInvalidDataException(
                    'The ' . $_GET['sort-by'] . ' sorting setting was not found.'
                );
            }
        }

        // Page parameter.
        $page = ($pagination) ? (filter_var($_GET['descriptions-page'] ?? 1, FILTER_SANITIZE_NUMBER_INT)) : null;

        // Provider parameter.
        $provider = '';
        if (empty($_GET['provider'][0])) {
            $provider = null;
        } else {
            $provider = implode(",", $_GET['provider']);
        }

        return '?' . http_build_query([
            'sortBy' => !empty($_GET['sort-by']) ? sanitize_key($_GET['sort-by']) : null,
            'page' => $page,
            'itemsPerPage' => filter_var($_GET['items-per-page'] ?? 10, FILTER_SANITIZE_NUMBER_INT),
            'info.owner' => !empty($_GET['owner']) ? sanitize_key($_GET['owner']) : null,
            'info.title' => !empty($_GET['search']) ? sanitize_text_field($_GET['search']) : null,
            'info.subtype' => !empty($_GET['subtype']) ? sanitize_key($_GET['subtype']) : null,
            'info.comments.in' => !empty($_GET['comments']) ? sanitize_key($_GET['comments']) : null,
            'info.contact' => !empty($_GET['contact']) ? sanitize_key($_GET['contact']) : null,
            'info.description.in' => !empty($_GET['search']) ? sanitize_text_field($_GET['search']) : null,
            'info.fieldOfUse' => !empty($_GET['field-of-use']) ? sanitize_key($_GET['field-of-use']) : null,
            'metadata.provider' => $provider,
            'metadata.uploadDate.gte' => !empty($_GET['upload-date-gte']) ? $_GET['upload-date-gte'] : null,
            'metadata.uploadDate.lte' => !empty($_GET['upload-date-lte']) ? $_GET['upload-date-lte'] : null,
            'metadata.last_updated_by' => !empty($_GET['last-updated-by']) ? sanitize_key($_GET['last-updated-by']) : null,
            'metadata.views.gte' => !empty($_GET['views-gte']) ? filter_var($_GET['views-gte'], FILTER_VALIDATE_INT) : null,
            'metadata.views.lte' => !empty($_GET['views-lte']) ? filter_var($_GET['views-lte'], FILTER_VALIDATE_INT) : null,
            'metadata.updateDate.gte' => !empty($_GET['update-date-gte']) ? $_GET['update-date-gte'] : null,
            'metadata.updateDate.lte' => !empty($_GET['upload-date-lte']) ? $_GET['upload-date-lte'] : null
        ]);
    }

    public static function get_filters_range()
    {
        return PolicyCloud_Marketplace::api_request(
            'GET',
            '/descriptions/statistics/filtering',
            []
        )['results'];
    }

    public static function get_pending(?string $type = null)
    {
        $token = PolicyCloud_Marketplace_Account::retrieve_token();

        // Get all descriptions.
        if (empty($type)) {
            $response = PolicyCloud_Marketplace::api_request(
                'GET',
                '/descriptions/permit/all?itemsPerPage=5',
                [],
                $token
            );
        }

        // Filtering by type.
        else {
            $response = PolicyCloud_Marketplace::api_request(
                'GET',
                '/descriptions/permit/' . $type,
                [],
                $token
            );
        }

        return self::parse($response, false);
    }

    public static function get_owned(PolicyCloud_Marketplace_User $user, string $token)
    {
        $response = PolicyCloud_Marketplace::api_request(
            'GET',
            '/descriptions/provider/' . $user->id . '/all' . self::parse_filter_query(false),
            [],
            $token
        );

        return self::parse($response, false);
    }

    public static function get_featured()
    {
        $response = PolicyCloud_Marketplace::api_request(
            'GET',
            '/frontend/homepage'
        );

        $featured = [
            'latest' => self::parse($response, false, 'latest'),
            'most_viewed' => self::parse($response, false, 'most_viewed'),
            'statistics' => $response['statistics'],
            'suggestions' => self::parse($response, false, 'suggestions'),
            'top_rated' => self::parse($response, false, 'top_rated')
        ];

        return $featured;
    }

    public static function get_all()
    {
        $filters = self::parse_filter_query();

        if (!empty($_GET['type'])) {
            // Filter by type.
            $response = PolicyCloud_Marketplace::api_request(
                'GET',
                '/descriptions/' . sanitize_key($_GET['type']) . $filters
            );
        } else {
            // Get all descriptions.
            $response = PolicyCloud_Marketplace::api_request(
                'GET',
                '/descriptions/all' . $filters,
            );
        }

        return self::parse($response);
    }

    public static function create(array $information): string
    {
        return PolicyCloud_Marketplace::api_request(
            'POST',
            '/descriptions/' . $information['type'],
            $information,
            PolicyCloud_Marketplace_Account::retrieve_token()
        )['id'];
    }
}
