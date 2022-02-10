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
        parent::__construct($message, 0, $previous);
    }
}

class PolicyCloudMarketplaceMissingOptionsException extends Exception
{
    public function __construct($message, $code = 0, Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }
}

class PolicyCloudMarketplaceAPIError extends Exception
{
    public int $http_status;
    public function __construct($message, int $http_status, $code = 0, Throwable $previous = null)
    {
        $this->http_status = $http_status;
        parent::__construct($message, $code, $previous);
    }
}
