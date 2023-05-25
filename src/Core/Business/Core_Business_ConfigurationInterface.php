<?php

/**
 * Interface Core_Business_ConfigurationInterface
 *
 * @since 1.9.1.0
 */
// @codingStandardsIgnoreStart
interface Core_Business_ConfigurationInterface {
    // @codingStandardsIgnoreEnd

    /**
     * @param string $key
     *
     * @return mixed
     *
     * @since 1.9.1.0
     * @version 1.8.1.0 Initial version
     */
    public function get($key);
}
