<?php
/**
 * Core components for the Omnireceipt PHP fiscal receipt processing library
 *
 * @link      https://github.com/omnireceipt/common
 * @package   omnireceipt/common
 * @license   MIT
 * @copyright Copyright (c) 2024, Alexander Arhitov, clgsru@gmail.com
 */

namespace Omnireceipt\Common\Entities;

use Carbon\Carbon;
use Doctrine\Common\Collections\ArrayCollection;
use Omnireceipt\Common\Contracts\ReceiptInterface;
use Omnireceipt\Common\Contracts\ReceiptItemInterface;
use Omnireceipt\Common\Supports\ParametersTrait;

/**
 * @method string getId()
 * @method string getIdOrNull()
 * @method self setId(string $value)
 *
 * @method string getType()
 * @method string getTypeOrNull()
 * @method self setType(string $value)
 *
 * @method string getPaymentId()
 * @method string getPaymentIdOrNull()
 * @method self setPaymentId(string $value)
 *
 * @method string getInfo()
 * @method string getInfoOrNull()
 * @method self setInfo(string $value)
 *
 * @method Carbon getDate()
 * @method Carbon getDateOrNull()
 * @method self setDate(string|Carbon $value)
 */
abstract class Receipt implements ReceiptInterface
{
    use ParametersTrait {
        validate as validateParametersTrait;
        toArray as toArrayParametersTrait;
    }

    protected Seller $seller;
    protected ?Customer $customer = null;

    /** @var ArrayCollection<int, ReceiptItemInterface> */
    protected ArrayCollection $items;

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
        $this->items = new ArrayCollection;

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
        $this->items->add($item);
        return $this;
    }

    /**
     * @return ArrayCollection<int, ReceiptItem>
     */
    public function getItemList(): ArrayCollection
    {
        return $this->items;
    }

    public function validate(): bool
    {
        $this->validateParametersTrait();

        if ($this->items->isEmpty()) {
            $this->parametersError['items'] = ['Items must be'];
        } else {
            /** @var ReceiptItem $item */
            foreach ($this->items as $idx => $item) {
                if (! $item->validate()) {
                    $this->parametersError['items'] = ["Item idx:$idx did not fail validation"];
                    $this->parametersError['items_error'] ??= [];
                    $this->parametersError['items_error'][$idx] = $item->validateLastError();
                }
            }
        }

        if (is_null($this->getCustomer())) {
            $this->parametersError['customer'] = ['Customer must be'];
        }

        return empty($this->parametersError);
    }

    public function toArray(): array
    {
        $array = $this->toArrayParametersTrait();

        $array['seller'] = $this->getSeller()->toArray();
        $array['customer'] = $this->getCustomer()?->toArray();

        $array['items'] = [];
        foreach ($this->getItemList() as $item) {
            $array['items'][] = $item->toArray();
        }

        return $array;
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
