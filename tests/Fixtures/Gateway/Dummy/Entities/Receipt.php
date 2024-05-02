<?php

namespace Omnireceipt\Common\Tests\Fixtures\Gateway\Dummy\Entities;

use Carbon\Carbon;
use Omnireceipt\Common\Entities\Receipt as BaseReceipt;

/**
 * @method string getState()
 * @method self setState(string $value)
 */
class Receipt extends BaseReceipt
{
    public function isPending(): bool
    {
        return 'pending' === $this->getState();
    }

    public function isSuccessful(): bool
    {
        return 'succeeded' === $this->getState();
    }

    public function isCancelled(): bool
    {
        return 'canceled' === $this->getState();
    }

    public function getDate(): Carbon
    {
        return Carbon::parse($this->getParameter('date'));
    }
}
