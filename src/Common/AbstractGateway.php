<?php
/**
 * Core components for the Omnireceipt PHP fiscal receipt processing library
 *
 * @link      https://github.com/omnireceipt/common
 * @package   omnireceipt/common
 * @license   MIT
 * @copyright Copyright (c) 2024, Alexander Arhitov, clgsru@gmail.com
 */

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
use Omnireceipt\Common\Supports\ParametersTrait;
use Symfony\Component\HttpFoundation\Request;

/**
 * @method static string classNameReceipt()
 * @method static string classNameCreateReceiptRequest()
 * @method static string classNameListReceiptsRequest()
 * @method static string classNameDetailsReceiptRequest()
 */
abstract class AbstractGateway implements GatewayInterface
{
    use ParametersTrait;

    protected ClientInterface $httpClient;
    protected Request $httpRequest;
    protected ?Seller $seller = null;
    protected ?Customer $customer = null;

    public static function rules(): array
    {
        return [];
    }

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
     * @return array
     */
    public function getDefaultParametersSeller(): array
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

    public function sellerFactory(array $parameters = []): Seller
    {
        $className = $this->classNameSeller();
        return new $className(array_merge(
            $this->getDefaultParametersSeller(),
            $parameters,
        ));
    }

    //##########
    // Customer
    //##########

    /**
     * @return array
     */
    public function getDefaultParametersCustomer(): array
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

    public function customerFactory(array $parameters = []): Customer
    {
        $className = $this->classNameCustomer();
        return new $className(array_merge(
            $this->getDefaultParametersCustomer(),
            $parameters,
        ));
    }

    //#########################
    // Receipt and ReceiptItem
    //#########################

    public static function classNameReceiptItem(): string
    {
        return static::classNameReceipt() . 'Item';
    }

    /**
     * @return array
     */
    public function getDefaultParametersReceipt(): array
    {
        return [];
    }

    /**
     * @return array
     */
    public function getDefaultParametersReceiptItem(): array
    {
        return [];
    }

    public function receiptFactory(array $parameters = [], array ...$parametersItemList): Receipt
    {
        $className = $this->classNameReceipt();
        $classItemName = $this->classNameReceiptItem();

        /** @var Receipt $receipt */
        $receipt = new $className(array_merge(
            $this->getDefaultParametersReceipt(),
            $parameters,
        ));
        foreach ($parametersItemList as $parametersItem) {
            $receipt->addItem(
                new $classItemName(array_merge(
                    $this->getDefaultParametersReceiptItem(),
                    $parametersItem,
                )),
            );
        }

        $receipt->setSeller(
            $this->getSeller(),
        );

        return $receipt;
    }

    /**
     * Creates a Receipt entity from an array.
     *
     * @param array $array
     * @return Receipt
     */
    public function receiptRestore(array $array): Receipt
    {
        $className = $this->classNameReceipt();
        $classItemName = $this->classNameReceiptItem();

        $parameters = $array;
        unset(
            $parameters['@seller'],
            $parameters['@customer'],
            $parameters['@itemList'],
        );
        /** @var Receipt $receipt */
        $receipt = new $className($parameters);

        foreach ($array['@itemList'] as $item) {
            $receipt->addItem(
                new $classItemName($item)
            );
        }

        if (! empty($array['@seller'])) {
            $className = $this->classNameSeller();

            $receipt->setSeller(
                new $className($array['@seller'])
            );
        }

        if (! empty($array['@customer'])) {
            $className = $this->classNameCustomer();

            $receipt->setCustomer(
                new $className($array['@customer'])
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
     * @param array $options
     * @param ?Seller $seller
     * @return AbstractCreateReceiptResponse
     * @throws \Omnireceipt\Common\Exceptions\Parameters\ParameterValidateException
     */
    public function createReceipt(Receipt $receipt, array $options = [], Seller $seller = null): AbstractCreateReceiptResponse
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
     * @throws \Omnireceipt\Common\Exceptions\Parameters\ParameterValidateException
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
     * @throws \Omnireceipt\Common\Exceptions\Parameters\ParameterValidateException
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
     * @throws \Omnireceipt\Common\Exceptions\Parameters\ParameterValidateException
     */
    protected function createRequest(string $class, array $parameters = []): AbstractRequest
    {
        $this->validateOrFail();
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
