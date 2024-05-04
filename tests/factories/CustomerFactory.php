<?php

namespace Omnireceipt\Common\Tests\factories;

use Omnireceipt\Common\Entities\Customer;

/**
 * @method static Customer create(string $className)
 */
class CustomerFactory extends Factory
{
    const SOURCE_TYPE = 'BUILDER';

    public static function definition(): array
    {
        return [
            'id'    => '4a65ecb6-8b1b-11df-be16-e0cb4ed5f70f',
            'name'  => 'Ivanov Ivan Ivanovich',
            'phone' => '+79000000000',
            'email' => 'email@email.ru',
        ];
    }
}
