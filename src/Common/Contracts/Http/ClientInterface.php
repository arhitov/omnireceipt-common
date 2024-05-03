<?php
/**
 * Core components for the Omnireceipt PHP fiscal receipt processing library
 *
 * @link      https://github.com/omnireceipt/common
 * @package   omnireceipt/common
 * @license   MIT
 * @copyright Copyright (c) 2024, Alexander Arhitov, clgsru@gmail.com
 */

namespace Omnireceipt\Common\Contracts\Http;

use Omnireceipt\Common\Exceptions\Http\NetworkException;
use Omnireceipt\Common\Exceptions\Http\RequestException;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\StreamInterface;
use Psr\Http\Message\UriInterface;

interface ClientInterface
{
    /**
     * Creates a new PSR-7 request.
     *
     * @param string $method
     * @param string|UriInterface $uri
     * @param array $headers
     * @param string|StreamInterface|null $body
     * @param string $protocolVersion
     *
     * @return \Omnireceipt\Common\Contracts\Http\Response\ResponseInterface
     * @throws NetworkException if there is an error with the network or the remote server cannot be reached.
     * @throws RequestException when the HTTP client is passed a request that is invalid and cannot be sent.
     */
    public function request(
        string                 $method,
        UriInterface|string    $uri,
        array                  $headers = [],
        StreamInterface|string $body = null,
        string                 $protocolVersion = '1.1',
    ): ResponseInterface;
}
