<?php
/**
 * Core components for the Omnireceipt PHP fiscal receipt processing library
 *
 * @link      https://github.com/omnireceipt/common
 * @package   omnireceipt/common
 * @license   MIT
 * @copyright Copyright (c) 2024, Alexander Arhitov, clgsru@gmail.com
 */

namespace Omnireceipt\Common\Http\Response;

use Omnireceipt\Common\Contracts\Http\ResponseInterface;
use Omnireceipt\Common\Exceptions\Http\Exception;
use Omnireceipt\Common\Http\Request\AbstractRequest;

abstract class AbstractResponse implements ResponseInterface
{
    /**
     * Constructor
     *
     * @param AbstractRequest $request the initiating request.
     * @param mixed $data The data contained in the response.
     */
    public function __construct(
        protected AbstractRequest $request,
        protected mixed $data,
        protected int $code = 0,
    ) {
    }

    /**
     * Get the initiating request object.
     *
     * @return AbstractRequest
     */
    public function getRequest(): AbstractRequest
    {
        return $this->request;
    }

    /**
     * Get the response data
     *
     * @return mixed
     */
    public function getData(): mixed
    {
        return $this->data;
    }

    /**
     * Get the response code
     *
     * @return mixed
     */
    public function getCode(): int
    {
        return $this->code;
    }

    /**
     * Response is successful
     *
     * @return bool
     */
    public function isSuccessful(): bool
    {
        return $this->getCode() === 200;
    }

    /**
     * If the request fails, throws an exception.
     *
     * @return $this
     */
    public function orFail(): static
    {
        $code = $this->getCode();
        if (400 <= $code && $code <= 499) {
            throw new Exception("Client Error \"$code\"", $this->getRequest());
        } elseif (500 <= $code && $code <= 599) {
            throw new Exception("Server Error \"$code\"", $this->getRequest());
        }
        return $this;
    }
}
