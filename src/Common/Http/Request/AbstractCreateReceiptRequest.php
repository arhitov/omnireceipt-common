<?php

namespace Omnireceipt\Common\Http\Request;

use Omnireceipt\Common\Entities\Receipt;
use Omnireceipt\Common\Entities\Seller;

abstract class AbstractCreateReceiptRequest extends AbstractRequest
{
    protected Receipt $receipt;

    protected Seller $seller;

    public function setReceipt(Receipt $receipt): self
    {
        $this->receipt = $receipt;
        return $this;
    }

    public function getReceipt(): Receipt
    {
        return $this->receipt;
    }

    public function setSeller(Seller $seller): self
    {
        $this->seller = $seller;
        return $this;
    }

    public function getSeller(): Seller
    {
        return $this->seller;
    }
}
