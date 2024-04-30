<?php

namespace Omnireceipt\Common\Entities;

use Omnireceipt\Common\Contracts\SellerInterface;
use Omnireceipt\Common\Supports\ParametersTrait;

class Seller implements SellerInterface
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
