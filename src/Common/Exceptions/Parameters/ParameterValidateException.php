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
