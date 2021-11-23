<?php

class PolicyCloud_Marketplace_User extends PolicyCloud_Marketplace_Account
{
    public function __construct(?string $username = null)
    {
        if (isset($username)) {
            parent::__construct($username);
        } else {
            parent::__construct($this->read('username'));
        }
    }

    /**
     * ------------
     * Basic Methods
     * ------------
     */

    public function is_admin(): bool {
        return $this->get_role == 'admin';
    }


    public function get_role (): string {
        return $this->read('metadata')['role'];
    }

    
    /**
     * 'picture',
     * 'information',
     * 'statistics',
     * 'descriptions' + ,
     * 'reviews' + ,
     * 'approvals' + ,
     * 'metadata',
     * 'preferences'
     */
    public function read(string ...$fields): mixed
    {
        $user_data = [];

        foreach ($fields as $field) {
            switch ($field) {
                case 'information':
                case 'metadata':
                case 'username':
                case 'preferences':
                    if (
                        !array_key_exists('information', $user_data) &&
                        !array_key_exists('metadata', $user_data) &&
                        !array_key_exists('preferences', $user_data) &&
                        !array_key_exists('username', $user_data)
                    ) {
                        $data = $this->get_information();
                        array_merge($user_data, [
                            'information' => $data['info'],
                            'metadata' => $data['account'],
                            'username' => $data['username'],
                            'preferences' => $data['profile_parameters'],
                        ]);
                    }
                    break;
                case 'statistics':
                    $user_data['statistics'] = $this->get_statistics();
                    break;
                case 'descriptions':
                    // TODO @alexandrosraikos: Retrieve descriptions (#60).
                    break;
                case 'reviews':
                    // TODO @alexandrosraikos: Retrieve reviews (#60).
                    break;
                case 'approvals':
                    // TODO @alexandrosraikos: Retrieve approvals (#60).
                    break;
                case 'picture':
                    $user_data['picture'] = $this->get_picture();
                default:
                    throw new PolicyCloudMarketplaceInvalidDataException(
                        "No user fields were set."
                    );
            }
        }

        if (count($fields) == 1) {
            return $user_data[$fields[0]];
        }  else {
            return $user_data; 
        }
    }

    public function resend_verification_email(): void {
        PolicyCloud_Marketplace::api_request(
            'POST',
            '/accounts/user/verification/resend',
            [
                'email' => $this->read('information')['email']
            ],
            $this->token
        );
    }

    public function update(array $data, ?array $picture = null): string
    {
        // Inspect uploaded information.
        self::inspect($data);

        // Upload new profile picture.
        if (isset($picture)) {
            $token = $this->update_picture($picture);
        }

        // Contact the PolicyCloud Marketplace API for password change.
        if (!empty($data['password'])) {
            $token = PolicyCloud_Marketplace::api_request(
                'POST',
                '/accounts/users/password/change',
                [
                    'old_password' => $data['current-password'],
                    'new_password' => $data['password']
                ],
                $this->token
            );
        }

        $token = PolicyCloud_Marketplace::api_request(
            'PUT',
            '/accounts/users/information/' . $this->id,
            [
                'info' => [
                    'name' => $data['name'],
                    'surname' => $data['surname'],
                    'title' => $data['title'],
                    'gender' => $data['gender'],
                    'organization' => $data['organization'],
                    'email' => $data['email'],
                    'phone' => $data['phone'],
                    'social' => $this->implode_urls(
                        $data['socials-title'],
                        $data['socials-url']
                    ),
                    'about' => $data['about']
                ],
                'profile_parameters' => [
                    'public_email' => intval($data['public-email']),
                    'public_phone' => intval($data['public-phone']),
                ]
            ],
            $this->token
        );

        // Return encrypted token.
        if (!empty($token)) {
            return parent::persist_token($token);
        }
    }

    public function delete(string $current_password): void
    {
        PolicyCloud_Marketplace::api_request(
            'DELETE',
            '/accounts/users/delete/' . $this->id,
            ['password' => $current_password],
            $this->token
        );
    }

    /**
     * ------------
     * Internal Methods
     * ------------
     */

    /**
     * 
     * User Data
     * ------------
     * 
     */

    protected function get_information(): array
    {
        $response = PolicyCloud_Marketplace::api_request(
            'GET',
            '/accounts/users/information/' . $this->id,
            [],
            $this->token
        );

        return $response['result'];
    }

    protected function get_statistics(): array
    {
        $response = PolicyCloud_Marketplace::api_request(
            'GET',
            '/accounts/users/statistics/' . $this->id,
            [],
            $this->token
        );

        return $response['results'];
    }

    /**
     * 
     * User Picture
     * ------------
     * 
     */

    public function get_picture()
    {
        return PolicyCloud_Marketplace::api_request(
            'GET',
            '/images/' . $this->id,
            [],
            $this->token,
            [
                'Content-Type: application/octet-stream',
                (!empty($token) ? ('x-access-token: ' . $token) : null)
            ],
        );
    }

    public function delete_picture(): string
    {
        $response = PolicyCloud_Marketplace::api_request(
            'DELETE',
            '/accounts/users/image/' . $this->id,
            [],
            $this->token,
            [
                'Content-Type: application/json',
                'x-access-token: ' . $this->token,
                'x-more-time: ' . PolicyCloud_Marketplace_Public::get_plugin_setting(true, 'api_access_token')
            ],
        );
        return parent::persist_token($response['token']);
    }

    public function update_picture(array $picture): string
    {

        if ($picture['error'] == 0) {
            if (
                $picture['type'] != 'image/jpeg' &&
                $picture['type'] != 'image/png'
            ) {
                throw new PolicyCloudMarketplaceInvalidDataException(
                    "Supported formats for  profile pictures are .png and .jpg/.jpeg."
                );
            }
            if ($picture['size'] > 1000000) {
                throw new PolicyCloudMarketplaceInvalidDataException(
                    "The image file is too large. Please upload a file less than 1MB in size."
                );
            }

            $response = PolicyCloud_Marketplace::api_request(
                'PUT',
                '/accounts/users/image',
                [
                    'asset' => new CURLFile($picture['tmp_name'],  $picture['type'], $this->id)
                ],
                $this->token,
                [
                    'x-mimetype: ' .  $picture['type'],
                    'x-access-token: ' . $this->token,
                    'x-more-time: ' . PolicyCloud_Marketplace_Public::get_plugin_setting(true, 'api_access_token')
                ],
                true
            );

            return parent::persist_token($response['token']);
        } elseif ($picture['error'] == 4);
    }

    /**
     * ------------
     * Basic Methods (Static)
     * ------------
     */

    public static function authenticate(string $id, string $password): string
    {
        // Get the authorized token.
        $response = PolicyCloud_Marketplace::api_request(
            'POST',
            '/accounts/users/authorization',
            [
                (is_email($id) ? 'email' : 'username') => $id,
                'password' => $password
            ]
        );

        return parent::persist_token($response['token']);
    }

    public static function register(array $information): string
    {
        if (!function_exists('availability')) {
            function availability($username)
            {
                $response = PolicyCloud_Marketplace::api_request(
                    'GET',
                    '/accounts/username/availability',
                    [],
                    null,
                    ["x-username: " . $username],
                );

                // Return status.
                return $response['_status'] == 'successful';
            }
        }

        self::inspect($information, [
            'username',
            'password',
            'email',
            'name',
            'surname'
        ]);

        // Username availability check.
        if (!availability($information['username'])) {
            throw new PolicyCloudMarketplaceInvalidDataException("Username already exists.");
        }

        $response = PolicyCloud_Marketplace::api_request(
            'POST',
            '/accounts/users/registration',
            [
                'username' => $information['username'],
                'account' => [
                    'password' => $information['password']
                ],
                'info' => [
                    'name' => $information['name'],
                    'surname' => $information['surname'],
                    'title' => $information['title'] ?? '',
                    'gender' => $information['gender'] ?? '',
                    'organization' => $information['organization'] ?? '',
                    'phone' => $information['phone'] ?? '',
                    'email' => $information['email'],
                    'about' => $information['about'],
                    'social' => self::implode_urls($information['social-title'], $information['social-url'])
                ]
            ]
        );

        return parent::persist_token($response['token']);
    }

    /**
     * ------------
     * Internal Methods (Static)
     * ------------
     */

    protected static function inspect(array $information, array $required = null): void
    {
        // Check required fields.
        if (isset($required)) {
            foreach ($required as $field) {
                if (empty($information[$field])) {
                    throw new PolicyCloudMarketplaceInvalidDataException("Please fill in all the required fields.");
                }
            }
        }

        // Check email.
        if (!filter_var($information["email"], FILTER_VALIDATE_EMAIL)) {
            throw new PolicyCloudMarketplaceInvalidDataException("Please enter a valid email address.");
        }

        // Check username.
        if (!empty($information['username'])) {
            if (strlen($information['username']) <= 2) {
                throw new PolicyCloudMarketplaceInvalidDataException("Username must be at least 2 characters.");
            }
        }

        // Check password and confirmation.
        if (!empty($information['password'])) {
            if (
                !empty(preg_match('@[A-Z]@', $information['password'])) &&
                !empty(preg_match('@[a-z]@', $information['password'])) &&
                !empty(preg_match('@[0-9]@', $information['password'])) &&
                !empty(preg_match('@[^\w]@', $information['password'])) &&
                strlen($information['password']) < 8
            ) {
                throw new PolicyCloudMarketplaceInvalidDataException('Password should be at least 8 characters and  include at least one uppercase letter, a number, and a special character.');
            }
            if (!empty($information['password-confirm'])) {
                if ($information['password'] !== $information['password-confirm']) {
                    throw new PolicyCloudMarketplaceInvalidDataException('Password and password confirmation should match.');
                }
            }
        }

        // Check title.
        if (!empty($information['title'])) {
            if (!in_array(
                $information['title'],
                [
                    'Mr.',
                    'Ms.',
                    'Mrs.',
                    'Dr.',
                    'Prof.',
                    'Sir',
                    'Miss',
                    'Mx.',
                    '-'
                ]
            )) {
                throw new InvalidArgumentException("Please select a valid title.");
            }
        }

        // Check gender.
        if (!empty($information['gender'])) {
            if (!in_array(
                $information['gender'],
                [
                    'male',
                    'female',
                    'transgender',
                    'genderqueer',
                    'questioning',
                    '-'
                ]
            )) {
                throw new InvalidArgumentException("Please select a gender from the list.");
            }
        }
    }

    protected static function implode_urls(array|string $titles, array|string $urls): array
    {
        if (!empty($titles) && !empty($urls)) {
            if (!is_array($titles) || !is_array($urls)) {
                $titles = [$titles];
                $urls = [$urls];
            }
            return array_map(
                function ($k, $v) use ($urls) {
                    return $v . ":" . $urls[$k];
                },
                $titles
            );
        } else return [''];
    }
}
