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

    public static function getClassEntitiesNameCustomer(): string
    {
        return Customer::class;
    }

    public static function getClassEntitiesNameSeller(): string
    {
        return Seller::class;
    }

    public static function getClassEntitiesNameReceipt(): string
    {
        return Receipt::class;
    }

    abstract public static function getClassRequestNameCreateReceipt(): string;
    abstract public static function getClassRequestNameListReceipts(): string;
    abstract public static function getClassRequestNameDetailsReceipt(): string;

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
        $className = $this->getClassEntitiesNameSeller();
        if (method_exists($this, 'sellerDefaultProperties')) {
            $properties = array_merge(
                $this->sellerDefaultProperties(),
                $properties,
            );
        }
        return new $className($properties);
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
        $className = $this->getClassEntitiesNameCustomer();
        if (method_exists($this, 'customerDefaultProperties')) {
            $properties = array_merge(
                $this->customerDefaultProperties(),
                $properties,
            );
        }
        return new $className($properties);
    }

    public function receiptFactory(array $properties = [], array ...$propertiesItemList): Receipt
    {
        $className = $this->getClassEntitiesNameReceipt();
        $classItemName = $className . 'Item';

        if (method_exists($this, 'receiptDefaultProperties')) {
            $properties = array_merge(
                $this->receiptDefaultProperties(),
                $properties,
            );
        }

        /** @var Receipt $receipt */
        $receipt = new $className($properties);
        foreach ($propertiesItemList as $itemProperties) {
            if (method_exists($this, 'receiptItemDefaultProperties')) {
                $itemProperties = array_merge(
                    $this->receiptItemDefaultProperties(),
                    $itemProperties,
                );
            }
            $receipt->addItem(
                new $classItemName($itemProperties),
            );
        }
        return $receipt;
    }

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
        $request = $this->createRequest(static::getClassRequestNameCreateReceipt(), $options);
        $request->setReceipt($receipt);
        if ($seller) {
            $request->setSeller($seller);
        }
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
        $response = $this->createRequest(static::getClassRequestNameListReceipts(), $options)->send();
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
        $response = $this->createRequest(static::getClassRequestNameDetailsReceipt(), ['id' => $id])->send();
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
