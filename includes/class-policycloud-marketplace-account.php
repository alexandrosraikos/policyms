<?php

abstract class PolicyCloud_Marketplace_Account
{
    protected string $id;
    protected string $token;

    protected ?array $picture = null;

    public function __construct(string $username)
    {
        $this->id = $username;
        $this->token = $this->retrieve_token();
    }

    protected static function get_option(array|string $keys): array|string
    {
        $options = get_option('policycloud_marketplace_plugin_settings');
        if (is_array($keys)) {
            foreach ($keys as $key) {
                if (empty($options[$key])) {
                    throw new PolicyCloudMarketplaceMissingOptionsException(
                        "Please finish setting up PolicyCloud Marketplace for WordPress in the Dashboard."
                    );
                }
            }
            return array_filter(
                $options,
                function ($option_key) use ($keys) {
                    return key_exists($option_key, $keys);
                },
                ARRAY_FILTER_USE_KEY
            );
        } elseif (is_string($keys)) {
            if (empty($options[$keys])) {
                throw new PolicyCloudMarketplaceMissingOptionsException(
                    "Please finish setting up PolicyCloud Marketplace for WordPress in the Dashboard."
                );
            } else {
                return $options[$keys];
            }
        }
    }

    protected static function encrypt_token($token)
    {
        return openssl_encrypt($token, "AES-128-ECB", self::get_option('encryption_key'));
    }

    protected static function decrypt_token($token)
    {
        return openssl_decrypt($token, "AES-128-ECB", self::get_option('encryption_key'));
    }

    /**
     * Retrieve and decrypt the token from the user's browser.
     * 
     * @since	1.0.0
     * @author  Alexandros Raikos <araikos@unipi.gr>
     * 
     * @version 1.1.0
     */
    protected function retrieve_token()
    {
        // Retrieve saved token.
        if (!empty($_COOKIE['ppmapi-token'])) {
            return $this->decrypt_token(filter_var($_COOKIE['pcmapi-token'], FILTER_SANITIZE_STRING));
        } else {
            throw new PolicyCloudMarketplaceUnauthorizedRequestException("The token could not be found.");
        };
    }

    abstract public static function register(array $information);
    abstract public static function authenticate(string $id, string $password): string;

    abstract protected static function inspect(array $information, array $required);
    abstract public function read(array $fields): array;

    abstract public function update(array $information, ?array $picture): string;
    abstract public function delete(string $current_password): void;
}