<?php

use Ephenyxdigitale\Core\DependencyInjection\ServiceLocator;

/**
 * Class Adapter_ServiceLocator
 */
// @codingStandardsIgnoreStart
class Adapter_ServiceLocator {

    // @codingStandardsIgnoreEnd

    /**
     * Set a service container Instance
     * @var Core_Foundation_IoC_Container
     */
    protected static $serviceContainer;

    /**
     * @param Core_Foundation_IoC_Container $container
     */
    public static function setServiceContainerInstance(Core_Foundation_IoC_Container $container) {

        Tools::displayAsDeprecated();
        ServiceLocator::initialize($container);
    }

    /**
     * Get a service depending on its given $serviceName
     *
     * @param string $serviceName
     *
     * @return mixed|object
     * @throws Adapter_Exception
     *
     * @since 1.9.1.0
     * @version 1.8.1.0 Initial version
     */
    public static function get($serviceName) {

        return ServiceLocator::getInstance()->getByServiceName($serviceName);
    }

}
