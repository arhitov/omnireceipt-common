<?php
/**
 * Core components for the Omnireceipt PHP fiscal receipt processing library
 *
 * @link      https://github.com/omnireceipt/common
 * @package   omnireceipt/common
 * @license   MIT
 * @copyright Copyright (c) 2024, Alexander Arhitov, clgsru@gmail.com
 */

namespace Omnireceipt\Common\Tests\factories;

use Omnireceipt\Common\Entities\Receipt;

/**
 * @method static Receipt create(string $className)
 */
class ReceiptFactory extends Factory
{
    const SOURCE_TYPE = 'BUILDER';

    public static function definition(): array
    {
        return [
            'asd_sdf' => 123,
            'type' => 'payment',
            'payment_id' => '24b94598-000f-5000-9000-1b68e7b15f3f',
            'info' => 'Lego Bricks',
            'date' => '2016-08-25 13:48:01',
        ];
    }
}