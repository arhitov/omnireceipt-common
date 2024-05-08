<?php
/**
 * Core components for the Omnireceipt PHP fiscal receipt processing library
 *
 * @link      https://github.com/omnireceipt/common
 * @package   omnireceipt/common
 * @license   MIT
 * @copyright Copyright (c) 2024, Alexander Arhitov, clgsru@gmail.com
 */

namespace Omnireceipt\Common\Tests\Fixtures\Gateway\Dummy\Entities;

use Omnireceipt\Common\Entities\Seller as BaseSeller;

/**
 * @method string getUuid()
 * @method string|null getUuidOrNull()
 * @method self setUuid(string $value)
 */
class Seller extends BaseSeller
{
}
