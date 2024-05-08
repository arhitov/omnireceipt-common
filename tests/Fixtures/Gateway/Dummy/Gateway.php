<?php
/**
 * Core components for the Omnireceipt PHP fiscal receipt processing library
 *
 * @link      https://github.com/omnireceipt/common
 * @package   omnireceipt/common
 * @license   MIT
 * @copyright Copyright (c) 2024, Alexander Arhitov, clgsru@gmail.com
 */

namespace Omnireceipt\Common\Tests\Fixtures\Gateway\Dummy;

use Carbon\Carbon;
use Omnireceipt\Common\AbstractGateway;
use Omnireceipt\Common\Tests\Fixtures\Gateway\Dummy\Entities\Customer;
use Omnireceipt\Common\Tests\Fixtures\Gateway\Dummy\Entities\Receipt;
use Omnireceipt\Common\Tests\Fixtures\Gateway\Dummy\Entities\Seller;
use Omnireceipt\Common\Tests\Fixtures\Gateway\Dummy\Http\CreateReceiptRequest;
use Omnireceipt\Common\Tests\Fixtures\Gateway\Dummy\Http\DetailsReceiptRequest;
use Omnireceipt\Common\Tests\Fixtures\Gateway\Dummy\Http\ListReceiptsRequest;

class Gateway extends AbstractGateway
{
    public static function rules(): array
    {
        return [
            'auth'    => ['required', 'string', 'in:ok'],
        ];
    }

    public function getName(): string
    {
        return 'Dummy';
    }

    //########
    // Seller
    //########

    public function classNameSeller(): string
    {
        return Seller::class;
    }

    public function getDefaultParametersSeller(): array
    {
        $properties = $this->getParameters()['default_properties']['seller'] ?? [];
        $properties['uuid'] ??= 'cb4ed5f7-8b1b-11df-be16-e04a65ecb60f';
        return $properties;
    }

    //##########
    // Customer
    //##########

    public function classNameCustomer(): string
    {
        return Customer::class;
    }

    public function getDefaultParametersCustomer(): array
    {
        $properties = $this->getParameters()['default_properties']['customer'] ?? [];
        $properties['uuid'] ??= '4a65ecb6-8b1b-11df-be16-e0cb4ed5f70f';
        return $properties;
    }

    //#########################
    // Receipt and ReceiptItem
    //#########################

    public function classNameReceipt(): string
    {
        return Receipt::class;
    }

    public function getDefaultParametersReceipt(): array
    {
        $properties = $this->getParameters()['default_properties']['receipt'] ?? [];

        $properties['uuid'] ??= '24b94598-000f-5000-9000-1b68e7b15f3f';
        $properties['date'] ??= Carbon::now()->toString();

        $seller = $this->getSeller();
        $properties['firm_uuid'] ??= $seller->getUuidOrNull();
        $properties['firm_name'] ??= $seller->getNameOrNull();

        $customer = $this->getCustomer();
        if ($customer) {
            $properties['client_uuid'] ??= $customer->getUuidOrNull();
            $properties['client_name'] ??= $customer->getNameOrNull();
        }

        return array_filter($properties);
    }

    public function getDefaultParametersReceiptItem(): array
    {
        $properties = $this->getParameters()['default_properties']['receipt_item'] ?? [];
        $properties['uuid'] ??= '5f3f68e7-24b9-5000-9000-45981bb1000f';
        return $properties;
    }

    //######################
    // HTTP Request Methods
    //######################

    public function classNameCreateReceiptRequest(): string
    {
        return CreateReceiptRequest::class;
    }

    public function classNameListReceiptsRequest(): string
    {
        return ListReceiptsRequest::class;
    }

    public function classNameDetailsReceiptRequest(): string
    {
        return DetailsReceiptRequest::class;
    }
}
