<?php

if (!function_exists("policyCloudMarketplaceAPIRequest")) {
    require_once plugin_dir_path(dirname(__FILE__)) . 'public/partials/policycloud-marketplace-accounts.php';
}

/**
 * Retrieve publicly available Assets from the Marketplace API, also filtered by collection.
 *
 * To learn more about the Marketplace API data schema for retrieving objects and filtering them, visit:
 * https://documenter.getpostman.com/view/16776360/TzsZs8kn#6c8e45e3-5be6-4c10-82a6-7d698b092e9e
 *
 * @param	array $args An array of arguments to filter the search.
 * 
 * @uses    policyCloudMarketplaceAPIRequest()
 * 
 * @since   1.0.0
 * @author  Alexandros Raikos <araikos@unipi.gr>
 */
function get_assets(array $args)
{

    $filters = '?' . http_build_query([
        'page' => !empty($args['assets-page']) ? $args['assets-page'] : 1,
        'sortBy' => !empty($args['sort-by']) ? $args['sort-by'] : 'newest',
        'itemsPerPage' => !empty($args['items-per-page']) ? $args['items-per-page'] : 10,
        'info.owner' => !empty($args['owner']) ? $args['owner'] : null,
        'info.title' => !empty($args['search']) ? $args['search'] : null,
        'info.subtype' => !empty($args['subtype']) ? $args['subtype'] : null,
        'info.comments.in' => !empty($args['comments']) ? $args['comments'] : null,
        'info.contact' => !empty($args['contact']) ? $args['contact'] : null,
        'info.description.in' => !empty($args['search']) ? $args['search'] : null,
        'info.fieldOfUse' => !empty($args['field_of_use']) ? $args['field_of_use'] : null,
        'metadata.provider' => !empty($args['provider']) ? join(",",$args['provider']) : null,
        'metadata.uploadDate.gte' => !empty($args['upload_date_gte']) ? $args['upload_date_gte'] : null,
        'metadata.uploadDate.lte' => !empty($args['upload_date_lte']) ? $args['upload_date_lte'] : null,
        'metadata.last_updated_by' => !empty($args['last_updated_by']) ? $args['last_updated_by'] : null,
        'metadata.views.gte' => !empty($args['views_gte']) ? $args['views_gte'] : null,
        'metadata.views.lte' => !empty($args['views_lte']) ? $args['views_lte'] : null,
        'metadata.updateDate.gte' => !empty($args['update_date_gte']) ? $args['update_date_gte'] : null,
        'metadata.updateDate.lte' => !empty($args['update_date_lte']) ? $args['update_date_lte'] : null
    ]);

    // Get all descriptions.
    if (!isset($args['type'])) {
        return policyCloudMarketplaceAPIRequest(
            'GET',
            '/descriptions/all' . $filters,
        );
    }

    // Filtering by collection.
    else {
        return policyCloudMarketplaceAPIRequest(
            'GET',
            '/descriptions/' . $args['type'] . $filters
        );
    }
}

/**
 * Retrieve private Assets in pending state from the Marketplace API, also filtered by collection.
 *
 * To learn more about the Marketplace API data schema for retrieving objects and filtering them, visit:
 * https://documenter.getpostman.com/view/16776360/TzsZs8kn#f3cb0963-533d-44d1-9706-b686fdc3a3d2
 *
 * @param	array $args An array of arguments to filter the search.
 * 
 * @uses    policyCloudMarketplaceAPIRequest()
 * 
 * @since   1.0.0
 * @author  Alexandros Raikos <araikos@unipi.gr>
 */
function get_pending_assets(string $token, array $args = [])
{
    // Get all descriptions.
    if (empty($args['collections'])) {
        return policyCloudMarketplaceAPIRequest(
            'GET',
            '/descriptions/permit/all?itemsPerPage=5',
            [],
            $token
        );
    }

    // Filtering by collection.
    else {
        return array_map(function ($collection) use ($token) {
            return policyCloudMarketplaceAPIRequest(
                'GET',
                '/descriptions/permit/' . $collection,
                [],
                $token
            );
        }, (is_array($args['collection']) ? $args['collection'] : [$args['collection']]));
    }
}

/**
 * Retrieve user specific Assets from the Marketplace API, also filtered by collection.
 *
 * To learn more about the Marketplace API data schema for retrieving objects and filtering them, visit:
 * https://documenter.getpostman.com/view/16776360/TzsZs8kn#763f96da-4c32-4e98-9dc3-39ce30a73eaa
 * 
 * To add additional argument for sorting and pagination, visit:
 * https://documenter.getpostman.com/view/16776360/TzsZs8kn#8174c0cb-29a7-4d95-9d13-4182c8b64c44
 *
 * @param   string $uid The relevant username.
 * @param	array $args An array of arguments to filter the search. NOTICE: When filtering with
 * items_per_page the returning assets will be arranged in arrays.
 * 
 * @throws  LogicException If the sorting setting is unsupported.
 * 
 * @uses    policyCloudMarketplaceAPIRequest()
 * 
 * @since   1.0.0
 * @author  Alexandros Raikos <araikos@unipi.gr>
 */
function get_account_assets(string $uid, string $token = null, array $args = [])
{
    // Check arguments
    if (!empty($args['sort_by'])) {
        if (
            $args['sort_by'] != 'newest' ||
            $args['sort_by'] != 'oldest' ||
            $args['sort_by'] != 'rating-asc' ||
            $args['sort_by'] != 'rating-desc' ||
            $args['sort_by'] != 'views-asc' ||
            $args['sort_by'] != 'views-desc' ||
            $args['sort_by'] != 'title'
        ) {
            throw new LogicException('The '.$args['sort_by'].' sorting setting was not found.');
        }
    }

    $filters = '?' . http_build_query([
        'sortBy' => $args['sort_by'] ?? null,
        'page' => $args['assets_page'] ?? null,
        'itemsPerPage' => $args['items_per_page'] ?? 5
    ]);

    return policyCloudMarketplaceAPIRequest(
        'GET',
        '/descriptions/provider/' . $uid . '/all' . $filters,
        [],
        $token
    );
}

/**
 * Retrieve max filtering values stemming from all
 * assets.
 * 
 * To learn more about the Marketplace API data schema for retrieving filtering values:
 * https://documenter.getpostman.com/view/16776360/TzsZs8kn#d95083ac-dd22-448b-bd7c-aeb106e5e42c
 * 
 * @uses    policyCloudMarketplaceAPIRequest()
 * 
 * @since   1.0.0
 * @author  Alexandros Raikos <araikos@unipi.gr>
 */
function get_filtering_values() {
    return policyCloudMarketplaceAPIRequest(
        'GET',
        '/descriptions/statistics/filtering',
        []
    )['results'];
}

/**
 * Retrieve account specific Reviews from the Marketplace API.
 *
 * To learn more about the Marketplace API data schema for retrieving objects and filtering them, visit:
 * https://documenter.getpostman.com/view/16776360/TzsZs8kn#e8f85a28-7e5d-419f-9892-0f5eb7a2ef10
 *
 * @param   string $uid The relevant username.
 * @param	array $args An array of arguments to filter the search. NOTICE: When filtering with
 * items_per_page the returning reviews will be arranged in arrays.
 * 
 * @throws  LogicException If the sorting setting is unsupported.
 * 
 * @uses    policyCloudMarketplaceAPIRequest()
 * 
 * @since   1.0.0
 * @author  Alexandros Raikos <araikos@unipi.gr>
 */
function get_account_reviews(string $uid, string $token = null, array $args = [])
{
    // Check arguments
    if (!empty($args['sort_by'])) {
        if (
            $args['sort_by'] != 'newest' ||
            $args['sort_by'] != 'oldest' ||
            $args['sort_by'] != 'rating-asc' ||
            $args['sort_by'] != 'rating-desc' ||
            $args['sort_by'] != 'views-asc' ||
            $args['sort_by'] != 'views-desc' ||
            $args['sort_by'] != 'title'
        ) {
            throw new LogicException('The '.$args['sort_by'].' sorting setting was not found.');
        }
    }

    $filters = '?' . http_build_query([
        'sortBy' => $args['sort_by'] ?? null,
        'page' => $args['assets_page'] ?? null,
        'itemsPerPage' => $args['items_per_page'] ?? 5
    ]);

    return policyCloudMarketplaceAPIRequest(
        'GET',
        '/descriptions/review/' . $uid . $filters,
        [],
        $token
    );
}


/**
 * Retrieve a specific Asset from the Marketplace API.
 * For more information on the retrieved schema, visit: 
 * https://documenter.getpostman.com/view/16776360/TzsZs8kn#8191ba2b-f1aa-4b05-8e66-12025c85d999
 * 
 * @param	string $api_host The hostname of the Marketplace API server.
 * @param	string $token The user's access token used for authorization (encoded).
 * 
 * @return  array An array containing the description information.
 * 
 * @uses    policyCloudMarketplaceAPIRequest()
 * 
 * @since   1.0.0
 * @author  Alexandros Raikos <araikos@unipi.gr>
 */
function get_asset(string $did, string $token = null)
{
    return policyCloudMarketplaceAPIRequest(
        'GET',
        '/descriptions/all/' . $did,
        [],
        $token
    );
}

/**
 * Get an asset's image data.
 * 
 * @param   string $id The unique ID of the asset.
 * @param   array $token The encoded access token of the requesting user.
 * 
 * @uses    policyCloudMarketplaceAPIRequest()
 * 
 * @since	1.0.0
 * @author Alexandros Raikos <araikos@unipi.gr>
 */
function get_asset_image($id, $token)
{
    $response = policyCloudMarketplaceAPIRequest(
        'GET',
        '/images/' . $id,
        [],
        $token,
        [
            'Content-Type: application/octet-stream',
            (!empty($token) ? ('x-access-token: ' . $token) : null)
        ],
    );

    return $response;
}


/**
 * Create an asset using the PolicyCloud Marketplace API. For more info visit:
 * https://documenter.getpostman.com/view/16776360/TzsZs8kn#5b7e797a-682f-4b0a-bb5f-d9c371988a05
 * 
 * @param	array $data An array using schema fields as keys and the requested updated values.
 * @param	string $token The user's access token used for authorization (encoded).
 * 
 * @return  array An array containing the description ID.
 * 
 * @uses    policyCloudMarketplaceAPIRequest()
 * 
 * @since   1.0.0
 * @author  Alexandros Raikos <araikos@unipi.gr>
 */
function create_asset($data, $token)
{
    $response = policyCloudMarketplaceAPIRequest(
        'POST',
        '/descriptions/' . $data['type'],
        $data,
        $token
    );

    return $response['id'];
}


/**
 * Edit Description objects using the PolicyCloud Marketplace. For more info visit:
 * https://documenter.getpostman.com/view/16776360/TzsZs8kn#5b7e797a-682f-4b0a-bb5f-d9c371988a05
 * 
 * @param	array $changes An array using schema fields as keys and the requested updated values.
 * 
 * @uses    policyCloudMarketplaceAPIRequest()
 * 
 * @since   1.0.0
 * @author  Alexandros Raikos <araikos@unipi.gr>
 */
function edit_asset($did, $information, $token)
{
    if (policyCloudMarketplaceAPIRequest(
        'PUT',
        '/descriptions/all/' . $did,
        $information,
        $token
    )) {
        return true;
    }
}
