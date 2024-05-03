<?php
/**
 * Core components for the Omnireceipt PHP fiscal receipt processing library
 *
 * @link      https://github.com/omnireceipt/common
 * @package   omnireceipt/common
 * @license   MIT
 * @copyright Copyright (c) 2024, Alexander Arhitov, clgsru@gmail.com
 */

namespace Omnireceipt\Common\Exceptions\Http;

use Omnireceipt\Common\Exceptions\RuntimeException;
use Omnireceipt\Common\Contracts\Http\RequestInterface;
use Psr\Http\Message\RequestInterface as PsrRequestInterface;

class Exception extends RuntimeException
{
    public RequestInterface|PsrRequestInterface $request;

    public function __construct($message, RequestInterface|PsrRequestInterface $request, $previous = null)
    {
        $this->request = $request;

        parent::__construct($message, 0, $previous);
    }
}
