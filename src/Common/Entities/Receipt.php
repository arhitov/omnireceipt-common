<?php

namespace Omnireceipt\Common\Entities;

use Omnireceipt\Common\Contracts\ReceiptInterface;
use Omnireceipt\Common\Contracts\ReceiptItemInterface;
use Omnireceipt\Common\Supports\ParametersTrait;

/**
 * @method string getId()
 * @method self setId(string $value)
 *
 * @method string getType()
 * @method self setType(string $value)
 *
 * @method string getPaymentId()
 * @method self setPaymentId(string $value)
 *
 * @method string getInfo()
 * @method self setInfo(string $value)
 *
 * @method string getDate()
 * @method self setDate(string $value)
 */
abstract class Receipt implements ReceiptInterface
{
    use ParametersTrait {
        validate as validateParametersTrait;
    }

    protected Seller $seller;
    protected ?Customer $customer = null;

    /** @var array<int, ReceiptItemInterface> */
    protected array $items;

    public static function rules(): array
    {
        return [
            'id'             => ['nullable', 'string'],
            'type'           => ['required', 'in:payment,refund'],
            'payment_id'     => ['nullable', 'string'],
            'info'           => ['nullable', 'string'],
            'date'           => ['required', 'string'],
        ];
    }

    public function __construct(
        array $parameters = [],
    ) {
        $this->initialize($parameters);
    }

    public function getSeller(): Seller
    {
        return $this->seller;
    }

    public function setSeller(Seller $seller): self
    {
        $this->seller = $seller;
        return $this;
    }

    public function setCustomer(Customer $customer): self
    {
        $this->customer = $customer;
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
        $this->validateParametersTrait();

        if (empty($this->items)) {
            $this->parametersError['items'] = ['Items must be'];
        } else {
            /** @var ReceiptItem $item */
            foreach ($this->items as $idx => $item) {
                if (! $item->validate()) {
                    $this->parametersError['items'] = ["Item idx:{$idx} did not fail validation"];
                    $this->parametersError['items_error'] ??= [];
                    $this->parametersError['items_error'][$idx] = $item->validateLastError();
                }
            }
        }

        return empty($this->parametersError);
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
