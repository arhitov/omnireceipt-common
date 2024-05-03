<?php
/**
 * Core components for the Omnireceipt PHP fiscal receipt processing library
 *
 * @link      https://github.com/omnireceipt/common
 * @package   omnireceipt/common
 * @license   MIT
 * @copyright Copyright (c) 2024, Alexander Arhitov, clgsru@gmail.com
 */

namespace Omnireceipt\Common\Contracts;

interface GatewayInterface
{
    /**
     * Get gateway display name
     *
     * @return string
     */
    public function getName(): string;

    /**
     * Get gateway short name
     *
     * @return string
     */
    public function getShortName(): string;

    /**
     * Define gateway parameters, in the following format:
     *
     * @return array
     */
    public function getDefaultParameters(): array;

    /**
     * Initialize gateway with parameters
     *
     * @return $this
     */
    public function initialize(array $parameters = []): static;

    /**
     * Get all gateway parameters
     *
     * @return array
     */
    public function getParameters(): array;

    //########
    // Seller
    //########

    public static function classNameSeller(): string;
    public function getDefaultParametersSeller(): array;

    //##########
    // Customer
    //##########

    public static function classNameCustomer(): string;
    public function getDefaultParametersCustomer(): array;

    //#########################
    // Receipt and ReceiptItem
    //#########################

    public static function classNameReceipt(): string;
    public function getDefaultParametersReceipt(): array;
    public function getDefaultParametersReceiptItem(): array;

    //######################
    // HTTP Request Methods
    //######################

    public static function classNameCreateReceiptRequest(): string;
    public static function classNameListReceiptsRequest(): string;
    public static function classNameDetailsReceiptRequest(): string;
}
