<?php

namespace Omnireceipt\Common\Tests\Fixtures\Gateway\Dummy\Http;

use Carbon\Carbon;
use Omnireceipt\Common\Http\Response\AbstractDetailsReceiptResponse;

class DetailsReceiptResponse extends AbstractDetailsReceiptResponse
{
    public function isPending(): bool
    {
        return $this->getState() === 'pending';
    }

    public function isSuccessful(): bool
    {
        return $this->getState() === 'succeeded';
    }

    public function isCancelled(): bool
    {
        return $this->getState() === 'canceled';
    }

    public function getState(): string|null
    {
        /** @var array|null $data */
        $data = $this->getData();
        return $data['status'] ?? null;
    }

    public function getDate(): Carbon|null
    {
        /** @var array|null $data */
        $data = $this->getData();
        $registered_at = $data['registered_at'] ?? null;
        return $registered_at ? Carbon::parse($registered_at) : null;
    }
}
