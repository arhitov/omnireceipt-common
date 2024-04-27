<?php

namespace Omnireceipt\Common\Tests\Unit;

use Omnireceipt\Common\Contracts\ReceiptInterface;
use Omnireceipt\Common\Entities\Receipt;
use Omnireceipt\Common\Exceptions\Property\PropertyNotFoundException;
use Omnireceipt\Common\Supports\PropertiesTrait;
use Omnireceipt\Common\Tests\factories\ReceiptFactory;
use Omnireceipt\Common\Tests\factories\ReceiptItemFactory;
use Omnireceipt\Common\Tests\TestCase;

class ReceiptTest extends TestCase
{
    public function testBase()
    {
        $receiptItem = new Receipt;

        $this->assertInstanceOf(ReceiptInterface::class, $receiptItem);
        $this->assertContains(PropertiesTrait::class, class_uses($receiptItem));
    }

    /**
     * @depends testBase
     * @return void
     */
    #[\PHPUnit\Framework\Attributes\Depends('testBase')]
    public function testGetterAndSetter()
    {
        $receipt = new Receipt;
        $type = 'Type';
        $paymentId = 'Payment id';
        $customerName = 'Customer name';
        $customerEmail = 'Customer email';
        $customerPhone = 'Customer phone';
        $info = 'Info';
        $date = 'Date';
        $qweAsd = 'QweAsd';

        $receipt->setType($type);
        $receipt->setPaymentId($paymentId);
        $receipt->setCustomerName($customerName);
        $receipt->setCustomerEmail($customerEmail);
        $receipt->setCustomerPhone($customerPhone);
        $receipt->setInfo($info);
        $receipt->setDate($date);

        $receipt->setQweAsd($qweAsd);

        $this->assertEquals($type, $receipt->getType());
        $this->assertEquals($paymentId, $receipt->getPaymentId());
        $this->assertEquals($customerName, $receipt->getCustomerName());
        $this->assertEquals($customerEmail, $receipt->getCustomerEmail());
        $this->assertEquals($customerPhone, $receipt->getCustomerPhone());
        $this->assertEquals($info, $receipt->getInfo());
        $this->assertEquals($date, $receipt->getDate());
        $this->assertEquals($qweAsd, $receipt->getQweAsd());
    }

    /**
     * @depends testGetterAndSetter
     * @return void
     */
    #[\PHPUnit\Framework\Attributes\Depends('testGetterAndSetter')]
    public function testGetterException()
    {

        $receipt = new Receipt;

        $this->expectException(PropertyNotFoundException::class);
        $receipt->getType();
    }

    /**
     * @depends testGetterAndSetter
     * @return void
     */
    #[\PHPUnit\Framework\Attributes\Depends('testGetterAndSetter')]
    public function testValidator()
    {
        $receipt = ReceiptFactory::create();

        $this->assertInstanceOf(ReceiptInterface::class, $receipt);
        $this->assertTrue($receipt->validate());

        $receipt->setType(null);
        $this->assertFalse($receipt->validate());
    }

    /**
     * @depends testGetterAndSetter
     * @return void
     */
    #[\PHPUnit\Framework\Attributes\Depends('testGetterAndSetter')]
    public function testItems()
    {
        $receipt = ReceiptFactory::create();

        $receiptItem = ReceiptItemFactory::create();
        $receiptItem->setAmount(12.51);
        $receipt->addItem($receiptItem);

        $receiptItem = ReceiptItemFactory::create();
        $receiptItem->setAmount(20.82);
        $receipt->addItem($receiptItem);

        $this->assertCount(2, $receipt->getItemList());
        $this->assertEquals(33.33, $receipt->getAmount());
    }
}
