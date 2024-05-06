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
use Omnireceipt\Common\Tests\Factories\CustomerFactory;
use Omnireceipt\Common\Tests\Factories\ReceiptFactory;
use Omnireceipt\Common\Tests\Factories\ReceiptItemFactory;
use Omnireceipt\Common\Tests\Factories\SellerFactory;
use Omnireceipt\Common\Tests\TestCase;
use PHPUnit\Framework\Attributes\Depends;

class ReceiptTest extends TestCase
{
    public function testBase()
    {
        $receipt = self::makeReceipt();

        $this->assertInstanceOf(ReceiptInterface::class, $receipt);
    }

    /**
     * @depends testBase
     * @return void
     */
    #[Depends('testBase')]
    public function testGetterAndSetter()
    {
        $receipt = self::makeReceipt();
        $type = 'Type';
        $paymentId = 'Payment id';
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
    #[Depends('testGetterAndSetter')]
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
    #[Depends('testGetterAndSetter')]
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
    #[Depends('testGetterAndSetter')]
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
     * @depends testValidatorItem
     * @return void
     */
    #[Depends('testValidatorItem')]
    public function testToArray()
    {
        $receipt = self::makeReceipt();
        $receipt->initialize(ReceiptFactory::definition());
        $receipt->setSeller(
            self::makeSeller(SellerFactory::definition()),
        );
        $receipt->addItem(
            self::makeReceiptItem(ReceiptItemFactory::definition())
        );

        $array = $receipt->toArray();
        $this->assertIsArray($array);
        $this->assertArrayHasKey('@seller', $array);
        $this->assertIsArray($array['@seller']);
        $this->assertEquals($receipt->getSeller()->getName(), $array['@seller']['name']);

        $this->assertArrayHasKey('@customer', $array);
        $this->assertNull($array['@customer']);

        $this->assertArrayHasKey('@itemList', $array);
        $this->assertIsArray($array['@itemList']);
        $this->assertCount(1, $array['@itemList']);
        $this->assertEquals($receipt->getItemList()->first()->getName(), $array['@itemList'][0]['name']);

        $receipt->setCustomer(
            self::makeCustomer(CustomerFactory::definition()),
        );

        $array = $receipt->toArray();
        $this->assertArrayHasKey('@customer', $array);
        $this->assertIsArray($array['@customer']);
        $this->assertEquals($receipt->getCustomer()->getName(), $array['@customer']['name']);
    }

    /**
     * @depends testGetterAndSetter
     * @return void
     */
    #[Depends('testGetterAndSetter')]
    public function testItems()
    {
        $receipt = self::makeReceipt(ReceiptFactory::definition());

        $receiptItem = self::makeReceiptItem(ReceiptItemFactory::definition());
        $receiptItem->setAmount(12.51);
        $receipt->addItem($receiptItem);

        $receiptItem = self::makeReceiptItem(ReceiptItemFactory::definition());
        $receiptItem->setAmount(20.82);
        $receipt->addItem($receiptItem);

        $this->assertEquals(2, $receipt->getItemList()->count());
        $this->assertEquals(33.33, $receipt->getAmount());
    }
}
