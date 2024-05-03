<?php
/**
 * Core components for the Omnireceipt PHP fiscal receipt processing library
 *
 * @link      https://github.com/omnireceipt/common
 * @package   omnireceipt/common
 * @license   MIT
 * @copyright Copyright (c) 2024, Alexander Arhitov, clgsru@gmail.com
 */

namespace Omnireceipt\Common\Entities;

use Omnireceipt\Common\Contracts\SellerInterface;
use Omnireceipt\Common\Supports\ParametersTrait;

abstract class Seller implements SellerInterface
{
    use ParametersTrait;

    public static function rules(): array
    {
        return [];
    }

    public function __construct(
        array $parameters = []
    ) {
        $this->initialize($parameters);
    }
}
