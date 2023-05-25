<?php


/**
 * Class Core_Business_ContainerBuilder
 *
 * @since 1.9.1.0
 */
// @codingStandardsIgnoreStart
class Core_Business_ContainerBuilder {

    // @codingStandardsIgnoreEnd

    /**
     * Construct PhenyxDigital Core Service container
     *
     * @return Core_Foundation_IoC_Container
     * @throws Core_Foundation_IoC_Exception
     *
     * @since 1.9.1.0
     * @version 1.8.1.0 Initial version
     */
    public function build() {

        $container = new Core_Foundation_IoC_Container();

        $container->bind('Core_Business_ConfigurationInterface', 'Adapter_Configuration', true);
        $container->bind('Core_Foundation_Database_DatabaseInterface', 'Adapter_Database', true);

        return $container;
    }
}
