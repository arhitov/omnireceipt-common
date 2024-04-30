<?php

namespace Omnireceipt\Common\Tests\Fixtures\Gateway\Dummy\Http;

use Doctrine\Common\Collections\ArrayCollection;
use Omnireceipt\Common\Http\Response\AbstractListReceiptsResponse;

class ListReceiptsResponse extends AbstractListReceiptsResponse
{
    public function isSuccessful(): bool
    {
        return $this->getCode() === 200;
    }

    public function getList(): ArrayCollection
    {
        return new ArrayCollection(
            $this->getCode() === 200 && is_array($this->getData())
                ? $this->getData()
                : []
        );
    }
}
