<?php

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
