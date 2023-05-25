<?php

define('EPH_UNPACK_NATIVE', 1);
define('EPH_UNPACK_MODIFIED', 2);

/**
 * Class Blowfish
 *
 * @since 1.9.1.0
 */
class Blowfish extends CryptBlowfish {

    /**
     * @param $plaintext
     *
     * @return bool|string
     *
     * @since 1.9.1.0
     * @version 1.8.1.0 Initial version
     */
    public function encrypt($plaintext) {

        if (($length = strlen($plaintext)) >= 1048576) {
            return false;
        }

        $ciphertext = '';
        $paddedtext = $this->maxi_pad($plaintext);
        $strlen = strlen($paddedtext);

        for ($x = 0; $x < $strlen; $x += 8) {
            $piece = substr($paddedtext, $x, 8);
            $cipherPiece = parent::encrypt($piece);
            $encoded = base64_encode($cipherPiece);
            $ciphertext = $ciphertext . $encoded;
        }

        return $ciphertext . sprintf('%06d', $length);
    }

    /**
     * @param string $plaintext
     *
     * @return string
     *
     * @since 1.9.1.0
     * @version 1.8.1.0 Initial version
     */
    public function maxi_pad($plaintext) {

        $strLen = strlen($plaintext);
        $padLen = $strLen % 8;

        for ($x = 0; $x < $padLen; $x++) {
            $plaintext = $plaintext . ' ';
        }

        return $plaintext;
    }

    /**
     * @param $ciphertext
     *
     * @return string
     *
     * @since 1.9.1.0
     * @version 1.8.1.0 Initial version
     */
    public function decrypt($ciphertext) {

        $plainTextLength = (int)(substr($ciphertext, -6));
        $ciphertext = substr($ciphertext, 0, -6);

        $plaintext = '';
        $chunks = explode('=', $ciphertext);
        $endingValue = count($chunks);

        for ($counter = 0; $counter < ($endingValue - 1); $counter++) {
            $chunk = $chunks[$counter] . '=';
            $decoded = base64_decode($chunk);
            $piece = parent::decrypt($decoded);
            $plaintext = $plaintext . $piece;
        }

        return substr($plaintext, 0, $plainTextLength);
    }

}
