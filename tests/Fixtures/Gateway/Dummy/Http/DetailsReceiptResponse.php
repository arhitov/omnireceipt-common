<?php
/**
 * Dummy driver for Omnireceipt fiscal receipt processing library
 *
 * @link      https://github.com/omnireceipt/common
 * @package   omnireceipt/common
 * @license   MIT
 * @copyright Copyright (c) 2024, Alexander Arhitov, clgsru@gmail.com
 */

namespace Omnireceipt\Common\Tests\Fixtures\Gateway\Dummy\Http;

use Omnireceipt\Common\Http\Response\AbstractDetailsReceiptResponse;
use Omnireceipt\Common\Tests\Fixtures\Gateway\Dummy\Entities\Receipt;
use Omnireceipt\Common\Tests\Fixtures\Gateway\Dummy\Entities\ReceiptItem;

class DetailsReceiptResponse extends AbstractDetailsReceiptResponse
{
    public function getReceipt(): ?Receipt
    {
        /** @var array $receiptArray */
        $receiptArray = $this->getData();
        $goods = $receiptArray['goods'] ?? [];
        unset($receiptArray['goods']);
        $receipt = new Receipt($receiptArray);
        foreach ($goods as $good) {
            $receipt->addItem(
                new ReceiptItem($good)
            );
        }
        return $receipt;
    }
}
