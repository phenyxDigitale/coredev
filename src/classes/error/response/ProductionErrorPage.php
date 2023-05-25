<?php

namespace Ephenyxdigital\Core\Error\Response;

use Ephenyxdigital\Core\Error\ErrorDescription;

/**
 * Class DebugErrorPageCore
 *
 * @since 1.4.0
 */
class ProductionErrorPage extends AbstractErrorPage {

    /**
     * Return content type
     * @return string
     */
    protected function getContentType() {

        return 'text/html';
    }

    /**
     * @param ErrorDescription $errorDescription
     * @return string
     * @throws \PhenyxException
     */
    protected function renderError(ErrorDescription $errorDescription) {

        return static::displayErrorTemplate(
            _PS_ROOT_DIR_ . '/error500.phtml',
            [
                'shopEmail' => \Configuration::get('PS_SHOP_EMAIL'),
                'encrypted' => $errorDescription->encrypt(),
            ]
        );
    }
}
