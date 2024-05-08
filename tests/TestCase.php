<?php
/**
 * Core components for the Omnireceipt PHP fiscal receipt processing library
 *
 * @link      https://github.com/omnireceipt/common
 * @package   omnireceipt/common
 * @license   MIT
 * @copyright Copyright (c) 2024, Alexander Arhitov, clgsru@gmail.com
 */

namespace Omnireceipt\Common\Tests;

use Omnireceipt\Common\Entities\Customer;
use Omnireceipt\Common\Entities\Receipt;
use Omnireceipt\Common\Entities\ReceiptItem;
use Omnireceipt\Common\Entities\Seller;
use PHPUnit\Framework\TestCase as UnitTestCase;

abstract class TestCase extends UnitTestCase
{
    public static function makeSeller(array $parameters = []): Seller
    {
        return new class($parameters) extends Seller
        {

        };
    }

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
            public function getId(): string
            {
                return '123';
            }

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
