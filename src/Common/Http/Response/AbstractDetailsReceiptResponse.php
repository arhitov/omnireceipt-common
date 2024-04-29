<?php

namespace Omnireceipt\Common\Http\Response;

use Carbon\Carbon;

abstract class AbstractDetailsReceiptResponse extends AbstractResponse
{
    abstract public function isPending(): bool;

    abstract public function isSuccessful(): bool;

    abstract public function isCancelled(): bool;

    abstract public function getState(): string|null;

    abstract public function getDate(): Carbon|null;
}
