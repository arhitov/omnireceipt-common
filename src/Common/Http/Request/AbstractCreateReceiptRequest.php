<?php
/**
 * Core components for the Omnireceipt PHP fiscal receipt processing library
 *
 * @link      https://github.com/omnireceipt/common
 * @package   omnireceipt/common
 * @license   MIT
 * @copyright Copyright (c) 2024, Alexander Arhitov, clgsru@gmail.com
 */

namespace Omnireceipt\Common\Http\Request;

use Omnireceipt\Common\Entities\Receipt;

abstract class AbstractCreateReceiptRequest extends AbstractRequest
{
    protected Receipt $receipt;

    public function getReceipt(): Receipt
    {
        return $this->receipt;
    }

    public function setReceipt(Receipt $receipt): self
    {
        $this->receipt = $receipt;
        return $this;
    }
}
