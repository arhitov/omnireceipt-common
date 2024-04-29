<?php

namespace Omnireceipt\Common\Exceptions\Property;

class PropertyNotFoundException extends PropertyException
{
    public function __construct(
        public object $class,
        public string $property,
        string $message = '',
    ) {
        if (empty($message)) {
            $className = $class::class;
            $message = "Property \"{$className}::{$property}\" not fount";
        }

        parent::__construct($message);
    }
}
