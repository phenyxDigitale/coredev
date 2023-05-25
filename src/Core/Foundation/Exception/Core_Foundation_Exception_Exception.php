<?php

/**
 * Class Core_Foundation_Exception_Exception
 *
 * @since 1.9.1.0
 */
// @codingStandardsIgnoreStart
class Core_Foundation_Exception_Exception extends Exception {

    // @codingStandardsIgnoreEnd

    /**
     * Core_Foundation_Exception_Exception constructor.
     *
     * @param string|null    $message
     * @param int            $code
     * @param Exception|null $previous
     *
     * @since 1.9.1.0
     * @version 1.8.1.0 Initial version
     */
    public function __construct($message = null, $code = 0, Exception $previous = null) {

        parent::__construct($message, $code, $previous);
    }
}
