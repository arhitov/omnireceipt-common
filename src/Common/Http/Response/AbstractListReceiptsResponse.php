<?php

namespace Omnireceipt\Common\Http\Response;

use Doctrine\Common\Collections\ArrayCollection;

abstract class AbstractListReceiptsResponse extends AbstractResponse
{
    abstract public function getList(): ArrayCollection;
}
