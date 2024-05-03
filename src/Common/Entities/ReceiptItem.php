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

use Omnireceipt\Common\Contracts\ReceiptItemInterface;
use Omnireceipt\Common\Supports\ParametersTrait;

/**
 * @method string getName()
 * @method string getNameOrNull()
 * @method self setName(string $value)
 *
 * @method int|float getAmount()
 * @method int|float getAmountOrNull()
 * @method self setAmount(int|float $value)
 *
 * @method string getCurrency()
 * @method string getCurrencyOrNull()
 * @method self setCurrency(string $value)
 *
 * @method int|float getQuantity()
 * @method int|float getQuantityOrNull()
 * @method self setQuantity(int|float $value)
 *
 * @method string getUnit()
 * @method string getUnitOrNull()
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
