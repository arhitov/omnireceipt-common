<?php

namespace Omnireceipt\Common\Entities;

use Omnireceipt\Common\Contracts\ReceiptInterface;
use Omnireceipt\Common\Contracts\ReceiptItemInterface;
use Omnireceipt\Common\Supports\PropertiesTrait;

/**
 * @method string getType()
 * @method self setType(string $value)
 * @method string getPaymentId()
 * @method self setPaymentId(string $value)
 * @method string getCustomerName()
 * @method self setCustomerName(string $value)
 * @method string getCustomerEmail()
 * @method self setCustomerEmail(string $value)
 * @method string getCustomerPhone()
 * @method self setCustomerPhone(string $value)
 * @method string getInfo()
 * @method self setInfo(string $value)
 * @method string getDate()
 * @method self setDate(string $value)
 */
class Receipt implements ReceiptInterface
{
    use PropertiesTrait;

    /** @var array<int, ReceiptItemInterface> */
    protected array $items;

    const RULES = [
        'type' => ['required', 'in:payment,refund'],
        'payment_id' => ['required', 'string'], // doc_num
        'customer_name' => ['required', 'string'], // client_name
        'customer_email' => ['nullable', 'string'],
        'customer_phone' => ['nullable', 'string'],
        'info' => ['nullable', 'string'],
        'date' => ['required', 'string'],
    ];

    public function __construct(
        array $properties = []
    ) {
        $this->properties = $properties;
    }

    /**
     * @param ReceiptItem $item
     * @return self
     */
    public function addItem(ReceiptItem $item): self
    {
        $this->items[] = $item;
        return $this;
    }

    /**
     * @return array<int, ReceiptItem>
     */
    public function getItemList(): array
    {
        return $this->items;
    }

    /**
     * Calculates the sum of the cost of all items
     *
     * @return int|float
     */
    public function getAmount(): int|float
    {
        $amount = 0;

        foreach ($this->getItemList() as $item) {
            $amount += $item->getAmount();
        }

        return $amount;
    }
}
