<?php

namespace Omnireceipt\Common\Entities;

use Omnireceipt\Common\Contracts\SellerInterface;
use Omnireceipt\Common\Supports\PropertiesTrait;

class Seller implements SellerInterface
{
    use PropertiesTrait;

    const RULES = [];

    public function __construct(
        array $properties = []
    ) {
        $this->properties = $properties;
    }
}
