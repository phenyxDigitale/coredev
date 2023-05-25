<?php

/**
 * Class CacheFs
 *
 * @since 1.0.0
 */
class CacheFs extends Cache {

    /**
     * @var int Number of subfolders to dispatch cached filenames
     */
    protected $depth;

    /**
     * CacheFsCore constructor.
     *
     * @throws PhenyxException
     */
    protected function __construct() {

        $this->depth = (int) Db::getInstance(_EPH_USE_SQL_SLAVE_)->getValue('SELECT value FROM ' . _DB_PREFIX_ . 'configuration WHERE name= \'EPH_CACHEFS_DIRECTORY_DEPTH\'', false);

        $keysFilename = $this->getFilename(static::KEYS_NAME);

        if (@filemtime($keysFilename)) {
            $this->keys = json_decode(file_get_contents($keysFilename), true);
        }

    }

    /**
     * @see Cache::_set()
     */
    protected function _set($key, $value, $ttl = 0) {

        $definedUmask = defined('_EPH_UMASK_') ? _EPH_UMASK_ : 0000;

        $previousUmask = @umask($definedUmask);

        $result = @file_put_contents($this->getFilename($key), json_encode($value));

        @umask($previousUmask);

        return $result;
    }

    /**
     * @see Cache::_get()
     */
    protected function _get($key) {

        if ($this->keys[$key] > 0 && $this->keys[$key] < time()) {
            $this->delete($key);

            return false;
        }

        $filename = $this->getFilename($key);

        if (!@filemtime($filename)) {
            unset($this->keys[$key]);
            $this->_writeKeys();

            return false;
        }

        $file = file_get_contents($filename);

        return json_decode($file);
    }

    /**
     * @see Cache::_exists()
     */
    protected function _exists($key) {

        if ($this->keys[$key] > 0 && $this->keys[$key] < time()) {
            $this->delete($key);

            return false;
        }

        return isset($this->keys[$key]) && @filemtime($this->getFilename($key));
    }

    /**
     * @see Cache::_delete()
     */
    protected function _delete($key) {

        $filename = $this->getFilename($key);

        if (!@filemtime($filename)) {
            return true;
        }

        return unlink($filename);
    }

    /**
     * @see Cache::_writeKeys()
     */
    protected function _writeKeys() {

        $definedUmask = defined('_EPH_UMASK_') ? _EPH_UMASK_ : 0000;

        $previousUmask = @umask($definedUmask);

        @file_put_contents($this->getFilename(static::KEYS_NAME), json_encode($this->keys));

        @umask($previousUmask);
    }

    /**
     * @see Cache::flush()
     */
    public function flush() {

        $this->delete('*');

        return true;
    }

    /**
     * Delete cache directory
     */
    public static function deleteCacheDirectory() {

        Tools::deleteDirectory(_EPH_CACHEFS_DIRECTORY_, false);
    }

    /**
     * Create cache directory
     *
     * @param int    $levelDepth
     * @param string $directory
     */
    public static function createCacheDirectories($levelDepth, $directory = false) {

        if (!$directory) {
            $directory = _EPH_CACHEFS_DIRECTORY_;
        }

        $chars = '0123456789abcdef';

        for ($i = 0, $length = strlen($chars); $i < $length; $i++) {
            $newDir = $directory . $chars[$i] . '/';

            if (mkdir($newDir)) {

                if (chmod($newDir, 0777)) {

                    if ($levelDepth - 1 > 0) {
                        CacheFs::createCacheDirectories($levelDepth - 1, $newDir);
                    }

                }

            }

        }

    }

    /**
     * Transform a key into its absolute path
     *
     * @param string $key
     * @return string
     */
    protected function getFilename($key) {

        $key = md5($key);
        $path = _EPH_CACHEFS_DIRECTORY_;

        for ($i = 0; $i < $this->depth; $i++) {
            $path .= $key[$i] . '/';
        }

        if (!is_dir($path)) {
            $definedUmask = defined('_EPH_UMASK_') ? _EPH_UMASK_ : 0000;
            $previousUmask = @umask($definedUmask);
            @mkdir($path, 0777, true);
            @umask($previousUmask);
        }

        return $path . $key;
    }

}
