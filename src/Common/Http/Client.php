<?php
/**
 * Core components for the Omnireceipt PHP fiscal receipt processing library
 *
 * @link      https://github.com/omnireceipt/common
 * @package   omnireceipt/common
 * @license   MIT
 * @copyright Copyright (c) 2024, Alexander Arhitov, clgsru@gmail.com
 */

namespace Omnireceipt\Common\Http;

use Exception;
use Http\Discovery\Psr17FactoryDiscovery;
use Http\Discovery\Psr18ClientDiscovery;
use Omnireceipt\Common\Exceptions\Http\NetworkException;
use Omnireceipt\Common\Exceptions\Http\RequestException;
use Omnireceipt\Common\Contracts\Http\ClientInterface;
use Psr\Http\Client\ClientInterface as PsrClientInterface;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\RequestFactoryInterface as PsrRequestFactoryInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\StreamInterface;

class Client implements ClientInterface
{

    /**
     * The Http Client which implements `public function sendRequest(RequestInterface $request)`
     * Note: Will be changed to PSR-18 when released
     */
    private PsrClientInterface $httpClient;

    private PsrRequestFactoryInterface $requestFactory;

    public function __construct(PsrClientInterface $httpClient = null, PsrRequestFactoryInterface $requestFactory = null)
    {
        $this->httpClient = $httpClient ?: Psr18ClientDiscovery::find();
        $this->requestFactory = $requestFactory ?: Psr17FactoryDiscovery::findRequestFactory();
    }

    /**
     * @param $method
     * @param $uri
     * @param array $headers
     * @param string|array|resource|StreamInterface|null $body
     * @param string $protocolVersion
     * @return ResponseInterface
     * @throws \Http\Client\Exception
     * @throws \Psr\Http\Client\ClientExceptionInterface
     */
    public function request(
        $method,
        $uri,
        array $headers = [],
        $body = null,
        string $protocolVersion = '1.1'
    ): ResponseInterface {
        $request = $this->requestFactory->createRequest($method, $uri, $headers, $body, $protocolVersion);

        return $this->sendRequest($request);
    }

    /**
     * @param RequestInterface $request
     * @return ResponseInterface
     * @throws \Http\Client\Exception
     * @throws \Psr\Http\Client\ClientExceptionInterface
     */
    private function sendRequest(RequestInterface $request): ResponseInterface
    {
        try {
            return $this->httpClient->sendRequest($request);
        } catch (\Http\Client\Exception\NetworkException $networkException) {
            throw new NetworkException($networkException->getMessage(), $request, $networkException);
        } catch (Exception $exception) {
            throw new RequestException($exception->getMessage(), $request, $exception);
        }
    }
}
