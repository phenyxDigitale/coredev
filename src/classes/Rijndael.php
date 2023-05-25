<?php

/**
 * Class Rijndael
 *
 * @since 1.9.1.0
 */
class Rijndael {

    protected $_key;
    protected $_iv;

    /**
     * RijndaelCore constructor.
     *
     * @param string $key
     * @param string $iv
     *
     * @since 1.9.1.0
     * @version 1.8.1.0 Initial version
     */
    public function __construct($key, $iv) {

        $this->_key = $key;
        $this->_iv = base64_decode($iv);
    }

    /**
     * Base64 is not required, but it is be more compact than urlencode
     *
     * @param string $plaintext
     *
     * @return bool|string
     *
     * @since 1.9.1.0
     * @version 1.8.1.0 Initial version
     * @throws Exception
     */
    public function encrypt($plaintext) {

        if (mb_strlen($this->_key, '8bit') !== 32) {
            return false;
        }

        if (function_exists('openssl_encrypt')) {
            $ivsize = openssl_cipher_iv_length('aes-256-cbc');
            $iv = openssl_random_pseudo_bytes($ivsize);
            $ciphertext = openssl_encrypt(
                $plaintext,
                'aes-256-cbc',
                $this->_key,
                OPENSSL_RAW_DATA,
                $iv
            );
        } else {
            $ivsize = mcrypt_get_iv_size(MCRYPT_RIJNDAEL_128, MCRYPT_MODE_CBC);
            try {
                $iv = random_bytes($ivsize);
            } catch (Exception $e) {

                if (function_exists('mcrypt_create_iv')) {
                    $iv = mcrypt_create_iv($ivsize, MCRYPT_RAND);
                } else if (function_exists('openssl_random_pseudo_bytes')) {
                    $iv = openssl_random_pseudo_bytes($ivsize);
                } else {
                    throw new Exception('No secure random number generator found on your system.');
                }

            }

            // Add PKCS7 Padding
            $block = mcrypt_get_block_size(MCRYPT_RIJNDAEL_128, MCRYPT_MODE_CBC);
            $pad = $block - (mb_strlen($plaintext, '8bit') % $block);
            $plaintext .= str_repeat(chr($pad), $pad);

            $ciphertext = mcrypt_encrypt(
                MCRYPT_RIJNDAEL_128,
                $this->_key,
                $plaintext,
                MCRYPT_MODE_CBC,
                $iv
            );
        }

        return $iv . $ciphertext;
    }

    /**
     * @param string $ciphertext
     *
     * @return string|false
     *
     * @since 1.9.1.0
     * @version 1.8.1.0 Initial version
     */
    public function decrypt($ciphertext) {

        if (mb_strlen($this->_key, '8bit') !== 32) {
            return false;
        }

        if (function_exists('openssl_decrypt')) {
            $ivsize = openssl_cipher_iv_length('aes-256-cbc');
            $iv = mb_substr($ciphertext, 0, $ivsize, '8bit');
            $ciphertext = mb_substr($ciphertext, $ivsize, null, '8bit');

            return openssl_decrypt(
                $ciphertext,
                'aes-256-cbc',
                $this->_key,
                OPENSSL_RAW_DATA,
                $iv
            );
        } else {
            $ivsize = mcrypt_get_iv_size(MCRYPT_RIJNDAEL_128, MCRYPT_MODE_CBC);
            $iv = mb_substr($ciphertext, 0, $ivsize, '8bit');
            $ciphertext = mb_substr($ciphertext, $ivsize, null, '8bit');

            $plaintext = mcrypt_decrypt(
                MCRYPT_RIJNDAEL_128,
                $this->_key,
                $ciphertext,
                MCRYPT_MODE_CBC,
                $iv
            );
        }

        $len = mb_strlen($plaintext, '8bit');
        $pad = ord($plaintext[$len - 1]);
        $block = mcrypt_get_block_size(MCRYPT_RIJNDAEL_128, MCRYPT_MODE_CBC);

        if ($pad <= 0 || $pad > $block) {
            // Padding error!
            return false;
        }

        return mb_substr($plaintext, 0, $len - $pad, '8bit');
    }

}
