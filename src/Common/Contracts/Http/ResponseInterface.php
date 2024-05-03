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

interface ResponseInterface
{
    /**
     * Get the original request which generated this response
     *
     * @return RequestInterface
     */
    public function getRequest(): RequestInterface;

    /**
     * Get the response data
     *
     * @return mixed
     */
    public function getData(): mixed;

    /**
     * Get the response code
     *
     * @return mixed
     */
    public function getCode(): int;

    /**
     * Response is successful
     *
     * @return bool
     */
    public function isSuccessful(): bool;

    /**
     * If the request fails, throws an exception.
     *
     * @return $this
     */
    public function orFail(): static;
}
