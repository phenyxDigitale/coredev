<?php

use Defuse\Crypto\Crypto;
use Defuse\Crypto\Key;

/**
 * Class PhpEncryption
 *
 * @since 1.9.1.0
 */
class PhpEncryption {

    protected $key;

    /**
     * PhpEncryptionCore constructor.
     *
     * @param string $asciiKey
     *
     * @throws \Defuse\Crypto\Exception\BadFormatException
     * @throws \Defuse\Crypto\Exception\EnvironmentIsBrokenException
     * @since 1.9.1.0
     */
    public function __construct($asciiKey) {

        $this->key = Key::loadFromAsciiSafeString($asciiKey);
    }

    /**
     * @param string $plaintext
     *
     * @return string Ciphertext
     * @throws \Defuse\Crypto\Exception\EnvironmentIsBrokenException
     */
    public function encrypt($plaintext) {

        return Crypto::encrypt($plaintext, $this->key);
    }

    /**
     * @param string $ciphertext
     *
     * @return string|null Plaintext
     */
    public function decrypt($ciphertext) {

        try {
            return Crypto::decrypt($ciphertext, $this->key);
        } catch (Exception $exception) {
            return null;
        }
    }
}
