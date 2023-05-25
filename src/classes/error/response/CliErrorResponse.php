<?php

namespace Ephenyxdigital\Core\Error\Response;

use Ephenyxdigital\Core\Error\ErrorDescription;

/**
 * Class JSendErrorResponse
 *
 * @since 1.4.0
 */
class CliErrorResponse extends AbstractErrorPage {

    /**
     * Return content type
     * @return string
     */
    protected function getContentType() {

        return 'text/plain';
    }

    /**
     * @param ErrorDescription $errorDescription
     * @return string
     */
    protected function renderError(ErrorDescription $errorDescription) {

        $message = $errorDescription->getExtendedMessage() . "\n";
        $message .= "Stacktrace:\n" . $errorDescription->getTraceAsString() . "\n";
        $cause = $errorDescription->getCause();

        while ($cause) {
            $message .= "Cause by " . $cause->getExtendedMessage() . "\n";
            $cause = $cause->getCause();
        }

        return $message;
    }

}
