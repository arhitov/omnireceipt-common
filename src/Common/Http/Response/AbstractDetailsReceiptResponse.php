<?php
/**
 * Core components for the Omnireceipt PHP fiscal receipt processing library
 *
 * @link      https://github.com/omnireceipt/common
 * @package   omnireceipt/common
 * @license   MIT
 * @copyright Copyright (c) 2024, Alexander Arhitov, clgsru@gmail.com
 */

namespace Omnireceipt\Common\Http\Response;

use Omnireceipt\Common\Entities\Receipt;

abstract class AbstractDetailsReceiptResponse extends AbstractResponse
{
    abstract public function getReceipt(): ?Receipt;
}
