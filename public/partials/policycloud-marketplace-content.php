<?php

/**
 * Retrieve publicly available Description Objects from the Marketplace API, also filtered by collection.
 *
 * To learn more about the Marketplace API data schema for retrieving objects and filtering them, visit:
 * https://documenter.getpostman.com/view/16776360/TzsZs8kn#6c8e45e3-5be6-4c10-82a6-7d698b092e9e
 *
 * @param	array $args An array of arguments to filter the search.
 * @throws  Exception If there is no PolicyCloud API hostname defined in the settings.
 * @throws  Exception If there was a connection error.
 * @throws  ErrorException If no descriptions were found for the assigned arguments.
 * 
 * @since    1.0.0
 */
function get_descriptions(array $args)
{

    // Retrieve credentials.
    $options = get_option('policycloud_marketplace_plugin_settings');
    if (empty($options['marketplace_host'])) throw new Exception("No PolicyCloud Marketplace API hostname was defined in WordPress settings.");

    /** 
     * 
     * 	TODO @alexandrosraikos: Coordinate query parameters for front-end usage. More information here:
     *  https://documenter.getpostman.com/view/16776360/TzsZs8kn#595b5504-1d07-49f8-8166-8efdb400c5f4
     * 
     * */

    // "in" can be get arrays for multiple parameters.
    // "gte"/"lte" are range selectors in HTML.

    $filters = '?' . http_build_query([
        'info.owner' => $args['owner'] ?? null,
        'info.title' => $args['title'] ?? $args['search'] ?? null,
        'info.type.in' => (empty($args['type'])) ? null : implode(',', ($args['type'] ?? [])),
        'info.subtype' => $args['subtype'] ?? null,
        'info.comments.in' => $args['comments'] ?? null,
        'info.contact' => $args['contact'] ?? null,
        'info.description.in' => $args['description'] ?? $args['search'] ?? null,
        'info.fieldOfUse' => $args['field_of_use'] ?? null,
        'metadata.provider' => $args['provider'] ?? null,
        'metadata.uploadDate.gte' => $args['upload_date_gte'] ?? null,
        'metadata.uploadDate.lte' => $args['upload_date_lte'] ?? null,
        'metadata.last_updated_by' => $args['last_updated_by'] ?? null,
        'metadata.views.gte' => $args['views_gte'] ?? null,
        'metadata.views.lte' => $args['views_lte'] ?? null,
        'metadata.updateDate.gte' => $args['update_date_gte'] ?? null,
        'metadata.updateDate.lte' => $args['update_date_lte'] ?? null
    ]);

    $curl = curl_init();

    // Get all descriptions.
    if (!isset($args['collections'])) {

        // Contact PolicyCloud Marketplace API.
        curl_setopt_array($curl, [
            CURLOPT_URL => 'https://' . $options['marketplace_host'] . '/descriptions/all' . $filters,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => "",
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 30,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => "GET",
        ]);

        // Get data.
        $descriptions = json_decode(curl_exec($curl), true);

        // Handle errors.
        if (!empty(curl_error($curl))) throw new Exception("There was a connection error while attempting to retrieve all descriptions.");
    }

    // Filtering by collection.
    else {
        $descriptions = array();
        foreach ($args['collection'] as $collection) {

            // Contact PolicyCloud Marketplace API.
            $curl = curl_init();
            curl_setopt_array($curl, [
                CURLOPT_URL => 'https://' . $options['marketplace_host'] . '/descriptions/' . $collection . $filters,
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_ENCODING => "",
                CURLOPT_MAXREDIRS => 10,
                CURLOPT_TIMEOUT => 30,
                CURLOPT_FOLLOWLOCATION => true,
                CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                CURLOPT_CUSTOMREQUEST => "GET",
            ]);

            // Append descriptions
            $descriptions += (array) json_decode(curl_exec($curl), true);

            // Handle errors.
            if (!empty(curl_error($curl))) throw new Exception("There was a connection error while attempting to retrieve all descriptions.");
        }
    }

    if (!empty($descriptions['_status'])) {
        if ($descriptions['_status'] == "unsuccessful") {
            throw new ErrorException("No descriptions were found.");
        }
    }

    // Close session.
    curl_close($curl);
    return $descriptions;
}

/**
 * Retrieve user specific Description Objects from the Marketplace API, also filtered by collection.
 *
 * To learn more about the Marketplace API data schema for retrieving objects and filtering them, visit:
 * https://documenter.getpostman.com/view/16776360/TzsZs8kn#763f96da-4c32-4e98-9dc3-39ce30a73eaa
 * 
 * To add additional argument for sorting and pagination, visit:
 * https://documenter.getpostman.com/view/16776360/TzsZs8kn#8174c0cb-29a7-4d95-9d13-4182c8b64c44
 *
 * @param   string $uid The relevant username.
 * @param	array $args An array of arguments to filter the search. 
 * NOTICE: When filtering with items_per_page the returning descriptions will be arranged in arrays.
 * @throws  Exception If there is no PolicyCloud API hostname defined in the settings.
 * @throws  Exception If there was a connection error.
 * @throws  ErrorException If no descriptions were found for the assigned arguments.
 * 
 * @since    1.0.0
 */
function get_user_descriptions(string $uid, string $token = null, array $args = [])
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
            throw new Exception('The asset sorting setting is invalid.');
        }
    }

    // Retrieve credentials.
    $options = get_option('policycloud_marketplace_plugin_settings');
    if (empty($options['marketplace_host'])) throw new Exception("No PolicyCloud Marketplace API hostname was defined in WordPress settings.");

    $filters = '?' . http_build_query([
        'sortBy' => $args['sort_by'] ?? null,
        'page' => $args['assets_page'] ?? null,
        'itemsPerPage' => $args['items_per_page'] ?? 5
    ]);

    $curl = curl_init();

    // Contact PolicyCloud Marketplace API.
    curl_setopt_array($curl, [
        CURLOPT_URL => 'https://' . $options['marketplace_host'] . '/descriptions/provider/' . $uid . '/all' . $filters,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_ENCODING => "",
        CURLOPT_MAXREDIRS => 10,
        CURLOPT_TIMEOUT => 30,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST => "GET",
        CURLOPT_FAILONERROR => true,
        CURLOPT_HTTPHEADER => ['Content-Type: application/json', (!empty($token) ? ('x-access-token: ' . $token) : null)]
    ]);

    // Get data.
    $descriptions = json_decode(curl_exec($curl), true);

    // Handle any errors.
    if (!empty(curl_error($curl))) throw new Exception("There was a connection error while attempting to retrieve the user's descriptions: ". curl_error($curl));

    // Close session.
    curl_close($curl);

    // Return encypted token.
    if (!isset($descriptions)) {
        throw new Exception("The Marketplace API response was invalid when attempting to retrieve this user's descriptions.");
    } elseif ($descriptions['_status'] == 'successful') {
        return $descriptions;
    } else throw new Exception("There was an error retrieving user descriptions. ".$descriptions['message']);
}


/**
 * Retrieve a specific Description Object from the Marketplace API.
 * For more information on the retrieved schema, visit: 
 * https://documenter.getpostman.com/view/16776360/TzsZs8kn#8191ba2b-f1aa-4b05-8e66-12025c85d999
 * 
 * @param	string $api_host The hostname of the Marketplace API server.
 * @param	string $token The user's access token used for authorization (encoded).
 * @return  array An array containing the description information.
 * @since    1.0.0
 */
function get_specific_description(string $did, string $token = null)
{
    // Retrieve credentials.
    $options = get_option('policycloud_marketplace_plugin_settings');
    if (empty($options['marketplace_host'])) throw new Exception("No PolicyCloud Marketplace API hostname was defined in WordPress settings.");

    // Contact Marketplace login API endpoint.
    $curl = curl_init();

    curl_setopt_array($curl, array(
        CURLOPT_URL => 'https://' . $options['marketplace_host'] . '/descriptions/all/' . $did,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_ENCODING => '',
        CURLOPT_MAXREDIRS => 10,
        CURLOPT_TIMEOUT => 0,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST => 'GET',
        CURLOPT_HTTPHEADER => ['Content-Type: application/json', (!empty($token) ? ('x-access-token: ' . $token) : null)]
    ));

    // Get data,
    $description = json_decode(curl_exec($curl), true);

    // Handle errors.
    if (!empty(curl_error($curl))) throw new Exception("There was a connection error while attempting to retrieve all descriptions.");

    // Close session.
    curl_close($curl);
    return $description;
}



/**
 * Edit Description objects using the PolicyCloud Marketplace. For more info visit:
 * https://documenter.getpostman.com/view/16776360/TzsZs8kn#5b7e797a-682f-4b0a-bb5f-d9c371988a05
 * 
 * @param	array $changes An array using schema fields as keys and the requested updated values.
 * @uses	PolicyCloud_Marketplace_Public::retrieve_token()
 * @since	1.0.0
 */
function description_editing($updated)
{
    // Retrieve credentials.
    $options = get_option('policycloud_marketplace_plugin_settings');
    if (empty($options['marketplace_host'])) throw new Exception("No PolicyCloud Marketplace API hostname was defined in WordPress settings.");

    try {

        require_once plugin_dir_path(dirname(__FILE__)) . 'public/partials/policycloud-marketplace-accounts.php';

        // Check authorization status
        $token = retrieve_token();
        if (!empty($token)) {

            // Contact Marketplace API endpoint.
            $curl = curl_init();

            curl_setopt_array($curl, array(
                CURLOPT_URL => 'https://' . $options['marketplace_host'] . '/descriptions/all/' . $updated['id'],
                CURLOPT_RETURNTRANSFER => false,
                CURLOPT_ENCODING => '',
                CURLOPT_MAXREDIRS => 10,
                CURLOPT_TIMEOUT => 0,
                CURLOPT_FOLLOWLOCATION => true,
                CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                CURLOPT_CUSTOMREQUEST => 'PUT',
                CURLOPT_HTTPHEADER => ['x-access-token: ' . $token]
            ));

            if (!curl_exec($curl)) throw new Exception("There an API error while updating the Description.");

            // Handle errors.
            if (!empty(curl_error($curl))) throw new Exception("There was a connection error while updating the Description.");

            // Close session.
            curl_close($curl);
        } else throw new Exception("You need to be logged in in order to make modifications.");
    } catch (Exception $e) {
        throw $e;
    }
}


/**
 * Create Description objects using the PolicyCloud Marketplace. For more info visit:
 * https://documenter.getpostman.com/view/16776360/TzsZs8kn#5b7e797a-682f-4b0a-bb5f-d9c371988a05
 * 
 * @param	array $changes An array using schema fields as keys and the requested updated values.
 * @uses	PolicyCloud_Marketplace_Public::retrieve_token()
 * @since	1.0.0
 */
function create_description($new)
{
    // Retrieve credentials.
    $options = get_option('policycloud_marketplace_plugin_settings');
    if (empty($options['marketplace_host'])) throw new Exception("No PolicyCloud Marketplace API hostname was defined in WordPress settings.");

    try {
        require_once plugin_dir_path(dirname(__FILE__)) . 'partials/policycloud-marketplace-accounts.php';

        // Check authorization status
        $token = retrieve_token();
        if (!empty($token)) {

            // Contact Marketplace API endpoint.
            $curl = curl_init();

            $data = json_encode([
                'title' => $new['title'],
                'type' => $new['type'],
                'subtype' => $new['subtype'] ?? '',
                'owner' => $new['owner'],
                'description' => $new['description'],
                'fieldOfUse' => [
                    $new['field_of_use'] ?? ''
                ],
                'comments' => $new['comment'] ?? '',
            ]);

            curl_setopt_array($curl, array(
                CURLOPT_URL => 'https://' . $options['marketplace_host'] . '/descriptions/' . $new['type'],
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_ENCODING => '',
                CURLOPT_MAXREDIRS => 10,
                CURLOPT_TIMEOUT => 0,
                CURLOPT_FOLLOWLOCATION => true,
                CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                CURLOPT_CUSTOMREQUEST => 'POST',
                CURLOPT_POSTFIELDS => $data,
                CURLOPT_HTTPHEADER => array('Content-Type: application/json', 'x-access-token: ' . $token)
            ));

            // Send data.
            $response = json_decode(curl_exec($curl), true);

            // Handle errors.
            if (!empty(curl_error($curl))) throw new Exception("There was a connection error while creating the Description.");

            // Close the session.
            curl_close($curl);

            // Check response and return encypted token.
            if (!isset($response)) throw new Exception("Unable to reach the Marketplace server.");
            elseif ($response['_status'] == 'successful') {
                return $response['id'];
            } elseif ($response['_status'] != 'successful') throw new Exception($response['message']);
        } else throw new Exception("You need to be logged in in order to create a new object.");
    } catch (Exception $e) {
        throw $e;
    }
}
