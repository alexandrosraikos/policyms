<?php

class PolicyCloud_Marketplace_User extends PolicyCloud_Marketplace_Account
{
    public array $information;
    public array $metadata;
    public array $preferences;

    protected ?array $statistics;
    protected ?array $descriptions;
    protected ?array $reviews;
    protected ?string $picture;

    public function __construct(?string $username = null)
    {
        if (isset($username)) {
            $data = $this->get_account_data($username);
            parent::__construct($username);
        } else {
            $data = $this->get_account_data();
            parent::__construct($data['username']);
        }

        $this->information = $data['info'];
        $this->metadata = $data['account'];
        $this->preferences = $data['profile_parameters'];
    }

    public function __get(string $name)
    {
        switch ($name) {
            case 'information':
            case 'metadata':
            case 'username':
            case 'preferences':
                return $this->${$name};
            case 'statistics':
                return $this->statistics ?? $this->get_statistics();
            case 'descriptions':
                return $this->descriptions ?? $this->get_descriptions();
            case 'reviews':
                return $this->reviews ?? $this->get_reviews();
            case 'approvals':
                return PolicyCloud_Marketplace_Description::get_pending();
            case 'picture':
                return $this->picture ?? $this->get_picture();
            default:
                throw new Exception(
                    "The property \"" . $name . "\" does not exist in " . get_class($this) . "."
                );
        }
    }

    /**
     * ------------
     * Basic Methods
     * ------------
     */

    public function is_admin(): bool
    {
        return $this->get_role() == 'admin';
    }


    public function get_role(): string
    {
        return $this->metadata['role'];
    }

    public function resend_verification_email(): void
    {
        PolicyCloud_Marketplace::api_request(
            'POST',
            '/accounts/user/verification/resend',
            [
                'email' => $this->information['email']
            ],
            $this->token
        );
    }

    public function update(array $data, ?array $picture = null): ?string
    {
        // Inspect uploaded information.
        self::inspect($data);

        // Upload new profile picture.
        if (isset($picture)) {
            $token = $this->update_picture($picture);
        }

        // Contact the PolicyCloud Marketplace API for password change.
        if (!empty($data['password'])) {
            $response = PolicyCloud_Marketplace::api_request(
                'POST',
                '/accounts/users/password/change',
                [
                    'old_password' => $data['current-password'],
                    'new_password' => $data['password']
                ],
                $this->token
            );
        }

        $response = PolicyCloud_Marketplace::api_request(
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
        if (!empty($response['token'])) {
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

    protected function get_statistics(): array
    {
        $this->statistics = PolicyCloud_Marketplace::api_request(
            'GET',
            '/accounts/users/statistics/' . $this->id,
            [],
            $this->token
        )['results'];

        return $this->statistics;
    }

    protected function get_descriptions(): array
    {
        require_once plugin_dir_path(dirname(__FILE__)) . 'public/class-policycloud-marketplace-description.php';
        $this->descriptions = PolicyCloud_Marketplace_Description::get_owned($this, $this->token);
        return $this->descriptions;
    }

    protected function get_reviews(): array
    {
        require_once plugin_dir_path(dirname(__FILE__)) . 'public/class-policycloud-marketplace-review.php';
        $this->reviews = PolicyCloud_Marketplace_Review::get_owned($this, $this->token);
        return $this->reviews;
    }

    /**
     *
     * User Picture
     * ------------
     *
     */

    public function get_picture()
    {
        if ($this->preferences['profile_image'] == 'default_image_users') {
            $this->picture = get_site_url(null, '/wp-content/plugins/policycloud-marketplace/public/assets/svg/user.svg');
        } else {
            $picture_data = PolicyCloud_Marketplace::api_request(
                'GET',
                '/images/' . $this->preferences['profile_image'],
                [],
                $this->token,
                [
                    'Content-Type: application/octet-stream',
                    (!empty($this->token) ? ('x-access-token: ' . $this->token) : null)
                ],
            );
            $this->picture = 'data:image/*;base64,' .base64_encode($picture_data);
        }

        return $this->picture;
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

    public function update_picture(array $picture): ?string
    {

        if ($picture['error'] == 0) {
            if ($picture['type'] != 'image/jpeg' &&
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
                    'asset' => new CURLFile($picture['tmp_name'], $picture['type'], $this->id)
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
        } elseif ($picture['error'] == 4) {
            return null;
        }
    }

    public function get_data_copy()
    {
        $response = PolicyCloud_Marketplace::api_request(
            'GET',
            '/accounts/users/data',
            [],
            $this->token
        );

        return $response['account_data'];
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

    public static function reset_password(string $email)
    {
        PolicyCloud_Marketplace::api_request(
            'POST',
            '/accounts/users/password/reset',
            [
                'email' => $email
            ]
        );
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

    protected static function get_account_data(string $id = null): array
    {
        $response = PolicyCloud_Marketplace::api_request(
            'GET',
            '/accounts/users/information' . (isset($id) ? '/' . $id : ''),
            [],
            PolicyCloud_Marketplace_Account::retrieve_token()
        );

        return $response['result'];
    }

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
            if (!empty(preg_match('@[A-Z]@', $information['password'])) &&
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

    protected static function implode_urls($titles, $urls): array
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
                array_keys($titles),
                $titles
            );
        } else {
            return [''];
        }
    }
}
