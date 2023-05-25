<?php

/**
 * Class Adapter_Configuration
 *
 * @since 1.9.1.0
 */
// @codingStandardsIgnoreStart
class Adapter_Configuration implements Core_Business_ConfigurationInterface {

    // @codingStandardsIgnoreEnd

    /**
     * Returns constant defined by given $key if exists or check directly in ephenyx' configuration
     *
     * @param string $key
     *
     * @return mixed
     *
     * @since 1.9.1.0
     * @version 1.8.1.0 Initial version
     */
    public function get($key) {

        if (defined($key)) {
            return constant($key);
        } else {
            return Configuration::get($key);
        }

    }

}
