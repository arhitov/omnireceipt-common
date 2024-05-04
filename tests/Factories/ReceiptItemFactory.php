<?php
/**
 * Core components for the Omnireceipt PHP fiscal receipt processing library
 *
 * @link      https://github.com/omnireceipt/common
 * @package   omnireceipt/common
 * @license   MIT
 * @copyright Copyright (c) 2024, Alexander Arhitov, clgsru@gmail.com
 */

namespace Omnireceipt\Common\Tests\Factories;

use Omnireceipt\Common\Entities\ReceiptItem;

/**
 * @method static ReceiptItem create(string $className)
 */
class ReceiptItemFactory extends Factory
{
    const SOURCE_TYPE = 'BUILDER';

    public static function definition(): array
    {
        return [
            'asd_sdf' => 123,
            'name' => 'FLAG, W/ 2 HOLDERS, NO. 22',
            'code' => '6446963/104515',
            'type' => 'product',
            'amount' => 2.12,
            'currency' => 'USD',
            'quantity' => 2,
            'unit' => 'pc',
            'tax' => 0,
        ];
    }
}
