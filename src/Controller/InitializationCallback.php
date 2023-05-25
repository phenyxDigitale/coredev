<?php

namespace Ephenyxdigital\Core;

use \Db;

/**
 * Interface InitializationCallback
 *
 * @since 1.3.0
 */
interface InitializationCallback {
    /**
     * Callback method to initialize class
     *
     * @param Db $conn
     * @return void
     */
    public static function initializationCallback(Db $conn);
}
