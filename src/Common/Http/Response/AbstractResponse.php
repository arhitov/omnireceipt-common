<?php

namespace Omnireceipt\Common\Http\Response;

use Omnireceipt\Common\Contracts\Http\ResponseInterface;
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
}
