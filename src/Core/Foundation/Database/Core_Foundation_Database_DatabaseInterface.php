<?php

/**
 * Interface Core_Foundation_Database_DatabaseInterface
 *
 * @sin
 */
// @codingstandardsIgnoreStart
interface Core_Foundation_Database_DatabaseInterface {
    // @codingStandardsIgnoreEnd

    /**
     * @param string $sqlString
     *
     * @return mixed
     *
     * @since 1.9.1.0
     * @version 1.8.1.0 Initial version
     */
    public function select($sqlString);

    /**
     * @param string $unsafeData
     *
     * @return mixed
     *
     * @since 1.9.1.0
     * @version 1.8.1.0 Initial version
     */
    public function escape($unsafeData);
}
