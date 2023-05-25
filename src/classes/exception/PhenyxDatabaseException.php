<?php

/**
 * Class PhenyxDatabaseException
 *
 * @since 1.9.1.0
 */
class PhenyxDatabaseException extends PhenyxException {

    /**
     * @return mixed
     *
     * @since 1.9.1.0
     * @version 1.8.1.0 Initial version
     */
    public function __toString() {

        return $this->message;
    }
}
