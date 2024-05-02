<?php

namespace Omnireceipt\Common\Tests;

use Omnireceipt\Common\Entities\Customer;
use Omnireceipt\Common\Entities\Receipt;
use Omnireceipt\Common\Entities\ReceiptItem;
use PHPUnit\Framework\TestCase as UnitTestCase;

abstract class TestCase extends UnitTestCase
{
    public static function makeCustomer(array $parameters = []): Customer
    {
        return new class($parameters) extends Customer
        {

        };
    }

    public static function makeReceipt(array $parameters = []): Receipt
    {
        return new class($parameters) extends Receipt
        {
            public function isPending(): bool
            {
                return false;
            }

            public function isSuccessful(): bool
            {
                return false;
            }

            public function isCancelled(): bool
            {
                return false;
            }
        };
    }

    public static function makeReceiptItem(array $parameters = []): ReceiptItem
    {
        return new class($parameters) extends ReceiptItem{};
    }
}
