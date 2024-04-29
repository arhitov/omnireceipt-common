<?php

namespace Omnireceipt\Common\Http\Response;

abstract class AbstractCreateReceiptResponse extends AbstractResponse
{
    abstract public function isSuccessful(): bool;
}
