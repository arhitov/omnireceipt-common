<?php
/**
 * Dummy driver for Omnireceipt fiscal receipt processing library
 *
 * @link      https://github.com/omnireceipt/common
 * @package   omnireceipt/common
 * @license   MIT
 * @copyright Copyright (c) 2024, Alexander Arhitov, clgsru@gmail.com
 */

namespace Omnireceipt\Common\Tests\Fixtures\Gateway\Dummy\Entities;

use Omnireceipt\Common\Entities\ReceiptItem as BaseReceiptItem;

/**
 *
 * @method string getCode()
 * @method string|null getCodeOrNull()
 * @method self setCode(string $value)
 *
 * @method string getVatRate()
 * @method string|null getVatRateOrNull()
 * @method self setVatRate(string $value)
 *
 * @method string getVatSum()
 * @method string|null getVatSumOrNull()
 * @method self setVatSum(string $value)
 */
class ReceiptItem extends BaseReceiptItem
{
}
