<?php

require_once plugin_dir_path(dirname(__FILE__)) . 'partials/vendor/autoload.php';

use Firebase\JWT\BeforeValidException;
use Firebase\JWT\ExpiredException;
use Firebase\JWT\JWT;
use Firebase\JWT\SignatureInvalidException;

/**
 * Send a request to the PolicyCloud Marketplace API. 
 * Documentation: https://documenter.getpostman.com/view/16776360/TzsZs8kn#intro
 * 
 * @param string $http_method The standardized HTTP method used for the request.
 * @param array $data The data to be sent according to the documented schema.
 * @param string $token The encoded user access token.
 * @param array $additional_headers Any additional HTTP headers for the request.
 * 
 * @throws InvalidArgumentException For missing WordPress settings.
 * @throws ErrorException For connectivity and other API issues.
 * 
 * @since   1.0.0
 * @author  Alexandros Raikos <araikos@unipi.gr>
 */
function policyCloudMarketplaceAPIRequest($http_method, $uri, $data = [], $token = null, $headers = null, $skip_encoding = false)
{

    // Retrieve hostname URL.
    $options = get_option('policycloud_marketplace_plugin_settings');
    if (empty($options['marketplace_host'])) throw new InvalidArgumentException("No PolicyCloud Marketplace API hostname was defined in WordPress settings.");

    if (!empty($data)) {
        $data = ($skip_encoding) ?  $data : json_encode($data);
    }

    // Contact Marketplace login API endpoint.
    $curl = curl_init();
    curl_setopt_array($curl, [
        CURLOPT_URL => 'https://' . $options['marketplace_host'] . $uri,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_ENCODING => '',
        CURLOPT_MAXREDIRS => 10,
        CURLOPT_TIMEOUT => 0,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST => $http_method,
        CURLOPT_POSTFIELDS => (!empty($data)) ?  $data : null,
        CURLOPT_HTTPHEADER => $headers ?? ['Content-Type: application/json', (!empty($token) ? ('x-access-token: ' . $token) : null), 'x-more-time: 1']
    ]);

    // Get the data.
    $response = curl_exec($curl);

    // Handle errors.
    if (curl_errno($curl)) {
        throw new Exception("Unable to reach the Marketplace server. More details: " . curl_error($curl));
    }

    curl_close($curl);
    if (isset($response)) {
        if (!is_bool($response)) {
            if (in_array('Content-Type: application/json', $headers ?? ['Content-Type: application/json'])) {
                $response = json_decode($response, true);
                if ($response['_status'] == 'successful' && curl_getinfo($curl, CURLINFO_HTTP_CODE) == 200) {
                    return $response;
                } else throw new ErrorException($response['message']);
            } elseif (in_array('Content-Type: application/octet-stream', $headers)) {
                return $response;
            }
        }
    } else throw new ErrorException("There was no response.");
}


/**
 * Retrieve and decrypt the token from the user.
 * @param	bool $decode Pass `true` if the token must be returned decoded as well.
 * @return	string|array|bool Returns the token or an array with `encoded` (string) and `decoded` (array) tokens. Returns *false* if there is no token.
 *
 * @throws InvalidArgumentException When WordPress Settings haven't been initialized.
 * @throws ErrorException When the decryption was unsuccesful.
 * @throws JsonException When the JSON Web Token (JWT) is invalid or has expired.
 * 
 * @since	1.0.0
 * @author  Alexandros Raikos <araikos@unipi.gr>
 */
function retrieve_token(bool $decode = false)
{
    // Retrieve credentials.
    $options = get_option('policycloud_marketplace_plugin_settings');
    if (empty($options['jwt_key'])) throw new InvalidArgumentException("No PolicyCloud Marketplace API Key defined in WordPress settings.");
    if (empty($options['login_page'])) throw new InvalidArgumentException("No PolicyCloud Marketplace log in page was defined in WordPress settings.");

    // Retrieve saved token.
    if (!empty($_COOKIE['ppmapi-token'])) {

        // Decrypt token.
        if (empty($options['encryption_key'])) throw new InvalidArgumentException("No PolicyCloud Marketplace encryption key was defined in WordPress settings.");
        $token = openssl_decrypt($_COOKIE['ppmapi-token'], "AES-128-ECB", $options['encryption_key']);
        if (empty($token)) throw new ErrorException("Decryption was unsuccessful.");

        // Validate token age, signature and content.
        try {
            $decoded_token = JWT::decode($token, $options['jwt_key'], array('HS256'));
        } catch (InvalidArgumentException $e) {
            throw new JsonException();
        } catch (UnexpectedValueException $e) {
            throw new JsonException();
        } catch (SignatureInvalidException $e) {
            throw new JsonException();
        } catch (BeforeValidException $e) {
            throw new JsonException();
        } catch (ExpiredException $e) {
            throw new JsonException();
        }

        return ($decode) ? [
            'encoded' => $token,
            'decoded' => json_decode(json_encode($decoded_token), true)
        ] : $token;
    } else return false;
}


/**
 * Check username availability using the Marketplace API.
 * For more information concerning the schema of the registration data, please visit:
 * https://documenter.getpostman.com/view/16776360/TzsZs8kn#135d37d6-0eef-47e5-a31f-df4153962503
 * 
 * @param	string $username The username to be registered.
 * @return	bool The availability of the requested username.
 * 
 * @uses    policyCloudMarketplaceAPIRequest()
 * 
 * @since	1.0.0
 * @author Alexandros Raikos <araikos@unipi.gr>
 * @author Eleftheria Kouremenou <elkour@unipi.gr>
 */
function marketplace_username_exists($username)
{
    $response = policyCloudMarketplaceAPIRequest(
        'GET',
        '/accounts/username_availability',
        [],
        null,
        ["username: " . $username],
    );

    // Return status.
    return $response['_status'] == 'successful';
}

/**
 * Enact user registration using the Marketplace API.
 * For more information concerning the schema of the registration data, please visit:
 * https://documenter.getpostman.com/view/16776360/TzsZs8kn#17a87988-323b-4209-b93c-ea3854616ab3 
 * 
 * @param	array $data The user information to be registered with the Marketplace API.
 * @usedby PolicyCloud_Marketplace_Public::account_registration_handler()
 * 
 * @throws InvalidArgumentException For non-available registration options & missing WordPress settings.
 * @throws RuntimeException For invalid registration data.
 * @throws ErrorException For connectivity and other API issues.
 * 
 * @return array An associative array with `new_token` and an optional `warning`
 * if the operation wasn't entirely successful.
 * 
 * @uses    policyCloudMarketplaceAPIRequest()
 * 
 * @since	1.0.0
 * @author  Alexandros Raikos <araikos@unipi.gr>
 */
function account_registration($data)
{
    // Retrieve API credentials and check for registered settings.
    $options = get_option('policycloud_marketplace_plugin_settings');
    if (empty($options['marketplace_host'])) throw new InvalidArgumentException("No PolicyCloud Marketplace API hostname was defined in WordPress settings.");
    if (empty($options['jwt_key'])) throw new InvalidArgumentException("No PolicyCloud Marketplace API key was defined in WordPress settings.");
    if (empty($options['encryption_key'])) throw new InvalidArgumentException("No PolicyCloud Marketplace encryption key was defined in WordPress settings.");

    // Information validation checks and errors.
    if (
        empty($data['username']) ||
        empty($data['password']) ||
        empty($data['email']) ||
        empty($data['name']) ||
        empty($data['surname'])
    ) throw new Exception('Please fill in all the required fields.');
    if (!filter_var($data["email"], FILTER_VALIDATE_EMAIL)) throw new RuntimeException("Please enter a valid email address.");
    if ($data['password'] !== $data['password-confirm']) throw new RuntimeException('Password and password confirmation should match.');
    if (
        !empty(preg_match('@[A-Z]@', $data['password'])) &&
        !empty(preg_match('@[a-z]@', $data['password'])) &&
        !empty(preg_match('@[0-9]@', $data['password'])) &&
        !empty(preg_match('@[^\w]@', $data['password'])) &&
        strlen($data['password']) < 8
    ) throw new RuntimeException('Password should be at least 8 characters and  include at least one uppercase letter, a number, and a special character.');
    if ($data['username'] <= 2) throw new RuntimeException("Username must be at least 2 characters.");
    if (!empty($data['title'])) {
        if (!in_array($data['title'], ['Mr.', 'Ms.', 'Mrs.', 'Dr.', 'Prof.', 'Sir', 'Miss', 'Mx.', '-'])) throw new InvalidArgumentException("Please select a valid title.");
    }
    if (!empty($data['gender'])) {
        if (!in_array($data['gender'], ['male', 'female', 'transgender', 'genderqueer', 'questioning', '-'])) throw new InvalidArgumentException("Please select a gender from the list.");
    }

    // Username availability check.
    if (!marketplace_username_exists($options['marketplace_host'], $data['username'])) throw new RuntimeException("Username already exists.");

    if (!is_array($data['social-title']) || !is_array($data['social-url'])) {
        $data['social-title'] = [$data['social-title']];
        $data['social-url'] = [$data['social-url']];
    }

    $response = policyCloudMarketplaceAPIRequest(
        'POST',
        '/accounts/users/registration',
        [
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
                'social' => (empty($data['social-title'][0]) || empty($data['social-url'][0])) ? [''] : array_map(function ($k, $v) use ($data) {
                    return $v . ":" . $data['social-url'][$k];
                }, $data['social-title'])
            ]
        ]
    );

    // Encrypt token using the same key and return.
    try {
        // Decode token and send verification email.
        $decoded_token = JWT::decode($response['token'], $options['jwt_key'], array('HS256'));
        if ($decoded_token->account->verified !== '1') {
            user_email_verification_resend($decoded_token->account->verified, $decoded_token->info->email);
        }
    } catch (RuntimeException $e) {
        $warning_message = 'The verification email could not be sent. Please log in with your credentials and try verifying your email through the account page later.';
    } catch (\Exception $e) {
        $warning_message = 'There has been an error with the newly registered user. More info: ' . $e->getMessage();
    }
    return [
        "new_token" => openssl_encrypt($response['token'], "AES-128-ECB", $options['encryption_key']),
        "warning" => $warning_message ?? ''
    ];
}



/**
 * Enact account authorization using the Marketplace API.
 *
 * @param   array $data The data user for existing user authorization (username, password).
 * @return  string The encoded Marketplace API token for the successfully authenticated user.
 * @usedby PolicyCloud_Marketplace_Public::account_authorization_handler()
 * 
 * @throws  InvalidArgumentException For invalid registration data or missing options.
 * @throws  ErrorException For connectivity and other API issues.
 * 
 * @uses    policyCloudMarketplaceAPIRequest()
 * 
 * @since   1.0.0
 * @author  Alexandros Raikos <araikos@unipi.gr>
 */
function account_authorization($data)
{

    // Check submitted log in information.
    if (
        empty($data['username-email']) ||
        empty($data['password'])
    ) throw new InvalidArgumentException('Please fill in all required fields.');
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

    // Get the authorized token.
    $response = policyCloudMarketplaceAPIRequest(
        'POST',
        '/accounts/users/authorization',
        $data
    );

    // Retun the encrypted authorized token.
    try {
        $options = get_option('policycloud_marketplace_plugin_settings');
        if (empty($options['encryption_key'])) throw new InvalidArgumentException("No PolicyCloud Marketplace encryption key was defined in WordPress settings.");
        else {
            return openssl_encrypt($response['token'], "AES-128-ECB", $options['encryption_key']);
        }
    } catch (Exception $e) {
        throw new ErrorException($e->getMessage());
    }
}

/**
 * 
 * Send an account verification email to the user.
 * 
 * @param string $verification_code The verification code of the user.
 * @param string $email The user's email address.
 * 
 * @throws RuntimeException If the email cannot be sent.
 * @throws InvalidArgumentException If no credentials were defined in WordPress settings
 * or the verification details were not found.
 * 
 * @since   1.0.0
 * @author  Alexandros Raikos <araikos@unipi.gr>
 */
function user_email_verification_resend(string $verification_code, string $email)
{
    $options = get_option('policycloud_marketplace_plugin_settings');
    if (empty($options['account_page'])) throw new InvalidArgumentException("No PolicyCloud Marketplace account page defined in WordPress settings.");
    if (!empty($verification_code) && !empty($email)) {
        $host = parse_url(get_site_url())['host'];
        if (!wp_mail(
            $email,
            'Verify your PolicyCloud Marketplace account',
            "You are receiving this email because a new PolicyCloud Marketplace account was created with this address. If that was you, please click this link to verify your email address: " . $options['account_page'] . "#details?verification-code=" . $verification_code,
            ['From: PolicyCloud Marketplace <noreply@' . $host . '>']
        )) {
            throw new RuntimeException("The verification email couldn't be delivered, please contact the server administrator.");
        }
    } else {
        throw new InvalidArgumentException("The verification details were not found.");
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
 * @uses    policyCloudMarketplaceAPIRequest()
 * 
 * @since 1.0.0
 * @author Alexandros Raikos <araikos@unipi.gr>
 */
function verify_user(string $verification_code)
{
    if (policyCloudMarketplaceAPIRequest(
        'GET',
        '/accounts/users/verification/' . $verification_code
    )) {
        return true;
    }
}

/**
 * Get another user's information.
 * For more information concerning the schema of the account data, please visit:
 * https://documenter.getpostman.com/view/16776360/TzsZs8kn#17a87988-323b-4209-b93c-ea3854616ab3 
 * 
 * @param   string $uid The valid username of the user whose information will be edited.
 * @param   array $token The encoded access token of the requesting user.
 * 
 * @uses    policyCloudMarketplaceAPIRequest()
 * 
 * @since	1.0.0
 * @author Alexandros Raikos <araikos@unipi.gr>
 */
function get_user_information($uid, $token)
{
    // TODO @alexandrosraikos: Support getting the profile picture (waiting on @vkoukos).

    $response = policyCloudMarketplaceAPIRequest(
        'GET',
        '/accounts/users/information/' . $uid,
        [],
        $token
    );

    return $response['result'];
}


/**
 * Get a user's picture blob.
 * 
 * @param   string $id The unique ID of the image file.
 * @param   array $token The encoded access token of the requesting user.
 * 
 * @uses    policyCloudMarketplaceAPIRequest()
 * 
 * @since	1.0.0
 * @author Alexandros Raikos <araikos@unipi.gr>
 */
function get_user_picture($id, $token)
{
    return policyCloudMarketplaceAPIRequest(
        'GET',
        '/images/' . $id,
        [],
        $token,
        [
            'Content-Type: application/octet-stream'
        ],
    );
}


/**
 * Set a user's picture blob.
 * For more information concerning the schema of the account data, please visit:
 * https://documenter.getpostman.com/view/16776360/TzsZs8kn#637c25f2-37ca-4c72-9a86-65e621439480
 * 
 * @param   string $id The unique ID of the image file.
 * @param   array $token The encoded access token of the requesting user.
 * 
 * @uses    policyCloudMarketplaceAPIRequest()
 * 
 * @since	1.0.0
 * @author Alexandros Raikos <araikos@unipi.gr>
 */
function set_user_picture($path, $type, $username, $token)
{
    $file = new CURLFile($path, $type, $username);
    $response = policyCloudMarketplaceAPIRequest(
        'PUT',
        '/accounts/users/image',
        [
            'image' => $file
        ],
        $token,
        [
            'x-file-type: ' . $type,
            'x-access-token: ' . $token,
            'x-more-time: 1'
        ],
        true
    );

    return $response;
}

/**
 * Delete a user's picture blob.
 * For more information concerning the schema of the account data, please visit:
 * https://documenter.getpostman.com/view/16776360/TzsZs8kn#ebc8f12b-e002-46f3-b813-2a4797a8336e
 * 
 * @param   string $id The unique ID of the image file.
 * @param   array $token The encoded access token of the requesting user.
 * 
 * @uses    policyCloudMarketplaceAPIRequest()
 * 
 * @throws  InvalidArgumentException When no encryption key was defined in the WordPress settings.
 * 
 * @since	1.0.0
 * @author Alexandros Raikos <araikos@unipi.gr>
 */
function delete_user_picture($username, $token)
{
    // Check and retrieve saved encryption key.
    $options = get_option('policycloud_marketplace_plugin_settings');
    if (empty($options['encryption_key'])) throw new InvalidArgumentException("No PolicyCloud Marketplace encryption key was defined in WordPress settings.");

    $response = policyCloudMarketplaceAPIRequest(
        'DELETE',
        '/accounts/users/image/' . $username,
        [],
        $token,
        [
            'Content-Type: application/json',
            'x-access-token: ' . $token,
            'x-more-time: 1'
        ],
    );

    return openssl_encrypt($response['token'], "AES-128-ECB", $options['encryption_key']);;
}

/**
 * Get a user's statistics.
 * For more information concerning the schema of the account data, please visit:
 * https://documenter.getpostman.com/view/16776360/TzsZs8kn#5805e187-2319-4166-bf58-b78c6e902e42
 * 
 * @param   string $uid The valid username of the user whose information will be edited.
 * @param   array $token The decoded access token of the requesting user.
 * 
 * @uses    policyCloudMarketplaceAPIRequest()
 * 
 * @since	1.0.0
 * @author  Alexandros Raikos <araikos@unipi.gr>
 */
function get_user_statistics($uid, $token)
{
    $response = policyCloudMarketplaceAPIRequest(
        'GET',
        '/accounts/users/statistics/' . $uid,
        [],
        $token
    );

    return $response['results'];
}

/**
 * Enact account editing using the Marketplace API.
 * For more information concerning the schema of the account data, please visit:
 * https://documenter.getpostman.com/view/16776360/TzsZs8kn#17a87988-323b-4209-b93c-ea3854616ab3 
 * 
 * @param	array $data The user information to be edited
 * @param   string $uid The valid username of the user whose information will be edited.
 * @param   array $token The encoded access token of the requesting user.
 * 
 * @throws  RuntimeException When input data are invalid.
 * @throws  InvalidArgumentException When no encryption key was defined in the WordPress settings.
 * 
 * @usedby  PolicyCloud_Marketplace_Public::account_edit_handler()
 * @uses    policyCloudMarketplaceAPIRequest()
 * 
 * @since	1.0.0
 * @author  Alexandros Raikos <araikos@unipi.gr>
 */
function account_edit($data, $uid, $token)
{

    // Check and retrieve saved encryption key.
    $options = get_option('policycloud_marketplace_plugin_settings');
    if (empty($options['encryption_key'])) throw new InvalidArgumentException("No PolicyCloud Marketplace encryption key was defined in WordPress settings.");
    
    // Information validation checks and errors.
    if (empty($data['email']) || empty($data['name']) || empty($data['surname'])) throw new RuntimeException('Please fill all required fields!');
    if (!filter_var($data["email"], FILTER_VALIDATE_EMAIL)) throw new RuntimeException("Please enter a valid email.");
    if (!empty($data['title'])) {
        if (!in_array($data['title'], ['Mr.', 'Ms.', 'Mrs.', 'Dr.', 'Prof.', 'Sir', 'Miss', 'Mx.', '-'])) throw new RuntimeException("Please select a valid title.");
    }
    if (!empty($data['gender'])) {
        if (!in_array($data['gender'], ['male', 'female', 'transgender', 'genderqueer', 'questioning', '-'])) throw new RuntimeException("Please select a gender from the list.");
    }

    if ($data['new_profile_picture']) {
        $profile_picture_response = set_user_picture(
            $_FILES['profile_picture']['tmp_name'],
            $_FILES['profile_picture']['type'],
            $uid,
            $token
        );
    }

    // Contact the PolicyCloud Marketplace API for password change.
    if (!empty($data['password'])) {

        if (!empty($data['password-confirm'])) {
            if ($data['password'] !== $data['password-confirm']) throw new RuntimeException('Password and password confirmation should match!');
            if (
                !empty(preg_match('@[A-Z]@', $data['password'])) &&
                !empty(preg_match('@[a-z]@', $data['password'])) &&
                !empty(preg_match('@[0-9]@', $data['password'])) &&
                !empty(preg_match('@[^\w]@', $data['password'])) &&
                strlen($data['password']) < 8
            ) throw new RuntimeException('Password should be at least 8 characters in length and should include at least one upper case letter, one number, and one special character.');
        } else throw new RuntimeException("Please fill in the password confirmation when changing your password.");
        if (empty($data['current-password'])) throw new RuntimeException('Please insert your current password before changing it.');

        try {
            $password_response = policyCloudMarketplaceAPIRequest(
                'POST',
                '/accounts/users/password/change',
                [
                    'old_password' => $data['current-password'],
                    'new_password' => $data['password']
                ],
                $token
            );
        } catch (Exception $e) {
        }
    }

    if (!is_array($data['social-title']) || !is_array($data['social-url'])) {
        $data['social-title'] = [$data['social-title']];
        $data['social-url'] = [$data['social-url']];
    }

    try {
        $information_response = policyCloudMarketplaceAPIRequest(
            'PUT',
            '/accounts/users/information/' . $uid,
            [
                'info' => [
                    'name' => $data['name'],
                    'surname' => $data['surname'],
                    'title' => $data['title'],
                    'gender' => $data['gender'],
                    'organization' => $data['organization'],
                    'email' => $data['email'],
                    'phone' => $data['phone'],
                    'social' => (empty($data['social-title'][0]) || empty($data['social-url'][0])) ? [''] : array_map(function ($k, $v) use ($data) {
                        return $v . ":" . $data['social-url'][$k];
                    }, $data['social-title']),
                    'about' => $data['about']
                ],
                'profile_parameters' => [
                    'public_email' => intval($data['public-email']),
                    'public_phone' => intval($data['public-phone']),
                ]
            ],
            $token
        );
    } catch (Exception $e) {
    }

    // Return encrypted token.
    try {
        if (!empty($information_response)) {
            return openssl_encrypt($information_response['token'], "AES-128-ECB", $options['encryption_key']);
        } elseif (!empty($password_response)) {
            return openssl_encrypt($password_response['token'], "AES-128-ECB", $options['encryption_key']);
        } elseif (!empty($profile_picture_response)) {
            return openssl_encrypt($profile_picture_response['token'], "AES-128-ECB", $options['encryption_key']);
        } else {
            throw new ErrorException("Please update the fields before submitting.");
        }
    } catch (Exception $e) {
        throw new ErrorException($e->getMessage());
    }
}

/**
 * Enact account editing using the Marketplace API.
 * For more information concerning the schema of the account data, please visit:
 * https://documenter.getpostman.com/view/16776360/TzsZs8kn#17a87988-323b-4209-b93c-ea3854616ab3 
 * 
 * @param	array $data The user's password
 * @param   string $uid The valid username of the user whose information will be edited.
 * @param   array $token The encoded access token of the requesting user.
 * @usedby PolicyCloud_Marketplace_Public::account_edit_handler()
 * 
 * @uses    policyCloudMarketplaceAPIRequest()
 * 
 * @since	1.0.0
 * @author  Alexandros Raikos <araikos@unipi.gr>
 */
function account_deletion($username, $token, $password)
{
    if (policyCloudMarketplaceAPIRequest(
        'DELETE',
        '/accounts/users/delete/' . $username,
        ['password' => $password],
        $token
    )) {
        return true;
    }
}
