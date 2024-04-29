<?php

namespace Omnireceipt\Common\Entities;

use Omnireceipt\Common\Contracts\CustomerInterface;
use Omnireceipt\Common\Supports\PropertiesTrait;

/**
 * @method string getId()
 * @method self setId(string $value)
 * @method string getName()
 * @method self setName(string $value)
 * @method string getPhone()
 * @method self setPhone(string $value)
 * @method string getEmail()
 * @method self setEmail(string $value)
 */
class Customer implements CustomerInterface
{
    use PropertiesTrait;

    const RULES = [
        'id'    => ['nullable', 'string'],
        'name'  => ['required', 'string'],
        'phone' => ['nullable', 'string'],
        'email' => ['nullable', 'string'],
    ];

    public function __construct(
        array $properties = [],
    ) {
        $this->properties = $properties;
    }
}