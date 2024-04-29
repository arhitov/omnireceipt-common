<?php

namespace Omnireceipt\Common;

use Omnireceipt\Common\Contracts\GatewayInterface;
use Omnireceipt\Common\Contracts\Http\ClientInterface;
use Omnireceipt\Common\Entities\Customer;
use Omnireceipt\Common\Entities\Receipt;
use Omnireceipt\Common\Entities\Seller;
use Omnireceipt\Common\Http\Client;
use Omnireceipt\Common\Http\Request\AbstractCreateReceiptRequest;
use Omnireceipt\Common\Http\Request\AbstractRequest;
use Omnireceipt\Common\Http\Response\AbstractCreateReceiptResponse;
use Omnireceipt\Common\Http\Response\AbstractDetailsReceiptResponse;
use Omnireceipt\Common\Http\Response\AbstractListReceiptsResponse;
use Omnireceipt\Common\Supports\Helper;
use Omnireceipt\Common\Supports\ParametersHttpTrait;
use Omnireceipt\Common\Supports\PropertiesTrait;
use Symfony\Component\HttpFoundation\Request;

abstract class AbstractGateway implements GatewayInterface
{
    use ParametersHttpTrait {
        setParameter as traitSetParameter;
        getParameter as traitGetParameter;
    }
    use PropertiesTrait;

    protected ClientInterface $httpClient;
    protected Request $httpRequest;
    protected ?Seller $seller = null;
    protected ?Customer $customer = null;

    /**
     * Create a new gateway instance
     *
     * @param ClientInterface|null $httpClient A HTTP client to make API calls with
     * @param Request|null $httpRequest A Symfony HTTP request object
     */
    public function __construct(ClientInterface $httpClient = null, Request $httpRequest = null)
    {
        $this->httpClient = $httpClient ?: $this->getDefaultHttpClient();
        $this->httpRequest = $httpRequest ?: $this->getDefaultHttpRequest();
        $this->initialize();
    }

    /**
     * Get the short name of the Gateway
     *
     * @return string
     */
    public function getShortName(): string
    {
        return Helper::getGatewayShortName(get_class($this));
    }

    /**
     * @return array
     */
    public function getDefaultParameters(): array
    {
        return [];
    }

    //########
    // Seller
    //########

    /**
     * @return string
     */
    public static function classNameSeller(): string
    {
        return Seller::class;
    }

    /**
     * @return array
     */
    public function getDefaultPropertiesSeller(): array
    {
        return [];
    }

    public function getSeller(): Seller
    {
        return $this->seller ?? $this->sellerFactory();
    }

    public function setSeller(Seller $seller): self
    {
        $this->seller = $seller;
        return $this;
    }

    public function sellerFactory(array $properties = []): Seller
    {
        $className = $this->classNameSeller();
        return new $className(array_merge(
            $this->getDefaultPropertiesSeller(),
            $properties,
        ));
    }

    //##########
    // Customer
    //##########

    /**
     * @return string
     */
    public static function classNameCustomer(): string
    {
        return Customer::class;
    }

    /**
     * @return array
     */
    public function getDefaultPropertiesCustomer(): array
    {
        return [];
    }

    public function getCustomer(): ?Customer
    {
        return $this->customer;
    }

    public function setCustomer(Customer $customer): self
    {
        $this->customer = $customer;
        return $this;
    }

    public function customerFactory(array $properties = []): Customer
    {
        $className = $this->classNameCustomer();
        return new $className(array_merge(
            $this->getDefaultPropertiesCustomer(),
            $properties,
        ));
    }

    //#########################
    // Receipt and ReceiptItem
    //#########################

    /**
     * @return string
     */
    public static function classNameReceipt(): string
    {
        return Receipt::class;
    }

    /**
     * @return array
     */
    public function getDefaultPropertiesReceipt(): array
    {
        return [];
    }

    /**
     * @return array
     */
    public function getDefaultPropertiesReceiptItem(): array
    {
        return [];
    }

    public function receiptFactory(array $properties = [], array ...$propertiesItemList): Receipt
    {
        $className = $this->classNameReceipt();
        $classItemName = $className . 'Item';

        /** @var Receipt $receipt */
        $receipt = new $className(array_merge(
            $this->getDefaultPropertiesReceipt(),
            $properties,
        ));
        foreach ($propertiesItemList as $itemProperties) {
            $receipt->addItem(
                new $classItemName(array_merge(
                    $this->getDefaultPropertiesReceiptItem(),
                    $itemProperties,
                )),
            );
        }
        return $receipt;
    }

    //######################
    // HTTP Request Methods
    //######################

    /**
     * Creating a receipt
     *
     * @param Receipt $receipt
     * @param ?Seller $seller
     * @param array $options
     * @return AbstractCreateReceiptResponse
     * @throws \Omnireceipt\Common\Exceptions\Property\PropertyValidateException
     */
    public function createReceipt(Receipt $receipt, Seller $seller = null, array $options = []): AbstractCreateReceiptResponse
    {
        /** @var AbstractCreateReceiptRequest $request */
        $request = $this->createRequest(static::classNameCreateReceiptRequest(), $options);

        $receipt->setSeller($seller ?? $this->getSeller());
        $customer = $this->getCustomer();
        if ($customer) {
            $receipt->setCustomer($customer);
        }

        $request->setReceipt($receipt);

        /** @var AbstractCreateReceiptResponse $response */
        $response = $request->send();
        return $response;
    }

    /**
     * Get a list of receipts
     *
     * @param array $options
     * @return AbstractListReceiptsResponse
     * @throws \Omnireceipt\Common\Exceptions\Property\PropertyValidateException
     */
    public function listReceipts(array $options = []): AbstractListReceiptsResponse
    {
        /** @var AbstractListReceiptsResponse $response */
        $response = $this->createRequest(static::classNameListReceiptsRequest(), $options)->send();
        return $response;
    }

    /**
     * Get check details
     *
     * @param string $id
     * @return AbstractDetailsReceiptResponse
     * @throws \Omnireceipt\Common\Exceptions\Property\PropertyValidateException
     */
    public function detailsReceipt(string $id): AbstractDetailsReceiptResponse
    {
        /** @var AbstractDetailsReceiptResponse $response */
        $response = $this->createRequest(static::classNameDetailsReceiptRequest(), ['id' => $id])->send();
        return $response;
    }

    /**
     * Create and initialize a request object
     *
     * @param string $class The request class name
     * @param array $parameters
     * @return AbstractRequest
     */
    protected function createRequest(string $class, array $parameters = []): AbstractRequest
    {
        return (new $class($this->httpClient, $this->httpRequest))
            ->initialize(array_replace($this->getParameters(), $parameters));
    }

    /**
     * Get the global default HTTP client.
     *
     * @return ClientInterface
     */
    protected function getDefaultHttpClient(): ClientInterface
    {
        return new Client();
    }

    /**
     * Get the global default HTTP request.
     *
     * @return Request
     */
    protected function getDefaultHttpRequest(): Request
    {
        return Request::createFromGlobals();
    }
}
