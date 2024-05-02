<?php

namespace Omnireceipt\Common\Http\Response;

use Carbon\Carbon;
use Omnireceipt\Common\Entities\Receipt;

abstract class AbstractDetailsReceiptResponse extends AbstractResponse
{
    abstract public function getReceipt(): ?Receipt;
}
