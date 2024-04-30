<?php

namespace Omnireceipt\Common\Tests\Fixtures\Gateway\Dummy\Entities;

use Omnireceipt\Common\Entities\Receipt as BaseReceipt;

class Receipt extends BaseReceipt
{
    public function isPending(): bool
    {
        return false;
    }

    public function isSuccessful(): bool
    {
        return false;
    }

    public function isCancelled(): bool
    {
        return false;
    }
}
