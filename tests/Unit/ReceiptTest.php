<?php
/**
 * Core components for the Omnireceipt PHP fiscal receipt processing library
 *
 * @link      https://github.com/omnireceipt/common
 * @package   omnireceipt/common
 * @license   MIT
 * @copyright Copyright (c) 2024, Alexander Arhitov, clgsru@gmail.com
 */

namespace Omnireceipt\Common\Tests\Unit;

use Omnireceipt\Common\Contracts\ReceiptInterface;
use Omnireceipt\Common\Exceptions\Parameters\ParameterNotFoundException;
use Omnireceipt\Common\Tests\factories\ReceiptFactory;
use Omnireceipt\Common\Tests\factories\ReceiptItemFactory;
use Omnireceipt\Common\Tests\TestCase;

class ReceiptTest extends TestCase
{
    public function testBase()
    {
        $receiptItem = self::makeReceipt();

        $this->assertInstanceOf(ReceiptInterface::class, $receiptItem);
    }

    /**
     * @depends testBase
     * @return void
     */
    #[\PHPUnit\Framework\Attributes\Depends('testBase')]
    public function testGetterAndSetter()
    {
        $receipt = self::makeReceipt();
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
        $receipt->setInfo($info);
        $receipt->setDate($date);

        $receipt->setQweAsd($qweAsd);

        $this->assertEquals($type, $receipt->getType());
        $this->assertEquals($paymentId, $receipt->getPaymentId());
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

        $receipt = self::makeReceipt();

        $this->expectException(ParameterNotFoundException::class);
        $receipt->getType();
    }

    /**
     * @depends testGetterAndSetter
     * @return void
     */
    #[\PHPUnit\Framework\Attributes\Depends('testGetterAndSetter')]
    public function testValidator()
    {
        $receipt = self::makeReceipt();
        $receipt->initialize(ReceiptFactory::definition());

        $this->assertInstanceOf(ReceiptInterface::class, $receipt);
        $this->assertFalse($receipt->validate());

        $receipt->addItem(self::makeReceiptItem(ReceiptItemFactory::definition()));
        $this->assertFalse($receipt->validate());

        $receipt->setCustomer(
            $this->makeCustomer()
        );
        $this->assertTrue($receipt->validate());

        $receipt->setType(null);
        $this->assertFalse($receipt->validate());
    }

    /**
     * @depends testGetterAndSetter
     * @return void
     */
    #[\PHPUnit\Framework\Attributes\Depends('testGetterAndSetter')]
    public function testValidatorItem()
    {
        $receipt = self::makeReceipt();
        $receipt->initialize(ReceiptFactory::definition());

        $receipt->addItem(self::makeReceiptItem());

        $receipt->setCustomer(
            $this->makeCustomer()
        );

        $this->assertFalse($receipt->validate());

        $validateLastError = $receipt->validateLastError()['parameters'];
        $this->assertArrayHasKey('items_error', $validateLastError);
        $parameters = $validateLastError['items_error'][0]['parameters'];
        $this->assertArrayHasKey('name', $parameters);
        $this->assertArrayHasKey('amount', $parameters);
        $this->assertArrayHasKey('currency', $parameters);
        $this->assertArrayHasKey('quantity', $parameters);
        $this->assertArrayHasKey('unit', $parameters);
    }

    /**
     * @depends testGetterAndSetter
     * @return void
     */
    #[\PHPUnit\Framework\Attributes\Depends('testGetterAndSetter')]
    public function testItems()
    {
        $receipt = self::makeReceipt(ReceiptFactory::definition());

        $receiptItem = self::makeReceiptItem(ReceiptItemFactory::definition());
        $receiptItem->setAmount(12.51);
        $receipt->addItem($receiptItem);

        $receiptItem = self::makeReceiptItem(ReceiptItemFactory::definition());
        $receiptItem->setAmount(20.82);
        $receipt->addItem($receiptItem);

        $this->assertCount(2, $receipt->getItemList());
        $this->assertEquals(33.33, $receipt->getAmount());
    }
}
