<?php

namespace Omnireceipt\Common\Exceptions\Property;

class PropertyNotFoundException extends PropertyException
{
    public function __construct(
        public string $property,
        string $message = '',
    ) {
        parent::__construct($message);
    }
}
