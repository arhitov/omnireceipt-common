<?php

namespace Omnireceipt\Common\Supports;

use Omnireceipt\Common\Exceptions\InvalidRequestException;
use Symfony\Component\HttpFoundation\ParameterBag;

trait ParametersHttpTrait
{
    /**
     * Internal storage of all of the parameters.
     */
    protected ParameterBag $parameters;

    /**
     * Set one parameter.
     *
     * @param string $key Parameter key
     * @param mixed $value Parameter value
     * @return $this
     */
    protected function setParameter(string $key, mixed $value): static
    {
        $this->parameters->set($key, $value);

        return $this;
    }

    /**
     * Get one parameter.
     *
     * @param string $key Parameter key
     * @return mixed A single parameter value.
     */
    protected function getParameter(string $key): mixed
    {
        return $this->parameters->get($key);
    }

    /**
     * Get all parameters.
     *
     * @return array An associative array of parameters.
     */
    public function getParameters(): array
    {
        return $this->parameters->all();
    }

    /**
     * Initialize the object with parameters.
     *
     * If any unknown parameters passed, they will be ignored.
     *
     * @param array $parameters An associative array of parameters
     * @return $this.
     */
    public function initialize(array $parameters = []): static
    {
        $this->parameters = new ParameterBag;

        if (method_exists($this, 'getDefaultParameters')) {
            // set default parameters
            foreach ($this->getDefaultParameters() as $key => $value) {
                if (is_array($value)) {
                    $this->parameters->set($key, reset($value));
                } else {
                    $this->parameters->set($key, $value);
                }
            }
        }

        Helper::initialize($this, $parameters);

        return $this;
    }

    /**
     * Validate the request.
     *
     * This method is called internally by gateways to avoid wasting time with an API call
     * when the request is clearly invalid.
     *
     * @param string $args ... a variable length list of required parameters
     * @throws InvalidRequestException
     */
    public function validateZZZ(...$args): void
    {
        foreach ($args as $key) {
            $value = $this->parameters->get($key);
            if (! isset($value)) {
                throw new InvalidRequestException("The $key parameter is required");
            }
        }
    }
}
