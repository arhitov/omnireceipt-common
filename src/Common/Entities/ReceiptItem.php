<?php

namespace Omnireceipt\Common\Entities;

use Omnireceipt\Common\Contracts\ReceiptItemInterface;
use Omnireceipt\Common\Supports\PropertiesTrait;

/**
 * @method string getName()
 * @method self setName(string $value)
 * @method int|float getAmount()
 * @method self setAmount(int|float $value)
 * @method string getCurrency()
 * @method self setCurrency(string $value)
 * @method int|float getQuantity()
 * @method self setQuantity(int|float $value)
 * @method string getUnit()
 * @method self setUnit(string $value)
 */
class ReceiptItem implements ReceiptItemInterface
{
    use PropertiesTrait;

    const RULES = [
        'name' => ['required', 'string'],
        'amount' => ['required', 'numeric'],
        'currency' => ['required', 'string'],
        'quantity' => ['required', 'numeric'],
        'unit' => ['required', 'string'],
    ];

    public function __construct(
        array $properties = []
    ) {
        $this->properties = $properties;
    }
}
