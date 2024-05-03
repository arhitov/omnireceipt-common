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

use Omnireceipt\Common\Contracts\ReceiptItemInterface;
use Omnireceipt\Common\Exceptions\Parameters\ParameterNotFoundException;
use Omnireceipt\Common\Tests\factories\ReceiptItemFactory;
use Omnireceipt\Common\Tests\TestCase;

class ReceiptItemTest extends TestCase
{
    public function testBase()
    {
        $receiptItem = self::makeReceiptItem();

        $this->assertInstanceOf(ReceiptItemInterface::class, $receiptItem);
    }

    /**
     * @depends testBase
     * @return void
     */
    #[\PHPUnit\Framework\Attributes\Depends('testBase')]
    public function testGetterAndSetter()
    {
        $receiptItem = self::makeReceiptItem();
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
    #[\PHPUnit\Framework\Attributes\Depends('testGetterAndSetter')]
    public function testGetterException()
    {
        $receiptItem = self::makeReceiptItem();

        $this->expectException(ParameterNotFoundException::class);
        $receiptItem->getName();
    }

    /**
     * @depends testGetterAndSetter
     * @return void
     */
    #[\PHPUnit\Framework\Attributes\Depends('testGetterAndSetter')]
    public function testValidator()
    {
        $receiptItem = self::makeReceiptItem(ReceiptItemFactory::definition());

        $this->assertInstanceOf(ReceiptItemInterface::class, $receiptItem);
        $this->assertTrue($receiptItem->validate());

        $receiptItem->setName(null);
        $this->assertFalse($receiptItem->validate());
    }
}
