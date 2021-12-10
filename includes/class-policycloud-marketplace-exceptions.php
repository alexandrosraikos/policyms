<?php

class PolicyCloudMarketplaceUnauthorizedRequestException extends Exception
{
    public function __construct($message, $code = 0, Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }
}

class PolicyCloudMarketplaceInvalidDataException extends Exception
{
    public function __construct($message, $code = 0, Throwable $previous = null)
    {
        parent::__construct($message, 0, null);
    }
}

class PolicyCloudMarketplaceMissingOptionsException extends Exception
{
    public function __construct($message, $code = 0, Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }
}
