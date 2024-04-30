<?php

namespace Omnireceipt\Common\Supports;

use ErrorException;
use Omnireceipt\Common\Exceptions\Parameters\ParameterNotFoundException;
use Omnireceipt\Common\Exceptions\Parameters\ParameterValidateException;
use Symfony\Component\HttpFoundation\ParameterBag;

trait ParametersTrait
{
    /**
     * Internal storage of all parameters.
     */
    protected ParameterBag $parameters;

    /**
     * List of parameters that failed after validation..
     */
    protected array $parametersError = [];

    /**
     * @return array
     */
    public function getRules(): array
    {
        $className = static::class;
        return defined("{$className}::RULES")
            ? $className::RULES
            : [];
    }

    /**
     * @param string $name
     * @param array $arguments
     * @return self|mixed
     * @throws ErrorException
     * @throws ParameterNotFoundException
     */
    public function __call(string $name, array $arguments)
    {
        if (preg_match('/^([gs]et)([A-Z].*)?$/', $name, $out)) {
            $key = strtolower(preg_replace('/(?<!^)[A-Z]/', '_$0', $out[2]));
            if (str_ends_with($key, '_or_null')) {
                $allowNull = true;
                $key = substr($key, 0, -8);
            } else {
                $allowNull = false;
            }
            if ('get' === $out[1]) {
                if (! $this->parameters->has($key)) {
                    return $allowNull
                        ? null
                        : throw new ParameterNotFoundException($this, $key);
                }
                return $this->parameters->get($key);
            }
            if ('set' === $out[1]) {
                $this->parameters->set($key, $arguments[0]);
                return $this;
            }
        }

        $className = $this::class;
        throw new ErrorException("Method \"{$className}:{$name}\" not found");
    }

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
     * @return void
     * @throws ParameterValidateException
     */
    public function validateOrFail(): void
    {
        if (! $this->validate()) {
            throw new ParameterValidateException($this, $this->validateLastError());
        }
    }

    /**
     * @return bool
     */
    public function validate(): bool
    {
        $this->parametersError = [];
        foreach ($this->getRules() as $key => $rules) {
            $value = $this->parameters->get($key, null);
            if (is_null($value)) {
                if (in_array('nullable', $rules)) {
                    continue;
                }
                if (in_array('required', $rules)) {
                    $this->parametersError[$key] = ['The field must be present and not empty.'];
                    continue;
                }
            }

            $error = [];
            foreach ($rules as $rule) {
                if ('numeric' === $rule && ! is_numeric($value)) {
                    $error[] = 'The field must be a numeric.';
                }
                if ('string' === $rule && ! is_string($value)) {
                    $error[] = 'The field must be a string.';
                }
                if (str_starts_with($rule, 'in:') && ! in_array($value, explode(',', substr($rule, 3)))) {
                    $error[] = 'The field must be included in this list of values.';
                }
                if ($rule === 'bool' && ! is_bool($value)) {
                    $error[] = 'The field must be a boolean.';
                }
            }
            if (! empty($error)) {
                $this->parametersError[$key] = $error;
            }
        }

        return empty($this->parametersError);
    }

    /**
     * @return array
     */
    public function validateLastError(): array
    {
        return [
            'parameters' => $this->parametersError,
        ];
    }
}
