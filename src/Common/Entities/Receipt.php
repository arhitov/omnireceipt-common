<?php

namespace Omnireceipt\Common\Entities;

use Omnireceipt\Common\Contracts\ReceiptInterface;
use Omnireceipt\Common\Contracts\ReceiptItemInterface;
use Omnireceipt\Common\Supports\PropertiesTrait;

/**
 * @method string getId()
 * @method self setId(string $value)
 * @method string getType()
 * @method self setType(string $value)
 * @method string getPaymentId()
 * @method self setPaymentId(string $value)
 * @method string getCustomerId()
 * @method self setCustomerId(string $value)
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
    use PropertiesTrait {
        validate as validatePropertiesTrait;
    }

    /** @var array<int, ReceiptItemInterface> */
    protected array $items;

    protected ?Customer $customer = null;

    const RULES = [
        'id'             => ['nullable', 'string'],
        'type'           => ['required', 'in:payment,refund'],
        'payment_id'     => ['nullable', 'string'],
        'customer_id'    => ['nullable', 'string'],
        'customer_name'  => ['required', 'string'],
        'customer_email' => ['nullable', 'string'],
        'customer_phone' => ['nullable', 'string'],
        'info'           => ['nullable', 'string'],
        'date'           => ['required', 'string'],
    ];

    public function __construct(
        array $properties = [],
    ) {
        $this->properties = $properties;
    }

    public function setCustomer(Customer $customer): self
    {
        $this->customer = $customer;
        $this->setCustomerId($customer->getId());
        $this->setCustomerName($customer->getName());
        $this->setCustomerEmail($customer->getEmail());
        $this->setCustomerPhone($customer->getPhone());
        return $this;
    }

    public function getCustomer(): ?Customer
    {
        return $this->customer;
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

    public function validate(): bool
    {
        $this->validatePropertiesTrait();

        if (empty($this->items)) {
            $this->propertiesError['items'] = ['Items must be'];
        } else {
            /** @var ReceiptItem $item */
            foreach ($this->items as $idx => $item) {
                if (! $item->validate()) {
                    $this->propertiesError['items'] = ["Item idx:{$idx} did not fail validation"];
                    $this->propertiesError['items_error'] ??= [];
                    $this->propertiesError['items_error'][$idx] = $item->getLastError();
                }
            }
        }

        return empty($this->propertiesError);
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
