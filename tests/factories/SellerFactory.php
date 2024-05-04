<?php

namespace Omnireceipt\Common\Tests\factories;

use Omnireceipt\Common\Entities\Seller;

/**
 * @method static Seller create(string $className)
 */
class SellerFactory extends Factory
{
    const SOURCE_TYPE = 'BUILDER';

    public static function definition(): array
    {
        return [
            'name' => 'LLC "HORNS AND HOOVES"',
        ];
    }
}
