<?php
/**
 * Core components for the Omnireceipt PHP fiscal receipt processing library
 *
 * @link      https://github.com/omnireceipt/common
 * @package   omnireceipt/common
 * @license   MIT
 * @copyright Copyright (c) 2024, Alexander Arhitov, clgsru@gmail.com
 */

namespace Omnireceipt\Common;

use Omnireceipt\Common\Contracts\Http\ClientInterface;
use Omnireceipt\Common\Exceptions\RuntimeException;
use Omnireceipt\Common\Supports\Helper;
use Symfony\Component\HttpFoundation\Request;

class GatewayFactory
{
    /**
     * Internal storage for all available gateways
     *
     * @var array
     */
    private array $gateways = [];

    /**
     * All available gateways
     *
     * @return array An array of gateway names
     */
    public function all(): array
    {
        return $this->gateways;
    }

    /**
     * Replace the list of available gateways
     *
     * @param array $gateways An array of gateway names
     */
    public function replace(array $gateways): void
    {
        $this->gateways = $gateways;
    }

    /**
     * Register a new gateway
     *
     * @param string $className Gateway name
     */
    public function register(string $className): void
    {
        if (! in_array($className, $this->gateways)) {
            $this->gateways[] = $className;
        }
    }

    /**
     * Create a new gateway instance
     *
     * @param string $class Gateway name
     * @param ClientInterface|null $httpClient A HTTP Client implementation
     * @param Request|null $httpRequest A Symfony HTTP Request implementation
     * @return AbstractGateway An object of class $class is created and returned
     * @throws RuntimeException If no such gateway is found
     */
    public function create(
        string          $class,
        ClientInterface $httpClient = null,
        Request         $httpRequest = null,
    ): AbstractGateway {
        $class = Helper::getGatewayClassName($class);

        if (! class_exists($class)) {
            throw new RuntimeException("Class \"{$class}\" not found");
        }

        return new $class($httpClient, $httpRequest);
    }
}
