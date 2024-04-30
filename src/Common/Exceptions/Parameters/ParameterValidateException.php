<?php

namespace Omnireceipt\Common\Exceptions\Parameters;

class ParameterValidateException extends ParameterException
{
    public function __construct(
        public object $object,
        public array $error,
        string $message = '',
    ) {
        parent::__construct($message);
    }
}
