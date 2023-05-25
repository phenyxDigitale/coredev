<?php

/**
 * This class requires the PECL APC extension or PECL APCu extension to be installed
 *
 * @since 1.5.0
 */
class CacheApcu extends Cache {

    /**
     * CacheApcCore constructor.
     *
     * @throws PhenyxException
     */
    public function __construct() {

        if (!extension_loaded('apcu')) {
            throw new PhenyxException('APCu cache has been enabled, but the APCu extension is not available');
        }

    }

    /**
     * Delete one or several data from cache (* joker can be used, but avoid it !)
     *    E.g.: delete('*'); delete('my_prefix_*'); delete('my_key_name');
     *
     * @param string $key Cache key
     *
     * @return bool Whether the key was deleted
     *
     * @since 1.0.0
     * @version 1.0.0 Initial version
     */
    public function delete($key) {

        if ($key == '*') {
            $this->flush();
        } else if (strpos($key, '*') === false) {
            $this->_delete($key);
        } else {
            $pattern = str_replace('\\*', '.*', preg_quote($key));

            $cacheInfo = apcu_cache_info('');

            foreach ($cacheInfo['cache_list'] as $entry) {

                if (isset($entry['key'])) {
                    $key = $entry['key'];
                } else {
                    $key = $entry['info'];
                }

                if (preg_match('#^' . $pattern . '$#', $key)) {
                    $this->_delete($key);
                }

            }

        }

        return true;
    }

    /**
     * @see Cache::_set()
     *
     * @since 1.0.0
     * @version 1.0.0 Initial version
     */
    protected function _set($key, $value, $ttl = 0) {

        return apcu_store($key, $value, $ttl);
    }

    /**
     * @see Cache::_get()
     *
     * @since 1.0.0
     * @version 1.0.0 Initial version
     */
    protected function _get($key) {

        return apcu_fetch($key);
    }

    /**
     * @see Cache::_exists()
     *
     * @since 1.0.0
     * @version 1.0.0 Initial version
     */
    protected function _exists($key) {

        return apcu_exists($key);
    }

    /**
     * @see Cache::_delete()
     *
     * @since 1.0.0
     * @version 1.0.0 Initial version
     */
    protected function _delete($key) {

        return apcu_delete($key);
    }

    /**
     * @see Cache::_writeKeys()
     *
     * @since 1.0.0
     * @version 1.0.0 Initial version
     */
    protected function _writeKeys() {}

    /**
     * @see Cache::flush()
     *
     * @since 1.0.0
     * @version 1.0.0 Initial version
     */
    public function flush() {

        return apcu_clear_cache();
    }

    /**
     * Store data in the cache
     *
     * @param string $key   Cache Key
     * @param mixed  $value Value
     * @param int    $ttl   Time to live in the cache
     *                      0 = unlimited
     *
     * @return bool Whether the data was successfully stored.
     *
     * @since 1.0.0
     * @version 1.0.0 Initial version
     */
    public function set($key, $value, $ttl = 0) {

        return $this->_set($key, $value, $ttl);
    }

    /**
     * Retrieve data from the cache
     *
     * @param string $key Cache key
     *
     * @return mixed Data
     *
     * @since 1.0.0
     * @version 1.0.0 Initial version
     */
    public function get($key) {

        return $this->_get($key);
    }

    /**
     * Check if data has been cached
     *
     * @param string $key Cache key
     *
     * @return bool Whether the data has been cached
     *
     * @since 1.0.0
     * @version 1.0.0 Initial version
     */
    public function exists($key) {

        return $this->_exists($key);
    }

}
