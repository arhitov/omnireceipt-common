<?php

namespace Omnireceipt\Common\Tests\factories;

use Omnireceipt\Common\Entities\ReceiptItem;

/**
 * @method static ReceiptItem create()
 */
class ReceiptItemFactory extends Factory
{
    const SOURCE = ReceiptItem::class;
    const SOURCE_TYPE = 'BUILDER';

    protected static function definition(): array
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
