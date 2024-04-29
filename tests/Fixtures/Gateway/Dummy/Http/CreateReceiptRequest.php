<?php

namespace Omnireceipt\Common\Tests\Fixtures\Gateway\Dummy\Http;

use Omnireceipt\Common\Http\Request\AbstractCreateReceiptRequest;
use Omnireceipt\Common\Http\Response\AbstractResponse;

class CreateReceiptRequest extends AbstractCreateReceiptRequest
{
    const RULES = [
    ];

    public function getData(): array
    {
        $receipt = $this->getReceipt();

        $goods = [];
        foreach ($receipt->getItemList() as $item) {
            $goods[] = [
                'good_name' => $item->getName(),
                'good_code' => $item->getCode(),
                'quantity' => $item->getQuantity(),
                'price' => $item->getAmount() / $item->getQuantity(),
                'dsum' => $item->getAmount(),
                'vat_rate' => $item->getVatRate(),
                'vat_sum' => $item->getVatSum(),
                'unit_uuid' => "bd72d926-55bc-11d9-848a-00112f43529a",
                'unit_name' => $item->getUnit(),
                'tag1214' => 4,
            ];
        }

        return [
            'uuid' => $receipt->getUuid(),
            'doc_date' => $receipt->getDate(),
            'doc_num' => $receipt->getDocNum(),
            'client_uuid' => $receipt->getCustomer()->getId(),
            'client_name' => $receipt->getCustomer()->getName(),
            'dsum' => $receipt->getAmount(),
            'debt' => $receipt->getAmount(),
            'info' => $receipt->getInfo(),
            'emailphone' => $receipt->getCustomer()->getEmail() ?? $receipt->getCustomer()->getPhone(),
            'pay_type' => '1',
            'firm_address' => $receipt->getSeller()->getAddressOrNull(),
            'goods' => $goods,
        ];
    }

    public function sendData(array $data): AbstractResponse
    {
        return new CreateReceiptResponse($this, null, 200);
    }
}
