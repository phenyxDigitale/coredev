<?php

/**
 * Class CacheNoop
 *
 * Dummy cache implementation
 *
 * @since 1.1.1
 */
class CacheNoop extends Cache {

    /**
     * Cache a data
     *
     * @param string $key
     * @param mixed $value
     * @param int $ttl
     *
     * @return bool
     *
     * @since 1.1.1
     * @version 1.1.1 Initial version
     */
    protected function _set($key, $value, $ttl = 0) {

        // no-op implementation
    }

    /**
     * Retrieve a cached data by key
     *
     * @param string $key
     *
     * @return mixed
     *
     * @since 1.1.1
     * @version 1.1.1 Initial version
     */
    protected function _get($key) {

        return null;
    }

    /**
     * Check if a data is cached by key
     *
     * @param string $key
     *
     * @return bool
     *
     * @since 1.1.1
     * @version 1.1.1 Initial version
     */
    protected function _exists($key) {

        return false;
    }

    /**
     * Delete a data from the cache by key
     *
     * @param string $key
     *
     * @return bool
     *
     * @since 1.1.1
     * @version 1.1.1 Initial version
     */
    protected function _delete($key) {

        return false;
    }

    /**
     * Write keys index
     *
     * @since 1.1.1
     * @version 1.1.1 Initial version
     */
    protected function _writeKeys() {

        // no-op implementation
    }

    /**
     * Clean all cached data
     *
     * @return bool
     *
     * @since 1.1.1
     * @version 1.1.1 Initial version
     */
    public function flush() {

        return true;
    }
}
