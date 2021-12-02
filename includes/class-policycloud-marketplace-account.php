<?php

abstract class PolicyCloud_Marketplace_Account
{
    public string $id;
    protected string $token;

    protected ?string $picture = null;

    public function __construct(string $id)
    {   
        $this->id = $id;
        $this->token = self::retrieve_token();
    }

    protected static function persist_token($token)
    {
        return openssl_encrypt($token, "AES-128-ECB", PolicyCloud_Marketplace_Public::get_plugin_setting(true, 'encryption_key'));
    }

    private static function decrypt_token($token)
    {
        return openssl_decrypt($token, "AES-128-ECB", PolicyCloud_Marketplace_Public::get_plugin_setting(true, 'encryption_key'));
    }

    /**
     * Retrieve and decrypt the token from the user's browser.
     * 
     * @since	1.0.0
     * @author  Alexandros Raikos <araikos@unipi.gr>
     * 
     * @version 1.1.0
     */
    public static function retrieve_token()
    {
        // Retrieve saved token.
        if (!empty($_COOKIE['pcmapi-token'])) {
            return self::decrypt_token($_COOKIE['pcmapi-token']);
        } else {
            throw new PolicyCloudMarketplaceUnauthorizedRequestException("The token could not be found.");
        };
    }

    public static function is_authenticated(): bool {
        try {
            return !empty(self::retrieve_token());
        } catch (PolicyCloudMarketplaceUnauthorizedRequestException $e) {
            return false;
        }
    }

    abstract public static function register(array $information);
    abstract public static function authenticate(string $id, string $password): string;

    abstract protected static function get_account_data(string $id = null): array;
    abstract public function __get(string $name);
    abstract public function get_role(): string;
    
    abstract public function update(array $information, ?array $picture): ?string;
    abstract public function delete(string $current_password): void;
    
    abstract protected static function inspect(array $information, array $required);
}