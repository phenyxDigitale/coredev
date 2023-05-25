<?php

namespace Ephenyxdigital\Core\Error\Response;

use Ephenyxdigital\Core\Error\ErrorDescription;

/**
 * Class JSendErrorResponse
 *
 * @since 1.4.0
 */
class JSendErrorResponseCore extends AbstractErrorPage {

    protected $sendErrorMessage;

    /**
     * @param bool $sendErrorMessage
     */
    public function __construct(bool $sendErrorMessage) {

        $this->sendErrorMessage = $sendErrorMessage;
    }

    /**
     * Return content type
     * @return string
     */
    protected function getContentType() {

        return 'application/json';
    }

    /**
     * @param ErrorDescription $errorDescription
     * @return string
     */
    protected function renderError(ErrorDescription $errorDescription) {

        return json_encode([
            'status'  => 'error',
            'message' => $this->getResponseMessage($errorDescription),
        ], JSON_PRETTY_PRINT);
    }

    protected function getResponseMessage(ErrorDescription $errorDescription) {

        if ($this->sendErrorMessage) {
            return $errorDescription->getExtendedMessage();
        } else {
            return \Tools::displayError('Internal server error');
        }

    }

}
