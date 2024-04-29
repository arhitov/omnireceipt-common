<?php

namespace Omnireceipt\Common\Exceptions\Http;

use Omnireceipt\Common\Exceptions\RuntimeException;
use Psr\Http\Message\RequestInterface;

class Exception extends RuntimeException
{
    public RequestInterface $request;

    public function __construct($message, RequestInterface $request, $previous = null)
    {
        $this->request = $request;

        parent::__construct($message, 0, $previous);
    }
}
