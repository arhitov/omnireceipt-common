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

use Omnireceipt\Common\Http\Request\AbstractCreateReceiptRequest;
use Omnireceipt\Common\Http\Response\AbstractResponse;

class CreateReceiptRequest extends AbstractCreateReceiptRequest
{
    static public function rules(): array
    {
        return [];
    }

    public function getData(): array
    {
        /** @var \Omnireceipt\Common\Tests\Fixtures\Gateway\Dummy\Entities\Receipt $receipt */
        $receipt = $this->getReceipt();

        $goods = [];
        /** @var \Omnireceipt\Common\Tests\Fixtures\Gateway\Dummy\Entities\ReceiptItem $item */
        foreach ($receipt->getItemList() as $item) {
            $goods[] = [
                'name' => $item->getName(),
                'quantity' => $item->getQuantity(),
                'price' => $item->getAmount() / $item->getQuantity(),
                'amount' => $item->getAmount(),
            ];
        }

        return [
            'uuid' => $receipt->getUuid(),
            'date' => $receipt->getDate(),
            'client_uuid' => $receipt->getCustomer()->getId(),
            'client_name' => $receipt->getCustomer()->getName(),
            'email' => $receipt->getCustomer()->getEmail(),
            'goods' => $goods,
        ];
    }

    public function sendData(array $data): AbstractResponse
    {
        return new CreateReceiptResponse($this, null, 200);
    }
}
