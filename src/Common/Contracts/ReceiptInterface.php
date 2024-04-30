<?php

namespace Omnireceipt\Common\Contracts;

interface ReceiptInterface
{
    public function isPending(): bool;
    public function isSuccessful(): bool;
    public function isCancelled(): bool;
}
