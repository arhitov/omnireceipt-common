<?php

namespace Omnireceipt\Common\Tests\Unit;

use Carbon\Carbon;
use Doctrine\Common\Collections\ArrayCollection;
use Omnireceipt\Common\AbstractGateway;
use Omnireceipt\Common\Entities\Customer;
use Omnireceipt\Common\Entities\Receipt;
use Omnireceipt\Common\Entities\Seller;
use Omnireceipt\Common\Exceptions\RuntimeException;
use Omnireceipt\Common\Supports\Helper;
use Omnireceipt\Common\Tests\factories\ReceiptFactory;
use Omnireceipt\Common\Tests\factories\ReceiptItemFactory;
use Omnireceipt\Common\Tests\Fixtures\Gateway\Dummy\Gateway;
use Omnireceipt\Common\Tests\TestCase;
use Omnireceipt\Omnireceipt;

class GatewayTest extends TestCase
{
    public function testBase()
    {
        $omnireceipt = self::createOmnireceipt();

        $this->assertInstanceOf(AbstractGateway::class, $omnireceipt);

        $this->assertEquals('Dummy', $omnireceipt->getName());
        $this->assertEquals('Dummy', $omnireceipt->getShortName());
        $this->assertIsArray($omnireceipt->getDefaultParameters());
        $this->assertEmpty($omnireceipt->getDefaultParameters());
        $this->assertIsArray($omnireceipt->getParameters());
        $this->assertEmpty($omnireceipt->getParameters());

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
                'customer_name' => $customerName,
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
        $this->assertEquals($customerName, $receipt->getCustomerName());
        $this->assertTrue($receipt->validate());
        $this->assertEquals(3.66, $receipt->getAmount());
        $this->assertCount(2, $receipt->getItemList());
    }

    /**
     * @depends testBase
     * @return void
     */
    #[\PHPUnit\Framework\Attributes\Depends('testBase')]
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
    #[\PHPUnit\Framework\Attributes\Depends('testBase')]
    public function testInitialize()
    {
        $omnireceipt = self::createOmnireceipt();

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
     * @throws \Omnireceipt\Common\Exceptions\Property\PropertyValidateException
     */
    #[\PHPUnit\Framework\Attributes\Depends('testInitialize')]
    public function testCreateReceipt()
    {
        $omnireceipt = self::createOmnireceipt();

        $receipt = ReceiptFactory::create();
        $receipt->setUuid('0ecab77f-7062-4a5f-aa20-35213db1397c');
        $receipt->setDocNum('ТД00-000001');

        $customer = new Customer([
            'id'    => '4a65ecb6-8b1b-11df-be16-e0cb4ed5f70f',
            'name'  => 'Ivanov Ivan Ivanovich',
            'phone' => '+79000000000',
            'email' => 'email@email.ru',
        ]);
        $receipt->setCustomer($customer);

        $receiptItem = ReceiptItemFactory::create();
        $receiptItem->setVatRate(0);
        $receiptItem->setVatSum(0);
        $receipt->addItem($receiptItem);

        $this->assertTrue($receipt->validate());

        $seller = new Seller([
            'address' => 'www.example.com',
        ]);

        $response = $omnireceipt->createReceipt(
            $receipt,
            $seller,
            options: [
                'qwe' => 'qwe',
            ],
        );

        $this->assertTrue($response->isSuccessful());
        $this->assertNull($response->getData());
        $this->assertEquals(200, $response->getCode());
    }

    /**
     * @depends testInitialize
     * @return void
     * @throws \Omnireceipt\Common\Exceptions\Property\PropertyValidateException
     */
    #[\PHPUnit\Framework\Attributes\Depends('testInitialize')]
    public function testCreateReceiptTwo()
    {
        $omnireceipt = self::createOmnireceipt();

        $receipt = ReceiptFactory::create();
        $receipt->setUuid('0ecab77f-7062-4a5f-aa20-35213db1397c');
        $receipt->setDocNum('ТД00-000001');

        $customer = $omnireceipt->customerFactory([
            'id'    => '4a65ecb6-8b1b-11df-be16-e0cb4ed5f70f',
            'name'  => 'Ivanov Ivan Ivanovich',
            'phone' => '+79000000000',
            'email' => 'email@email.ru',
        ]);
        $receipt->setCustomer($customer);

        $receiptItem = ReceiptItemFactory::create();
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
     * @throws \Omnireceipt\Common\Exceptions\Property\PropertyValidateException
     */
    #[\PHPUnit\Framework\Attributes\Depends('testInitialize')]
    public function testListReceipts()
    {
        $omnireceipt = self::createOmnireceipt();

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
     * @depends testListReceipts
     * @return void
     * @throws \Omnireceipt\Common\Exceptions\Property\PropertyValidateException
     */
    #[\PHPUnit\Framework\Attributes\Depends('testListReceipts')]
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
     * @throws \Omnireceipt\Common\Exceptions\Property\PropertyValidateException
     */
    #[\PHPUnit\Framework\Attributes\Depends('testInitialize')]
    public function testDetailsReceipt()
    {
        $omnireceipt = self::createOmnireceipt();

        $id = 'pending-2da5c87d-0384-50e8-a7f3-8d5646dd9e10';
        $response = $omnireceipt->detailsReceipt($id);
        $this->assertNotEmpty($response->getState());
        $this->assertInstanceOf(Carbon::class, $response->getDate());
        $answer = $response->getData();
        $this->assertIsArray($answer);
        $this->assertEquals($id, $answer['id']);
    }

    /**
     * @depends testDetailsReceipt
     * @return void
     * @throws \Omnireceipt\Common\Exceptions\Property\PropertyValidateException
     */
    #[\PHPUnit\Framework\Attributes\Depends('testDetailsReceipt')]
    public function testDetailsReceiptPending()
    {
        $omnireceipt = self::createOmnireceipt();

        $id = 'pending-2da5c87d-0384-50e8-a7f3-8d5646dd9e10';
        $response = $omnireceipt->detailsReceipt($id);

        $this->assertTrue($response->isPending());
        $this->assertFalse($response->isSuccessful());
        $this->assertFalse($response->isCancelled());
        $this->assertEquals('pending', $response->getState());
    }

    /**
     * @depends testDetailsReceipt
     * @return void
     * @throws \Omnireceipt\Common\Exceptions\Property\PropertyValidateException
     */
    #[\PHPUnit\Framework\Attributes\Depends('testDetailsReceipt')]
    public function testDetailsReceiptSuccessful()
    {
        $omnireceipt = self::createOmnireceipt();

        $id = 'succeeded-2da5c87d-0384-50e8-a7f3-8d5646dd9e10';
        $response = $omnireceipt->detailsReceipt($id);

        $this->assertFalse($response->isPending());
        $this->assertTrue($response->isSuccessful());
        $this->assertFalse($response->isCancelled());
        $this->assertEquals('succeeded', $response->getState());
    }

    /**
     * @depends testDetailsReceipt
     * @return void
     * @throws \Omnireceipt\Common\Exceptions\Property\PropertyValidateException
     */
    #[\PHPUnit\Framework\Attributes\Depends('testDetailsReceipt')]
    public function testDetailsReceiptCancelled()
    {
        $omnireceipt = self::createOmnireceipt();

        $id = 'canceled-2da5c87d-0384-50e8-a7f3-8d5646dd9e10';
        $response = $omnireceipt->detailsReceipt($id);

        $this->assertFalse($response->isPending());
        $this->assertFalse($response->isSuccessful());
        $this->assertTrue($response->isCancelled());
        $this->assertEquals('canceled', $response->getState());
    }

    /**
     * @depends testDetailsReceipt
     * @return void
     * @throws \Omnireceipt\Common\Exceptions\Property\PropertyValidateException
     */
    #[\PHPUnit\Framework\Attributes\Depends('testDetailsReceipt')]
    public function testDetailsReceiptNotFound()
    {
        $omnireceipt = self::createOmnireceipt();

        $id = 'not-found';
        $response = $omnireceipt->detailsReceipt($id);

        $this->assertFalse($response->isPending());
        $this->assertFalse($response->isSuccessful());
        $this->assertFalse($response->isCancelled());
        $this->assertNull($response->getState());
        $this->assertNull($response->getDate());
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

    protected static function createOmnireceipt(): Gateway
    {
        return Omnireceipt::create(Gateway::class);
    }
}
