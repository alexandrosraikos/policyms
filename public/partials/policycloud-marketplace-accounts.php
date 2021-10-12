<?php

require_once plugin_dir_path(dirname(__FILE__)) . 'partials/vendor/autoload.php';

use Firebase\JWT\BeforeValidException;
use Firebase\JWT\ExpiredException;
use Firebase\JWT\JWT;
use Firebase\JWT\SignatureInvalidException;


/**
 * Check username availability using the Marketplace API.
 * For more information concerning the schema of the registration data, please visit:
 * https://documenter.getpostman.com/view/16776360/TzsZs8kn#135d37d6-0eef-47e5-a31f-df4153962503
 * 
 * @param	string $username The username to be registered.
 * @return	bool The availability of the requested username.
 * @since	1.0.0
 */
function marketplace_username_exists($hostname, $username)
{
    // Request username availability status.
    $curl = curl_init();
    curl_setopt_array($curl, [
        CURLOPT_URL => 'https://' . $hostname . '/accounts/username_availability',
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_ENCODING => '',
        CURLOPT_MAXREDIRS => 10,
        CURLOPT_TIMEOUT => 0,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST => 'GET',
        CURLOPT_HTTPHEADER => [
            'username: "' . $username . '"'
        ],
    ]);
    $response = json_decode(curl_exec($curl), true);
    if (curl_errno($curl)) {
        throw new Exception("Unable to reach the Marketplace server. More details: " . curl_error($curl));
    }
    curl_close($curl);

    // Return status.
    return ($response['_status'] ?? '') == 'successful';
}

/**
 * Enact user registration using the Marketplace API.
 * For more information concerning the schema of the registration data, please visit:
 * https://documenter.getpostman.com/view/16776360/TzsZs8kn#17a87988-323b-4209-b93c-ea3854616ab3 
 * 
 * @param	array $data The user information to be registered with the Marketplace API.
 * @usedby PolicyCloud_Marketplace_Public::account_registration_handler()
 * 
 * @throws Exception For invalid registration data, missing options, or connectivity issues.
 * 
 * @since	1.0.0
 */
function account_registration($data)
{
    // Information validation checks and errors.
    if (
        empty($data['username']) ||
        empty($data['password']) ||
        empty($data['email']) ||
        empty($data['name']) ||
        empty($data['surname'])
    ) throw new Exception('Please fill in all the required fields.');
    if (!filter_var($data["email"], FILTER_VALIDATE_EMAIL)) throw new Exception("Please enter a valid email address.");
    if ($data['password'] !== $data['password-confirm']) throw new Exception('Password and password confirmation should match.');
    if (
        !empty(preg_match('@[A-Z]@', $data['password'])) &&
        !empty(preg_match('@[a-z]@', $data['password'])) &&
        !empty(preg_match('@[0-9]@', $data['password'])) &&
        !empty(preg_match('@[^\w]@', $data['password'])) &&
        strlen($data['password']) < 8
    ) throw new Exception('Password should be at least 8 characters and  include at least one uppercase letter, a number, and a special character.');
    if ($data['username'] <= 2) throw new Exception("Username must be at least 2 characters.");
    if (!empty($data['title'])) {
        if (!in_array($data['title'], ['Mr.', 'Ms.', 'Mrs.', 'Dr.', 'Prof.', 'Sir', 'Miss', 'Mx.', '-'])) throw new Exception("Please select a valid title.");
    }
    if (!empty($data['gender'])) {
        if (!in_array($data['gender'], ['male', 'female', 'transgender', 'genderqueer', 'questioning', '-'])) throw new Exception("Please select a gender from the list.");
    }

    // Retrieve API credentials.
    $options = get_option('policycloud_marketplace_plugin_settings');
    if (empty($options['marketplace_host'])) throw new Exception("No PolicyCloud Marketplace API hostname was defined in WordPress settings.");

    // Username availability check.
    if (!marketplace_username_exists($options['marketplace_host'], $data['username'])) throw new Exception("Username already exists.");

    // Contact Marketplace registration API.
    $curl = curl_init();
    curl_setopt_array($curl, array(
        CURLOPT_URL => 'https://' . $options['marketplace_host'] . '/accounts/users/registration',
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_ENCODING => '',
        CURLOPT_MAXREDIRS => 10,
        CURLOPT_TIMEOUT => 0,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST => 'POST',
        CURLOPT_POSTFIELDS => json_encode([
            'username' => $data['username'],
            'account' => [
                'password' => $data['password']
            ],
            'info' => [
                'name' => $data['name'],
                'surname' => $data['surname'],
                'title' => $data['title'] ?? '',
                'gender' => $data['gender'] ?? '',
                'organization' => $data['organization'] ?? '',
                'phone' => $data['phone'] ?? '',
                'email' => $data['email'],
                'about' => $data['about'],
                'social' => array_map(function ($k, $v) use ($data) {
                    return $v . ":" . $data['social-url'][$k];
                }, $data['social-title'] ?? [])
            ]
        ]),
        CURLOPT_HTTPHEADER => array('Content-Type: application/json', 'x-more-time: 1')
    ));

    // Get the data.
    $response = json_decode(curl_exec($curl), true);

    // Handle errors.
    if (curl_errno($curl)) {
        throw new Exception("Unable to reach the Marketplace server. More details: " . curl_error($curl));
    }

    // Close the session.
    curl_close($curl);

    // Handle response.
    if (!isset($response)) throw new Exception("The Marketplace API response was invalid.");
    elseif ($response['_status'] == 'successful') {
        try {
            // Encrypt token using the same key and return.
            if (empty($options['jwt_key'])) throw new Exception("No PolicyCloud Marketplace API key was defined in WordPress settings.");
            else {
                // Decode token and send verification email.
                $decoded_token = JWT::decode($response['token'], $options['jwt_key'], array('HS256'));
                if ($decoded_token->account->verified !== '1') {
                    user_email_verification_resend($decoded_token->account->verified, $decoded_token->account->email);
                }
                if (empty($options['encryption_key'])) throw new Exception("No PolicyCloud Marketplace encryption key was defined in WordPress settings.");
                return openssl_encrypt($response['token'], "AES-128-ECB", $options['encryption_key']);
            }
        } catch (\Exception $e) {
            throw new Exception($e->getMessage());
        }
    } else throw new Exception($response['message']);
}



/**
 * Enact account authorization using the Marketplace API.
 *
 * @param array $data The data user for existing user authorization (username, password).
 * @return string The encoded Marketplace API token for the successfully authenticated user.
 * @usedby PolicyCloud_Marketplace_Public::account_authorization_handler()
 * 
 * @throws Exception For invalid registration data, missing options, or connectivity issues.
 * 
 * @since    1.0.0
 */
function account_authorization($data)
{

    // Check submitted log in information.
    if (
        empty($data['username-email']) ||
        empty($data['password'])
    ) throw new Exception('Please fill in all required fields.');

    $options = get_option('policycloud_marketplace_plugin_settings');
    if (empty($options['marketplace_host'])) throw new Exception("No PolicyCloud Marketplace API hostname was defined in WordPress settings.");

    if (is_email($data['username-email'])) {
        $data = [
            'email' => $data['username-email'],
            'password' => $data['password'],
        ];
    } else {
        $data = [
            'username' => $data['username-email'],
            'password' => $data['password'],
        ];
    }

    // Contact Marketplace login API endpoint.
    $curl = curl_init();
    curl_setopt_array($curl, [
        CURLOPT_URL => 'https://' . $options['marketplace_host'] . '/accounts/users/authorization',
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_ENCODING => '',
        CURLOPT_MAXREDIRS => 10,
        CURLOPT_TIMEOUT => 0,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST => 'POST',
        CURLOPT_POSTFIELDS => json_encode($data),
        CURLOPT_HTTPHEADER => ['Content-Type: application/json', 'x-more-time: 1']
    ]);
    
    // Get the data.
    $response = json_decode(curl_exec($curl), true);

    // Handle errors.
    if (curl_errno($curl)) {
        throw new Exception("Unable to reach the Marketplace server. More details: " . curl_error($curl));
    }

    // Close the session.
    curl_close($curl);

    // Handle response.
    if (!isset($response)) {
        throw new Exception("The Marketplace API response was invalid.");
    } elseif ($response['_status'] == 'successful') {
        try {
            // Encrypt token using the same key and return.
            if (empty($options['encryption_key'])) throw new Exception("No PolicyCloud Marketplace encryption key was defined in WordPress settings.");
            else {
                return openssl_encrypt($response['token'], "AES-128-ECB", $options['encryption_key']);
            }
        } catch (Exception $e) {
            throw new Exception($e->getMessage());
        }
    } else throw new Exception($response['message']);
}


/**
 * Retrieve and decrypt the token from the user.
 * @param	bool $decode Pass *true* if the token must be returned decoded as well.
 * @return	string|array|bool Returns the token or an array with both encoded and decoded tokens. Returns *false* if there is no token.
 * @since	1.0.0
 */
function retrieve_token(bool $decode = false)
{
    // Retrieve credentials.
    $options = get_option('policycloud_marketplace_plugin_settings');
    if (empty($options['jwt_key'])) throw new Exception("No PolicyCloud Marketplace API Key defined in WordPress settings.");

    // Retrieve saved token.
    if (!empty($_COOKIE['ppmapi-token'])) {

        // Decrypt token.
        if (empty($options['encryption_key'])) throw new Exception("No PolicyCloud Marketplace encryption key was defined in WordPress settings.");
        $token = openssl_decrypt($_COOKIE['ppmapi-token'], "AES-128-ECB", $options['encryption_key']);
        if (empty($token)) throw new Exception("Decryption was unsuccessful.");

        // Validate token age, signature and content.
        try {
            $decoded_token = JWT::decode($token, $options['jwt_key'], array('HS256'));
        } catch (InvalidArgumentException $e) {
            throw new Exception($e->getMessage());
        } catch (UnexpectedValueException $e) {
            if (empty($options['encryption_key'])) throw new Exception("No PolicyCloud Marketplace log in page was defined in WordPress settings.");
            throw new Exception('Your session is invalid, please <a href="' . $options['login_page'] . '">log in</a> again to continue.');
        } catch (SignatureInvalidException $e) {
            throw new Exception($e->getMessage());
        } catch (BeforeValidException $e) {
            throw new Exception($e->getMessage());
        } catch (ExpiredException $e) {
            if (empty($options['encryption_key'])) throw new Exception("No PolicyCloud Marketplace log in page was defined in WordPress settings.");
            throw new Exception('Your session has expired, please <a href="' . $options['login_page'] . '">log in</a> again to continue.');
        }

        return ($decode) ? [
            'encoded' => $token,
            'decoded' => json_decode(json_encode($decoded_token),true)
        ] : $token;
    } else return false;
}

/**
 * 
 * Send an account verification email to the user.
 * 
 * @param string $verification_code The verification code of the user.
 * @param string $email The user's email address.
 * 
 * @throws Exception If the email cannot be sent.
 * @throws Exception If the verification code is empty.
 * 
 * @since 1.0.0
 */
function user_email_verification_resend(string $verification_code, string $email)
{
    $options = get_option('policycloud_marketplace_plugin_settings');
    if (empty($options['account_page'])) throw new Exception("No PolicyCloud Marketplace account page defined in WordPress settings.");
    if (!empty($verification_code)) {
        $host = parse_url(get_site_url())['host'];
        if (!wp_mail(
            $email,
            'Verify your PolicyCloud Marketplace account',
            "You are receiving this email because a new PolicyCloud Marketplace account was created with this address. If that was you, please click this link to verify your email address: " . $options['account_page'] . "#details?verification-code=" . $verification_code,
            ['From: PolicyCloud Marketplace <noreply@' . $host . '>']
        )) {
            throw new Exception("The email couldn't be delivered, please contact the server administrator.");
        }
    } else {
        throw new Exception("The verification code was not found.");
    }
}

/**
 * 
 * Verifies the user with the PolicyCloud Marketplace API.
 * 
 * For more information on the interface used, refer to the documentation here:
 * https://documenter.getpostman.com/view/16776360/TzsZs8kn#d83c1527-9597-4d80-a6ab-6cbe89ab13f8
 * 
 * @param string $verification_code The user's verification code.
 * 
 * @throws Exception If the PolicyCloud Marketplace API host is not defined in the WordPress Settings.
 * @throws Exception If the verification code is empty.
 * 
 * @since 1.0.0
 */
function verify_user(string $verification_code)
{

    // Retrieve API credentials.
    $options = get_option('policycloud_marketplace_plugin_settings');
    if (empty($options['marketplace_host'])) throw new Exception("No PolicyCloud Marketplace API hostname was defined in WordPress settings.");

    // Contact Marketplace registration API.
    $curl = curl_init();
    curl_setopt_array($curl, array(
        CURLOPT_URL => 'https://' . $options['marketplace_host'] . '/accounts/users/verification/' . $verification_code,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_ENCODING => '',
        CURLOPT_MAXREDIRS => 10,
        CURLOPT_TIMEOUT => 0,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST => 'GET',
        CURLOPT_HTTPHEADER => array('Content-Type: application/json', 'x-more-time: 1')
    ));

    // Get the data.
    $response = json_decode(curl_exec($curl), true);

    // Handle errors.
    if (curl_errno($curl)) {
        throw new Exception("Unable to reach the Marketplace server. More details: " . curl_error($curl));
    }

    // Close the session.
    curl_close($curl);

    // Handle response.
    if (!isset($response)) {
        throw new Exception("The Marketplace API response was invalid.");
    } elseif ($response['_status'] == 'successful') {
        try {
            // Encrypt token using the same key and return.
            if (empty($options['encryption_key'])) throw new Exception("No PolicyCloud Marketplace encryption key was defined in WordPress settings.");
            else {
                return openssl_encrypt($response['token'], "AES-128-ECB", $options['encryption_key']);
            }
        } catch (Exception $e) {
            throw new Exception($e->getMessage());
        }
    } else throw new Exception($response['message']);
}

/**
 * Get another user's information.
 * For more information concerning the schema of the account data, please visit:
 * https://documenter.getpostman.com/view/16776360/TzsZs8kn#17a87988-323b-4209-b93c-ea3854616ab3 
 * 
 * @param   string $uid The valid username of the user whose information will be edited.
 * @param   array $token The decoded access token of the requesting user.
 * 
 * @since	1.0.0
 */
function get_user_information($uid, $token)
{

    // Retrieve credentials.
    $options = get_option('policycloud_marketplace_plugin_settings');
    if (empty($options['marketplace_host'])) throw new Exception("No PolicyCloud Marketplace API hostname was defined in WordPress settings.");

    $curl = curl_init();

    // Contact PolicyCloud Marketplace API.
    curl_setopt_array($curl, [
        CURLOPT_URL => 'https://' . $options['marketplace_host'] . '/accounts/users/information/' . $uid,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_ENCODING => "",
        CURLOPT_MAXREDIRS => 10,
        CURLOPT_TIMEOUT => 30,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST => "GET",
        CURLOPT_HTTPHEADER => ['Content-Type: application/json', (!empty($token) ? ('x-access-token: ' . $token) : null)]
    ]);

    // Get data.
    $information = json_decode(curl_exec($curl), true);

    // Handle errors.
    if (!empty(curl_error($curl))) throw new Exception("There was a connection error while attempting to retrieve the user's information.");

    // Close session.
    curl_close($curl);

    // Return encypted token.
    if (!isset($information)) {
        throw new Exception("The Marketplace API response was invalid when trying to retrieve this user's information.");
    } elseif ($information['_status'] == 'successful') {
        return $information['result'];
    } else throw new Exception("The user couldn't be retrieved. More details: " . $information['message']);
}

/**
 * Get a user's statistics.
 * For more information concerning the schema of the account data, please visit:
 * https://documenter.getpostman.com/view/16776360/TzsZs8kn#5805e187-2319-4166-bf58-b78c6e902e42
 * 
 * @param   string $uid The valid username of the user whose information will be edited.
 * @param   array $token The decoded access token of the requesting user.
 * 
 * @since	1.0.0
 */
function get_user_statistics($uid, $token)
{

    // Retrieve credentials.
    $options = get_option('policycloud_marketplace_plugin_settings');
    if (empty($options['marketplace_host'])) throw new Exception("No PolicyCloud Marketplace API hostname was defined in WordPress settings.");

    $curl = curl_init();

    // Contact PolicyCloud Marketplace API.
    curl_setopt_array($curl, [
        CURLOPT_URL => 'https://' . $options['marketplace_host'] . '/accounts/users/statistics/' . $uid,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_ENCODING => "",
        CURLOPT_MAXREDIRS => 10,
        CURLOPT_TIMEOUT => 30,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST => "GET",
        CURLOPT_HTTPHEADER => ['Content-Type: application/json', (!empty($token) ? ('x-access-token: ' . $token) : null)]
    ]);

    // Get data.
    $statistics = json_decode(curl_exec($curl), true);

    // Handle errors.
    if (!empty(curl_error($curl))) throw new Exception("There was a connection error while attempting to retrieve the user's statistics: ". curl_error($curl));

    // Close session.
    curl_close($curl);

    // Return 
    if (!isset($statistics)) throw new Exception("The Marketplace API response for retrieving the user's statistics was invalid.");
    elseif ($statistics['_status'] == 'successful') {
       return $statistics['results'];
    } else throw new Exception("The Marketplace API response was invalid when trying to retrieve this user's statistics. " . $statistics['message']);
}

/**
 * Enact account editing using the Marketplace API.
 * For more information concerning the schema of the account data, please visit:
 * https://documenter.getpostman.com/view/16776360/TzsZs8kn#17a87988-323b-4209-b93c-ea3854616ab3 
 * 
 * @param	array $data The user information to be edited
 * @param   string $uid The valid username of the user whose information will be edited.
 * @param   array $token The encoded access token of the requesting user.
 * @usedby PolicyCloud_Marketplace_Public::account_edit_handler()
 * @since	1.0.0
 */
function account_edit($data, $uid, $token)
{

    // Information validation checks and errors.
    if (empty($data['email']) || empty($data['name']) || empty($data['surname'])) throw new Exception('Please fill all required fields!');
    if (!filter_var($data["email"], FILTER_VALIDATE_EMAIL)) throw new Exception("Please enter a valid email.");
    if (!empty($data['password'])) {
        if (!empty($data['password-confirm'])) {
            if ($data['password'] !== $data['password-confirm']) throw new Exception('Password and password confirmation should match!');
            if (
                !empty(preg_match('@[A-Z]@', $data['password'])) &&
                !empty(preg_match('@[a-z]@', $data['password'])) &&
                !empty(preg_match('@[0-9]@', $data['password'])) &&
                !empty(preg_match('@[^\w]@', $data['password'])) &&
                strlen($data['password']) < 8
            ) throw new Exception('Password should be at least 8 characters in length and should include at least one upper case letter, one number, and one special character.');
        } else throw new Exception("Please fill in the password confirmation when changing your password.");
        if (empty($data['current-password'])) throw new Exception('Please insert your current password before changing it.');
    }
    if (!empty($data['title'])) {
        if (!in_array($data['title'], ['Mr.', 'Ms.', 'Mrs.', 'Dr.', 'Prof.', 'Sir', 'Miss', 'Mx.', '-'])) throw new Exception("Please select a valid title.");
    }
    if (!empty($data['gender'])) {
        if (!in_array($data['gender'], ['male', 'female', 'transgender', 'genderqueer', 'questioning', '-'])) throw new Exception("Please select a gender from the list.");
    }

    // Retrieve API credentials.
    $options = get_option('policycloud_marketplace_plugin_settings');
    if (empty($options['marketplace_host'])) throw new Exception("No PolicyCloud Marketplace API hostname was defined in WordPress settings.");

    // Contact the PolicyCloud Marketplace API for password change.
    if (!empty($data['password'])) {
        $curl = curl_init();
        curl_setopt_array($curl, array(
            CURLOPT_URL => 'https://' . $options['marketplace_host'] . '/accounts/users/password/change',
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_POSTFIELDS => json_encode([
                'old_password' => $data['current-password'],
                'new_password' => $data['password']
            ]),
            CURLOPT_HTTPHEADER => array('Content-Type: application/json', (!empty($token) ? ('x-access-token: ' . $token) : null))
        ));
        
        // Get the data.
        $password_update_response = json_decode(curl_exec($curl), true);

        // Handle errors.
        if (curl_errno($curl)) throw new Exception("Unable to reach the Marketplace server to change the password. More details: " . curl_error($curl));

        // Close the session.
        curl_close($curl);

        // Handle the response.
        if (!isset($password_update_response)) throw new Exception("The Marketplace API response for changing the user's password was invalid.");
        elseif ($password_update_response['_status'] != 'successful') throw new Exception('There was an error updating the user\'s password: ' . $password_update_response['message']);
    }

    // TODO @alexandrosraikos: Handle email change with API endpoint.

    // Contact the PolicyCloud Marketplace API for non-sensitive information updating.
    $curl = curl_init();
    curl_setopt_array($curl, array(
        CURLOPT_URL => 'https://' . $options['marketplace_host'] . '/accounts/users/information/' . $uid,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_ENCODING => '',
        CURLOPT_MAXREDIRS => 10,
        CURLOPT_TIMEOUT => 0,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST => 'PUT',
        CURLOPT_POSTFIELDS => json_encode([
            'info' => [
                'name' => $data['name'],
                'surname' => $data['surname'],
                'title' => $data['title'],
                'gender' => $data['gender'],
                'organization' => $data['organization'],
                'phone' => $data['phone'],
                'social' => array_map(function ($k, $v) {
                    return $k . ":" . $v;
                }, $data['socials-title'] ?? [], $data['socials-url'] ?? []),
                'about' => $data['about']
            ],
            'profile_parameters' => [
                'public_email' => intval($data['public-email']),
                'public_phone' => intval($data['public-phone']),
            ]
        ]),
        CURLOPT_HTTPHEADER => array('Content-Type: application/json', (!empty($token) ? ('x-access-token: ' . $token) : null), 'x-more-time: 1')
    ));

    // Get the data
    $nonsensitive_update_response = json_decode(curl_exec($curl), true);

    // Handle errors.
    if (curl_errno($curl)) throw new Exception("Unable to reach the Marketplace server to update the information. More details: " . curl_error($curl));

    // Close the session.
    curl_close($curl);

    // Handle response.
    if (!isset($nonsensitive_update_response)) throw new Exception("The Marketplace API response for changing the user's password was invalid.");
    elseif ($nonsensitive_update_response['_status'] == 'successful') {
        try {

            // Encrypt token using the same key and return.
            if (empty($options['encryption_key'])) throw new Exception("No PolicyCloud Marketplace encryption key was defined in WordPress settings.");
            return openssl_encrypt($nonsensitive_update_response['token'], "AES-128-ECB", $options['encryption_key']);
        } catch (Exception $e) {
            throw new Exception($e->getMessage());
        }
    } else throw new Exception('There was an error updating user information: ' . $nonsensitive_update_response['message']);
}