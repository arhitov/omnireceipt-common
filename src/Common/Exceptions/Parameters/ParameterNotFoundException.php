<?php
/**
 * Core components for the Omnireceipt PHP fiscal receipt processing library
 *
 * @link      https://github.com/omnireceipt/common
 * @package   omnireceipt/common
 * @license   MIT
 * @copyright Copyright (c) 2024, Alexander Arhitov, clgsru@gmail.com
 */

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
