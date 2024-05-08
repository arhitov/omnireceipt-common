<?php
/**
 * Core components for the Omnireceipt PHP fiscal receipt processing library
 *
 * @link      https://github.com/omnireceipt/common
 * @package   omnireceipt/common
 * @license   MIT
 * @copyright Copyright (c) 2024, Alexander Arhitov, clgsru@gmail.com
 */

namespace Omnireceipt\Common\Tests\Fixtures\Gateway\Dummy\Http;

use Doctrine\Common\Collections\ArrayCollection;
use Omnireceipt\Common\Http\Response\AbstractListReceiptsResponse;

class ListReceiptsResponse extends AbstractListReceiptsResponse
{
    public function getList(): ArrayCollection
    {
        return new ArrayCollection(
            $this->isSuccessful() && is_array($this->getData())
                ? $this->getData()
                : []
        );
    }
}
