<?php

namespace Omnireceipt\Common\Tests\Fixtures\Gateway\Dummy\Http;

use Omnireceipt\Common\Http\Response\AbstractCreateReceiptResponse;

class CreateReceiptResponse extends AbstractCreateReceiptResponse
{
    public function isSuccessful(): bool
    {
        return $this->getCode() === 200;
    }
}
