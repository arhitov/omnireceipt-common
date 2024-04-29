<?php

namespace Omnireceipt\Common\Supports;

use ErrorException;
use Omnireceipt\Common\Exceptions\Property\PropertyNotFoundException;
use Omnireceipt\Common\Exceptions\Property\PropertyValidateException;

trait PropertiesTrait
{
    protected array $properties = [];
    protected array $propertiesError = [];

    /**
     * @param string $name
     * @param array $arguments
     * @return self|mixed
     * @throws ErrorException
     * @throws PropertyNotFoundException
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
                if (! array_key_exists($key, $this->properties)) {
                    return $allowNull
                        ? null
                        : throw new PropertyNotFoundException($this, $key);
                }
                return $this->properties[$key];
            }
            if ('set' === $out[1]) {
                $this->properties[$key] = $arguments[0];
                return $this;
            }
        }

        throw new ErrorException("Method \"{$name}\" not found");
    }

    /**
     * @return array
     */
    public function getProperties(): array
    {
        return $this->properties;
    }

    /**
     * @return array
     */
    public function getRules(): array
    {
        return static::RULES;
    }

    /**
     * @return void
     * @throws PropertyValidateException
     */
    public function validateOrFail(): void
    {
        if (! $this->validate()) {
            throw new PropertyValidateException($this, $this->getLastError());
        }
    }

    /**
     * @return bool
     */
    public function validate(): bool
    {
        $this->propertiesError = [];
        foreach ($this->getRules() as $key => $rules) {
            $value = $this->properties[$key] ?? null;
            if (is_null($value)) {
                if (in_array('nullable', $rules)) {
                    continue;
                }
                if (in_array('required', $rules)) {
                    $this->propertiesError[$key] = ['The field must be present and not empty.'];
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
                $this->propertiesError[$key] = $error;
            }
        }

        return empty($this->propertiesError);
    }

    /**
     * @return array
     */
    public function getLastError(): array
    {
        return [
            'properties' => $this->propertiesError,
        ];
    }
}
