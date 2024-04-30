<?php

namespace Omnireceipt\Common\Exceptions\Parameters;

class ParameterNotFoundException extends ParameterException
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
