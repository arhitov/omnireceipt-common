<?php

namespace Omnireceipt;

use Omnireceipt\Common\Contracts\Http\ClientInterface;
use Omnireceipt\Common\GatewayFactory;

/**
 * Omnireceipt class
 *
 * Provides static access to the gateway factory methods. This is the
 * recommended route for creation and establishment of payment gateway
 * objects via the standard GatewayFactory.
 *
 * Example:
 *
 * <code>
 *   // Create a gateway for the PayPal ExpressGateway
 *   // (routes to GatewayFactory::create)
 *   $gateway = Omnireceipt::create('ExpressGateway');
 *
 *   // Initialise the gateway
 *   $gateway->initialize(...);
 *
 *   // Get the gateway parameters.
 *   $parameters = $gateway->getParameters();
 *
 *   // Do an authorisation transaction on the gateway
 *   if ($gateway->supportsAuthorize()) {
 *       $gateway->authorize(...);
 *   } else {
 *       throw new \Exception('Gateway does not support authorize()');
 *   }
 * </code>
 *
 * For further code examples see the *Omnireceipt-example* repository on github.
 *
 * @method static array  all()
 * @method static array  replace(array $gateways)
 * @method static string register(string $className)
 * @method static \Omnireceipt\Common\AbstractGateway create(string $class, ClientInterface $httpClient = null, \Symfony\Component\HttpFoundation\Request $httpRequest = null)
 *
 * @see \Omnireceipt\Common\GatewayFactory
 */
class Omnireceipt
{
    /**
     * Internal factory storage
     */
    private static GatewayFactory|null $factory = null;

    /**
     * Get the gateway factory
     *
     * Creates a new empty GatewayFactory if none has been set previously.
     *
     * @return GatewayFactory A GatewayFactory instance
     */
    public static function getFactory(): GatewayFactory
    {
        if (is_null(self::$factory)) {
            self::$factory = new GatewayFactory;
        }

        return self::$factory;
    }

    /**
     * Set the gateway factory
     *
     * @param GatewayFactory|null $factory A GatewayFactory instance
     */
    public static function setFactory(GatewayFactory $factory = null): void
    {
        self::$factory = $factory;
    }

    /**
     * Static function call router.
     *
     * All other function calls to the Omnireceipt class are routed to the factory.
     *
     * Example:
     *
     * <code>
     *   // Create a gateway for the ExpressGateway
     *   $gateway = Omnireceipt::create('ExpressGateway');
     * </code>
     *
     * @param string $method     The factory method to invoke.
     * @param array $parameters Parameters passed to the factory method.
     *
     * @return mixed
     * @see GatewayFactory
     */
    public static function __callStatic(string $method, array $parameters)
    {
        $factory = self::getFactory();

        return call_user_func_array(array($factory, $method), $parameters);
    }
}
