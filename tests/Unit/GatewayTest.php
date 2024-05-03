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

use Carbon\Carbon;
use Doctrine\Common\Collections\ArrayCollection;
use Omnireceipt\Common\AbstractGateway;
use Omnireceipt\Common\Entities\Customer;
use Omnireceipt\Common\Entities\Receipt;
use Omnireceipt\Common\Entities\Seller;
use Omnireceipt\Common\Exceptions\Parameters\ParameterValidateException;
use Omnireceipt\Common\Exceptions\RuntimeException;
use Omnireceipt\Common\Supports\Helper;
use Omnireceipt\Common\Tests\factories\ReceiptFactory;
use Omnireceipt\Common\Tests\factories\ReceiptItemFactory;
use Omnireceipt\Common\Tests\Fixtures\Gateway\Dummy\Gateway;
use Omnireceipt\Common\Tests\TestCase;
use Omnireceipt\Omnireceipt;
use PHPUnit\Framework\Attributes\Depends;

class GatewayTest extends TestCase
{
    public function testBase()
    {
        $omnireceipt = self::createOmnireceipt(false);

        $this->assertInstanceOf(AbstractGateway::class, $omnireceipt);

        $this->assertEquals('Dummy', $omnireceipt->getName());
        $this->assertEquals('Dummy', $omnireceipt->getShortName());
        $this->assertIsArray($omnireceipt->getDefaultParameters());
        $this->assertEmpty($omnireceipt->getDefaultParameters());
        $this->assertIsArray($omnireceipt->getParameters());
        $this->assertEmpty($omnireceipt->getParameters());
        $this->assertFalse($omnireceipt->validate());

        $omnireceipt->initialize(['auth' => 'ok']);
        $this->assertNotEmpty($omnireceipt->getParameters());
        $this->assertTrue($omnireceipt->validate());

        // Customer
        $customerName = 'Ivanov Ivan';
        $customer = $omnireceipt->customerFactory(['name' => $customerName]);
        $this->assertInstanceOf(Customer::class, $customer);
        $this->assertEquals($customerName, $customer->getName());
        $this->assertTrue($customer->validate());

        // Seller
        $seller = $omnireceipt->sellerFactory();
        $this->assertInstanceOf(Seller::class, $seller);
        $this->assertTrue($seller->validate());

        // Receipt
        $receipt = $omnireceipt->receiptFactory(
            [
                'type'          => 'payment',
                'date'          => '2024-04-29T18:27:34.000+03:00',
            ],
            [
                'name'          => 'FLAG, W/ 2 HOLDERS, NO. 22',
                'amount'        => 2.12,
                'currency'      => 'USD',
                'quantity'      => 2,
                'unit'          => 'pc',
            ],
            [
                'name'          => 'MINI WIG, NO. 288',
                'amount'        => 1.54,
                'currency'      => 'USD',
                'quantity'      => 2,
                'unit'          => 'pc',
            ],
        );
        $this->assertInstanceOf(Receipt::class, $receipt);
        $this->assertFalse($receipt->validate());
        $this->assertArrayHasKey('customer', $receipt->validateLastError()['parameters'] ?? []);

        $receipt->setCustomer(
            $omnireceipt->customerFactory()
        );
        $this->assertTrue($receipt->validate());
        $this->assertEquals(3.66, $receipt->getAmount());
        $this->assertCount(2, $receipt->getItemList());
    }

    /**
     * @depends testBase
     * @return void
     */
    #[Depends('testBase')]
    public function testBaseException()
    {
        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('Class "\Omnireceipt\Qwe\Gateway" not found');

        Omnireceipt::create('Qwe');
    }

    /**
     * @depends testBase
     * @return void
     */
    #[Depends('testBase')]
    public function testInitialize()
    {
        $omnireceipt = (self::createOmnireceipt())
                       ->initialize(['auth' => 'ok']);

        $initializeData = self::initializeData();

        $this->assertNotEmpty($initializeData);

        $omnireceipt->initialize($initializeData);

        foreach ($initializeData as $key => $value) {
            $method = Helper::getGetterMethodName($key);
            $this->assertEquals($value, $omnireceipt->$method());
        }
    }

    /**
     * @depends testInitialize
     * @return void
     * @throws \Omnireceipt\Common\Exceptions\Parameters\ParameterValidateException
     */
    #[Depends('testInitialize')]
    public function testCreateReceipt()
    {
        $omnireceipt = (self::createOmnireceipt())
                       ->initialize(['auth' => 'ok']);

        /** @var \Omnireceipt\Common\Tests\Fixtures\Gateway\Dummy\Entities\Receipt $receipt */
        $receipt = ReceiptFactory::create($omnireceipt::classNameReceipt());
        $receipt->setUuid('0ecab77f-7062-4a5f-aa20-35213db1397c');
        $receipt->setDocNum('ТД00-000001');

        $classNameCustomer = $omnireceipt::classNameCustomer();
        $customer = new $classNameCustomer([
            'id'    => '4a65ecb6-8b1b-11df-be16-e0cb4ed5f70f',
            'name'  => 'Ivanov Ivan Ivanovich',
            'phone' => '+79000000000',
            'email' => 'email@email.ru',
        ]);
        $receipt->setCustomer($customer);

        /** @var \Omnireceipt\Common\Tests\Fixtures\Gateway\Dummy\Entities\ReceiptItem $receiptItem */
        $receiptItem = ReceiptItemFactory::create($omnireceipt::classNameReceiptItem());
        $receiptItem->setVatRate(0);
        $receiptItem->setVatSum(0);
        $receipt->addItem($receiptItem);

        $this->assertTrue($receipt->validate());

        $classNameSeller = $omnireceipt::classNameSeller();
        $seller = new $classNameSeller([
            'address' => 'www.example.com',
        ]);

        $response = $omnireceipt->createReceipt(
            $receipt,
            [
                'qwe' => 'qwe',
            ],
            seller: $seller,
        );

        $this->assertTrue($response->isSuccessful());
        $this->assertNull($response->getData());
        $this->assertEquals(200, $response->getCode());
    }

    /**
     * @depends testInitialize
     * @return void
     * @throws \Omnireceipt\Common\Exceptions\Parameters\ParameterValidateException
     */
    #[Depends('testInitialize')]
    public function testCreateReceiptTwo()
    {
        $omnireceipt = (self::createOmnireceipt())
                       ->initialize(['auth' => 'ok']);

        /** @var \Omnireceipt\Common\Tests\Fixtures\Gateway\Dummy\Entities\Receipt $receipt */
        $receipt = ReceiptFactory::create($omnireceipt::classNameReceipt());
        $receipt->setUuid('0ecab77f-7062-4a5f-aa20-35213db1397c');
        $receipt->setDocNum('ТД00-000001');

        $customer = $omnireceipt->customerFactory([
            'id'    => '4a65ecb6-8b1b-11df-be16-e0cb4ed5f70f',
            'name'  => 'Ivanov Ivan Ivanovich',
            'phone' => '+79000000000',
            'email' => 'email@email.ru',
        ]);
        $receipt->setCustomer($customer);

        /** @var \Omnireceipt\Common\Tests\Fixtures\Gateway\Dummy\Entities\ReceiptItem $receiptItem */
        $receiptItem = ReceiptItemFactory::create($omnireceipt::classNameReceiptItem());
        $receiptItem->setVatRate(0);
        $receiptItem->setVatSum(0);
        $receipt->addItem($receiptItem);

        $this->assertTrue($receipt->validate());

        $response = $omnireceipt->createReceipt(
            $receipt,
        );

        $this->assertTrue($response->isSuccessful());
        $this->assertNull($response->getData());
        $this->assertEquals(200, $response->getCode());
    }

    /**
     * @depends testInitialize
     * @return void
     * @throws \Omnireceipt\Common\Exceptions\Parameters\ParameterValidateException
     */
    #[Depends('testInitialize')]
    public function testListReceipts()
    {
        $omnireceipt = (self::createOmnireceipt())
                       ->initialize(['auth' => 'ok']);

        $response = $omnireceipt->listReceipts([
            'date_from' => '2016-08-25 00:00:00',
            'date_to' => '2016-08-25 23:59:59',
            'deleted' => false,
        ]);

        $this->assertEquals(200, $response->getCode());

        $list = $response->getList();
        $this->assertInstanceOf(ArrayCollection::class, $list);
        $this->assertEquals(1, $list->count());

        $answer = $response->getData();
        $this->assertIsArray($answer);
        $this->assertCount(1, $answer);
    }

    /**
     * @depends testInitialize
     * @return void
     */
    #[Depends('testInitialize')]
    public function testListReceiptsUseDefaultParameters()
    {
        $omnireceipt = (self::createOmnireceipt())
            ->initialize(['auth' => 'ok']);

        try {
            $omnireceipt->listReceipts();
            $this->fail('Exception didn\'t work');
        } catch (ParameterValidateException $exception) {
            $this->assertIsArray($exception->error);
            $this->assertIsArray($exception->error['parameters']);
            $this->assertArrayHasKey('date_from', $exception->error['parameters']);
            $this->assertArrayHasKey('date_to', $exception->error['parameters']);
            $this->assertArrayNotHasKey('deleted', $exception->error['parameters']);
        }
    }

    /**
     * @depends testListReceipts
     * @return void
     * @throws \Omnireceipt\Common\Exceptions\Parameters\ParameterValidateException
     */
    #[Depends('testListReceipts')]
    public function testListReceiptsNotFound()
    {
        $omnireceipt = self::createOmnireceipt();

        $response = $omnireceipt->listReceipts([
            'date_from' => '2049-08-25 00:00:00',
            'date_to' => '2049-08-25 23:59:59',
            'deleted' => false,
        ]);

        $this->assertEquals(404, $response->getCode());

        $list = $response->getList();
        $this->assertInstanceOf(ArrayCollection::class, $list);
        $this->assertEquals(0, $list->count());

        $answer = $response->getData();
        $this->assertNull($answer);
    }

    /**
     * @depends testInitialize
     * @return void
     * @throws \Omnireceipt\Common\Exceptions\Parameters\ParameterValidateException
     */
    #[Depends('testInitialize')]
    public function testDetailsReceipt()
    {
        $omnireceipt = self::createOmnireceipt();

        $id = 'pending-2da5c87d-0384-50e8-a7f3-8d5646dd9e10';
        $response = $omnireceipt->detailsReceipt($id);
        $this->assertTrue($response->isSuccessful());
        $receipt = $response->getReceipt();
        $this->assertInstanceOf(Receipt::class, $receipt);
        $this->assertEquals($id, $receipt->getId());
        $this->assertInstanceOf(Carbon::class, $receipt->getDate());
        $answer = $response->getData();
        $this->assertIsArray($answer);
        $this->assertEquals($id, $answer['id']);
    }

    /**
     * @depends testDetailsReceipt
     * @return void
     * @throws \Omnireceipt\Common\Exceptions\Parameters\ParameterValidateException
     */
    #[Depends('testDetailsReceipt')]
    public function testDetailsReceiptPending()
    {
        $omnireceipt = self::createOmnireceipt();

        $id = 'pending-2da5c87d-0384-50e8-a7f3-8d5646dd9e10';
        $response = $omnireceipt->detailsReceipt($id);
        $this->assertTrue($response->isSuccessful());

        /** @var \Omnireceipt\Common\Tests\Fixtures\Gateway\Dummy\Entities\Receipt $receipt */
        $receipt = $response->getReceipt();
        $this->assertTrue($receipt->isPending());
        $this->assertFalse($receipt->isSuccessful());
        $this->assertFalse($receipt->isCancelled());
        $this->assertEquals('pending', $receipt->getState());
    }

    /**
     * @depends testDetailsReceipt
     * @return void
     * @throws \Omnireceipt\Common\Exceptions\Parameters\ParameterValidateException
     */
    #[Depends('testDetailsReceipt')]
    public function testDetailsReceiptSuccessful()
    {
        $omnireceipt = self::createOmnireceipt();

        $id = 'succeeded-2da5c87d-0384-50e8-a7f3-8d5646dd9e10';
        $response = $omnireceipt->detailsReceipt($id);
        $this->assertTrue($response->isSuccessful());

        /** @var \Omnireceipt\Common\Tests\Fixtures\Gateway\Dummy\Entities\Receipt $receipt */
        $receipt = $response->getReceipt();
        $this->assertFalse($receipt->isPending());
        $this->assertTrue($receipt->isSuccessful());
        $this->assertFalse($receipt->isCancelled());
        $this->assertEquals('succeeded', $receipt->getState());
    }

    /**
     * @depends testDetailsReceipt
     * @return void
     * @throws \Omnireceipt\Common\Exceptions\Parameters\ParameterValidateException
     */
    #[Depends('testDetailsReceipt')]
    public function testDetailsReceiptCancelled()
    {
        $omnireceipt = self::createOmnireceipt();

        $id = 'canceled-2da5c87d-0384-50e8-a7f3-8d5646dd9e10';
        $response = $omnireceipt->detailsReceipt($id);
        $this->assertTrue($response->isSuccessful());

        /** @var \Omnireceipt\Common\Tests\Fixtures\Gateway\Dummy\Entities\Receipt $receipt */
        $receipt = $response->getReceipt();
        $this->assertFalse($receipt->isPending());
        $this->assertFalse($receipt->isSuccessful());
        $this->assertTrue($receipt->isCancelled());
        $this->assertEquals('canceled', $receipt->getState());
    }

    /**
     * @depends testDetailsReceipt
     * @return void
     * @throws \Omnireceipt\Common\Exceptions\Parameters\ParameterValidateException
     */
    #[Depends('testDetailsReceipt')]
    public function testDetailsReceiptNotFound()
    {
        $omnireceipt = self::createOmnireceipt();

        $id = 'not-found';
        $response = $omnireceipt->detailsReceipt($id);
        $this->assertFalse($response->isSuccessful());
        $this->assertNull($response->getData());
    }

    public static function initializeData(): array
    {
        return [
            'keyAccess' => 'KeyAccess-123',
            'userID' => 'UserID-123',
            'storeUUID' => 'StoreUUID-123',
            'qwe_qwe' => 'StoreUUID-123',
        ];
    }

    protected static function createOmnireceipt(bool $initialize = true): Gateway
    {
        $omnireceipt = Omnireceipt::create(Gateway::class);
        if ($initialize) {
            $omnireceipt->initialize(['auth' => 'ok']);
        }
        return $omnireceipt;
    }
}
