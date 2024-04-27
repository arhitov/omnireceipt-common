<?php

namespace Omnireceipt\Common\Exceptions\Property;

class PropertyValidateException extends PropertyException
{
    public function __construct(
        public object $object,
        public array $error,
        string $message = '',
    ) {
        parent::__construct($message);
    }
}
