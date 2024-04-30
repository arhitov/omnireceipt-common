<?php

namespace Omnireceipt\Common\Entities;

use Omnireceipt\Common\Contracts\ReceiptItemInterface;
use Omnireceipt\Common\Supports\ParametersTrait;

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
abstract class ReceiptItem implements ReceiptItemInterface
{
    use ParametersTrait;

    public static function rules(): array
    {
        return [
            'name'     => ['required', 'string'],
            'amount'   => ['required', 'numeric'],
            'currency' => ['required', 'string'],
            'quantity' => ['required', 'numeric'],
            'unit'     => ['required', 'string'],
        ];
    }

    public function __construct(
        array $parameters = []
    ) {
        $this->initialize($parameters);
    }
}
