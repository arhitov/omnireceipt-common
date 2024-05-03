<?php
/**
 * Core components for the Omnireceipt PHP fiscal receipt processing library
 *
 * @link      https://github.com/omnireceipt/common
 * @package   omnireceipt/common
 * @license   MIT
 * @copyright Copyright (c) 2024, Alexander Arhitov, clgsru@gmail.com
 */

namespace Omnireceipt\Common\Supports;

use Omnireceipt\Common\Contracts\GatewayInterface;

/**
 * Helper class
 *
 * This class defines various static utility functions that are in use
 * throughout the Omnireceipt system.
 */
class Helper
{
    /**
     * Convert a string to camelCase. Strings already in camelCase will not be harmed.
     *
     * @param string $str The input string
     * @return string camelCased output string
     */
    public static function camelCase(string $str): string
    {
        $str = self::convertToLowercase($str);
        return preg_replace_callback(
            '/_([a-z])/',
            function ($match) {
                return strtoupper($match[1]);
            },
            $str
        );
    }

    /**
     * Forms the name of the getter method
     *
     * @param string $key
     * @return string
     */
    public static function getGetterMethodName(string $key): string
    {
        return 'get'.ucfirst(static::camelCase($key));
    }

    /**
     * Forms the name of the setter method
     *
     * @param string $key
     * @return string
     */
    public static function getSetterMethodName(string $key): string
    {
        return 'set'.ucfirst(static::camelCase($key));
    }

    /**
     * Convert strings with underscores to be all lowercase before camelCase is preformed.
     *
     * @param string $str The input string
     * @return string The output string
     */
    protected static function convertToLowercase(string $str): string
    {
        $explodedStr = explode('_', $str);
        $lowerCasedStr = [];

        if (count($explodedStr) > 1) {
            foreach ($explodedStr as $value) {
                $lowerCasedStr[] = strtolower($value);
            }
            $str = implode('_', $lowerCasedStr);
        }

        return $str;
    }

    /**
     * Validate a card number according to the Luhn algorithm.
     *
     * @param string $number The card number to validate
     * @return boolean True if the supplied card number is valid
     */
    public static function validateLuhn(string $number): bool
    {
        $str = '';
        foreach (array_reverse(str_split($number)) as $i => $c) {
            $str .= $i % 2 ? $c * 2 : $c;
        }

        return array_sum(str_split($str)) % 10 === 0;
    }

    /**
     * Initialize an object with a given array of parameters
     *
     * Parameters are automatically converted to camelCase. Any parameters which do
     * not match a setter on the target object are ignored.
     *
     * @param object $target The object to set parameters on
     * @param array|null $parameters An array of parameters to set
     */
    public static function initialize(object $target, array $parameters = null): void
    {
        if ($parameters) {
            foreach ($parameters as $key => $value) {
                $method = self::getSetterMethodName($key);
                $target->$method($value);
            }
        }
    }

    /**
     * Resolve a gateway class to a short name.
     *
     * The short name can be used with GatewayFactory as an alias of the gateway class,
     * to create new instances of a gateway.
     */
    public static function getGatewayShortName(string $className): string
    {
        if (str_starts_with($className, '\\')) {
            $className = substr($className, 1);
        }

        return match (true) {
            str_starts_with($className, 'Omnireceipt\\Common\\Tests\\Fixtures\\Gateway')
                => trim(str_replace('\\', '_', substr($className, 41, -7)), '_'),
            str_starts_with($className, 'Omnireceipt\\')
                => trim(str_replace('\\', '_', substr($className, 11, -7)), '_'),
            default
                => '\\'.$className,
        };
    }

    /**
     * Resolve a short gateway name to a full namespaced gateway class.
     *
     * Class names beginning with a namespace marker (\) are left intact.
     * Non-namespaced classes are expected to be in the \Omnireceipt namespace, e.g.:
     *
     *      \Custom\Gateway     => \Custom\Gateway
     *      \Custom_Gateway     => \Custom_Gateway
     *      Stripe              => \Omnireceipt\Stripe\Gateway
     *      PayPal\Express      => \Omnireceipt\PayPal\ExpressGateway
     *      PayPal_Express      => \Omnireceipt\PayPal\ExpressGateway
     *
     * @param string $shortName The short gateway name or the FQCN
     * @return string  The fully namespaced gateway class name
     */
    public static function getGatewayClassName(string $shortName): string
    {
        // If the class starts with \ or Omnireceipt\, assume it's a FQCN
        if (str_starts_with($shortName, '\\') || str_starts_with($shortName, 'Omnireceipt\\')) {
            return $shortName;
        }

        // Check if the class exists and implements the Gateway Interface, if so -> FCQN
        if (is_subclass_of($shortName, GatewayInterface::class, true)) {
            return $shortName;
        }

        // replace underscores with namespace marker, PSR-0 style
        $shortName = str_replace('_', '\\', $shortName);
        if (! str_contains($shortName, '\\')) {
            $shortName .= '\\';
        }

        return '\\Omnireceipt\\' . $shortName . 'Gateway';
    }
}
