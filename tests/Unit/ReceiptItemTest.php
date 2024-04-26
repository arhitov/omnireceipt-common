<?php

namespace Omnireceipt\Common\Tests\Unit;

use Omnireceipt\Common\Contracts\ReceiptItemInterface;
use Omnireceipt\Common\Entities\ReceiptItem;
use Omnireceipt\Common\Exceptions\Property\PropertyNotFoundException;
use Omnireceipt\Common\Supports\PropertiesTrait;
use Omnireceipt\Common\Tests\factories\ReceiptItemFactory;
use Omnireceipt\Common\Tests\TestCase;

class ReceiptItemTest extends TestCase
{
    public function testBase()
    {
        $receiptItem = new ReceiptItem;

        $this->assertInstanceOf(ReceiptItemInterface::class, $receiptItem);
        $this->assertContains(PropertiesTrait::class, class_uses($receiptItem));
    }

    /**
     * @depends testBase
     * @return void
     */
    public function testGetterAndSetter()
    {
        $receiptItem = new ReceiptItem;
        $name = 'Name';
        $amount = 'Amount';
        $currency = 'Currency';
        $quantity = 'Quantity';
        $unit = 'Unit';
        $qweAsd = 'QweAsd';

        $receiptItem->setName($name);
        $receiptItem->setAmount($amount);
        $receiptItem->setCurrency($currency);
        $receiptItem->setQuantity($quantity);
        $receiptItem->setUnit($unit);

        $receiptItem->setQweAsd($qweAsd);

        $this->assertEquals($name, $receiptItem->getName());
        $this->assertEquals($amount, $receiptItem->getAmount());
        $this->assertEquals($currency, $receiptItem->getCurrency());
        $this->assertEquals($quantity, $receiptItem->getQuantity());
        $this->assertEquals($unit, $receiptItem->getUnit());
        $this->assertEquals($qweAsd, $receiptItem->getQweAsd());
    }

    /**
     * @depends testGetterAndSetter
     * @return void
     */
    public function testGetterException()
    {

        $receiptItem = new ReceiptItem;

        $this->expectException(PropertyNotFoundException::class);
        $receiptItem->getName();
    }

    /**
     * @depends testGetterAndSetter
     * @return void
     */
    public function testValidator()
    {
        $receiptItem = ReceiptItemFactory::create();

        $this->assertInstanceOf(ReceiptItemInterface::class, $receiptItem);
        $this->assertTrue($receiptItem->validate());

        $receiptItem->setName(null);
        $this->assertFalse($receiptItem->validate());
    }
}
