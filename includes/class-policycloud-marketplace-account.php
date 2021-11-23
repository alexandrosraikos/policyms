<?php

abstract class PolicyCloud_Marketplace_Account
{
    public string $id;
    protected string $token;

    protected ?array $picture = null;

    public function __construct(string $id)
    {   
        $this->id = $id;
    }

    protected static function persist_token($token)
    {
        return openssl_encrypt($token, "AES-128-ECB", PolicyCloud_Marketplace_Public::get_plugin_setting('encryption_key'));
    }

    private static function decrypt_token($token)
    {
        return openssl_decrypt($token, "AES-128-ECB", PolicyCloud_Marketplace_Public::get_plugin_setting('encryption_key'));
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
        if (!empty($_COOKIE['ppmapi-token'])) {
            return self::decrypt_token(filter_var($_COOKIE['pcmapi-token'], FILTER_SANITIZE_STRING));
        } else {
            throw new PolicyCloudMarketplaceUnauthorizedRequestException("The token could not be found.");
        };
    }

    public static function is_authenticated(): bool {
        return !empty(self::retrieve_token());
    }

    abstract public static function register(array $information);
    abstract public static function authenticate(string $id, string $password): string;

    abstract public function get_role(): string;
    abstract public function read(array $fields): mixed;
    
    abstract public function update(array $information, ?array $picture): string;
    abstract public function delete(string $current_password): void;
    
    abstract protected static function inspect(array $information, array $required);
}