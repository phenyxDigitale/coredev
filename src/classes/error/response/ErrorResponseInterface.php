<?php

namespace Ephenyxdigital\Core\Error\Response;

use Ephenyxdigital\Core\Error\ErrorDescription;

/**
 * Interface ErrorResponseInterface
 *
 * @since 1.4.0
 */
interface ErrorResponseInterface {

    /**
     * Displays Error Page for given exception.
     *
     * Will never return, it will exit script
     *
     * @param ErrorDescription $errorDescription
     * @return void
     */
    public function sendResponse(ErrorDescription $errorDescription);
}
