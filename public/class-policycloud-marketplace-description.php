<?php

require_once plugin_dir_path(dirname(__FILE__)) . 'public/class-policycloud-marketplace-asset.php';
require_once plugin_dir_path(dirname(__FILE__)) . 'public/class-policycloud-marketplace-review.php';

class PolicyCloud_Marketplace_Description
{
    public string $id;

    public string $type;

    public array $information;

    public array $metadata;

    public ?array $assets;

    public ?array $reviews;

    public function __construct(string $id, ?array $fetched = [])
    {
        if (empty($fetched)) {

            $response = PolicyCloud_Marketplace::api_request(
                'GET',
                '/descriptions/all/' . $id,
                [],
                PolicyCloud_Marketplace_Account::retrieve_token()
            );

            $this->match_field($response['results'][0]);
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
                    substr($file_id, 0, 5) === "file-" ||
                    substr($file_id, 0, 6) === "image-" ||
                    substr($file_id, 0, 6) === "video-"
                ) {
                    foreach ($this->assets as $category => $assets) {
                        if ($category == explode('-', $file_id, 0)[0] . 's') {
                            foreach ($assets as $type => $asset) {
                                $id = explode('-', $file_id, 0)[1];
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

        PolicyCloud_Marketplace::api_request(
            'PUT',
            '/descriptions/all/' . $this->id,
            [
                "title" => sanitize_text_field($information['title']),
                "type" => sanitize_text_field($information['type']),
                "subtype" => sanitize_text_field($information['subtype'] ?? ''),
                "owner" => sanitize_text_field($information['owner'] ?? ''),
                "description" => sanitize_text_field($information['description']),
                "fieldOfUse" => explode(", ", $information['fields-of-use'] ?? []),
                "comments" => sanitize_text_field($information['comments'] ?? '')
            ],
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

    public function is_provider(PolicyCloud_Marketplace_User $provider) {
        return $this->metadata['provider'] == $provider->id;
    }

    public function get_reviews() {
        
    }

    public function approve(int $decision) {
        PolicyCloud_Marketplace::api_request(
            'POST',
            '/descriptions/permit/all/'.$this->id,
            [],
            PolicyCloud_Marketplace_Account::retrieve_token(),
            [
              'x-access-token: '. PolicyCloud_Marketplace_Account::retrieve_token(),
              'x-permission: '. $decision
            ]
            );
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

        if (
            empty($description['collection']) ||
            empty($description['info'] ||
                empty($description['metadata']))
        ) {
            throw new PolicyCloudMarketplaceInvalidDataException(
                "The description did not match the expected schema."
            );
        } else {
            $this->type = $description['collection'];
            $this->information = $description['information'];
            $this->metadata = $description['metadata'];
        }

        if (!empty($decription['assets'])) {
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
    }

    protected static function parse(array $results): self
    {
        $descriptions = [];
        foreach ($results as $page) {
            foreach ($page as $description) {
                $descriptions[] = new self($description['id'], $description);
            }
        }
        return (count($descriptions) == 1) ? $descriptions[0] : $descriptions;
    }

    protected static function parse_filter_query()
    {

        // Check arguments
        if (!empty($_GET['sort-by'])) {
            if (
                $_GET['sort-by'] != 'newest' ||
                $_GET['sort-by'] != 'oldest' ||
                $_GET['sort-by'] != 'rating-asc' ||
                $_GET['sort-by'] != 'rating-desc' ||
                $_GET['sort-by'] != 'views-asc' ||
                $_GET['sort-by'] != 'views-desc' ||
                $_GET['sort-by'] != 'title'
            ) {
                throw new PolicyCloudMarketplaceInvalidDataException(
                    'The ' . $_GET['sort-by'] . ' sorting setting was not found.'
                );
            }
        }

        return '?' . http_build_query([
            'sortBy' => isset($_GET['sort-by']) ? sanitize_key($_GET['sort-by']) : null,
            'page' => isset($_GET['descriptions-page']) ? sanitize_key($_GET['descriptions-page']) : null,
            'itemsPerPage' => filter_var($_GET['items-per-page'] ?? 5, FILTER_SANITIZE_NUMBER_INT),
            'info.owner' => isset($_GET['owner']) ? sanitize_key($_GET['owner']) : null,
            'info.title' => isset($_GET['search']) ? sanitize_text_field($_GET['search']) : null,
            'info.subtype' => isset($_GET['subtype']) ? sanitize_key($_GET['subtype']) : null,
            'info.comments.in' => isset($_GET['comments']) ? sanitize_key($_GET['comments']) : null,
            'info.contact' => isset($_GET['contact']) ? sanitize_key($_GET['contact']) : null,
            'info.description.in' => isset($_GET['search']) ? sanitize_text_field($_GET['search']) : null,
            'info.fieldOfUse' => isset($_GET['field-of-use']) ? sanitize_key($_GET['field-of-use']) : null,
            'metadata.provider' => isset($_GET['provider']) ? sanitize_key($_GET['provider']) : null,
            'metadata.uploadDate.gte' => isset($_GET['upload-date-gte']) ? $_GET['upload-date-gte'] : null,
            'metadata.uploadDate.lte' => isset($_GET['upload-date-lte']) ? $_GET['upload-date-lte'] : null,
            'metadata.last_updated_by' => isset($_GET['last-updated-by']) ? sanitize_key($_GET['last-updated-by']) : null,
            'metadata.views.gte' => isset($_GET['views-gte']) ? filter_var($_GET['views-gte'], FILTER_VALIDATE_INT) : null,
            'metadata.views.lte' => isset($_GET['views-lte']) ? filter_var($_GET['views-lte'], FILTER_VALIDATE_INT) : null,
            'metadata.updateDate.gte' => isset($_GET['update-date-gte']) ? $_GET['update-date-gte'] : null,
            'metadata.updateDate.lte' => isset($_GET['upload-date-lte']) ? $_GET['upload-date-lte'] : null
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

        return self::parse($response['results']);
    }

    public static function get_owned(PolicyCloud_Marketplace_User $user)
    {
        $response = PolicyCloud_Marketplace::api_request(
            'GET',
            '/descriptions/provider/' . $user->id . '/all' . self::parse_filter_query(),
            [],
            $user->token
        );

        return self::parse($response['results']);
    }

    public static function get_all()
    {
        $filters = self::parse_filter_query();

        if (isset($_GET['type'])) {

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

        return self::parse($response['results']);
    }

    public static function create(array $information): int
    {
        return PolicyCloud_Marketplace::api_request(
            'POST',
            '/descriptions/' . $information['type'],
            $information,
            PolicyCloud_Marketplace_Account::retrieve_token()
        )['id'];
    }
}
